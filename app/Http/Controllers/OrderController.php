<?php

namespace App\Http\Controllers;

use App\Mail\AdminOrderAlert;
use App\Mail\UserOrderReceipt;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Coupon; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product')->where('user_id', auth()->id())->latest()->get();
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // 1. Get All Inputs
        $cart = $request->input('cart');
        $couponCode = $request->input('coupon_code');
        $paymentReference = $request->input('payment_reference'); 
        $userInfo = $request->input('user_info'); 

        // 2. Validations
        if (!$cart || count($cart) == 0) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }
        if (!$paymentReference) {
            return response()->json(['message' => 'No payment reference provided'], 400);
        }
        // Validate User Info
        if (!$userInfo || empty($userInfo['address']) || empty($userInfo['full_name'])) {
             return response()->json(['message' => 'Shipping address is incomplete'], 400);
        }

        DB::beginTransaction();

        try {
            // 3. Calculate Totals
            $subtotal = 0;
            $itemsToCreate = [];

            foreach ($cart as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json(['message' => "Not enough stock for {$product->name}"], 400);
                }

                $lineTotal = $product->price * $item['quantity'];
                $subtotal += $lineTotal;

                $itemsToCreate[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->price
                ];
            }

            // 4. Coupon Logic
            $discountAmount = 0;
            if ($couponCode) {
                $coupon = Coupon::where('code', $couponCode)->first();
                if ($coupon && !$coupon->isExpired()) { 
                    if ($coupon->type === 'percentage') {
                        $discountAmount = ($coupon->value / 100) * $subtotal;
                    } else {
                        $discountAmount = $coupon->value;
                    }
                    $discountAmount = min($discountAmount, $subtotal);
                }
            }
            $expectedTotal = $subtotal - $discountAmount;

            // 5. Verify Paystack
            $secretKey = config('services.paystack.secret') ?? env('PAYSTACK_SECRET_KEY');

            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $secretKey,
                    'Cache-Control' => 'no-cache',
                ])
                ->get("https://api.paystack.co/transaction/verify/" . $paymentReference);
            
            $result = $response->json();

            if (!$result['status'] || $result['data']['status'] !== 'success') {
                DB::rollBack();
                return response()->json(['message' => 'Payment verification failed'], 400);
            }

            $paidAmount = $result['data']['amount'] / 100;

            if ($paidAmount < $expectedTotal) {
                 DB::rollBack();
                 return response()->json(['message' => 'Payment mismatch. Expected: ' . $expectedTotal . ', Paid: ' . $paidAmount], 400);
            }

            // 6. CREATE ORDER
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $expectedTotal,
                'discount_amount' => $discountAmount,
                'coupon_code' => $couponCode,
                'payment_reference' => $paymentReference,
                'payment_method' => 'paystack',
                'status' => 'pending',
                
                'shipping_name'    => $userInfo['full_name'],
                'shipping_address' => $userInfo['address'],
                'shipping_phone'   => $userInfo['phone'],
                'shipping_city'    => $userInfo['city'] ?? null,
                'shipping_state'   => $userInfo['state'] ?? null,
                'shipping_email'   => $userInfo['email'] ?? null,
            ]);

            // 7. Save Items
            foreach ($itemsToCreate as $data) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $data['product']->id,
                    'quantity' => $data['quantity'],
                    'price' => $data['price']
                ]);
                $data['product']->decrement('stock', $data['quantity']);
            }

            if ($couponCode) {
                 Coupon::where('code', $couponCode)->decrement('usage_limit');
            }

            // 8. Record Transaction
            Transaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'wallet_id' => null, 
                'reference' => $paymentReference,
                'amount' => $expectedTotal,
                'status' => 'success',
                'description' => 'Paystack Payment' 
            ]);

            $user->review_status = 'active';
            $user->save();  

            DB::commit();

            // Mail::to($user->email)->send(new UserOrderReceipt($order));

            // [!] EDITED: Dispatch Alert to ONLY ONE primary admin instead of a full collection loop
            $primaryAdmin = User::where('role', 'admin')->first();
            if ($primaryAdmin) {
                Mail::to($primaryAdmin->email)->send(new AdminOrderAlert($order));
            }

            return response()->json([
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Order Error: " . $e->getMessage());
            return response()->json(['message' => 'Order creation failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        $order = Order::with('items.product.category')->where('id', $id)->firstOrFail();
        return response()->json($order);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required',
        ]);
        $order = Order::findOrFail($id);
        $order->update([
            'status' => $request->status,
        ]);
        return response()->json("updated");
    }

    public function destroy(string $id)
    {
        $order = Order::firstOrFail($id);
        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Only pending orders can be cancelled'], 400);
        }
        $order->delete();
        return response()->json(['message' => 'Order cancelled successfully']);
    }

    public function adminIndex()
    {
        $orders = Order::with('items.product', 'user')->get();
        return response()->json($orders);
    }

    public function cancel($id)
    {
        $order = Order::where('user_id', auth()->id())->findOrFail($id);

        if ($order->status === 'delivered') {
            return response()->json(['error' => 'Delivered orders cannot be cancelled'], 400);
        }

        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'message' => 'Order cancelled successfully',
            'order' => $order
        ]);
    }
}