<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coupon = Coupon::all();
        return response()->json($coupon);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'expiry_date' => 'required|date',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric',
            'usage_limit' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);
        $coupon = Coupon::create($request->all());
        return response()->json($coupon, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        return response()->json($coupon);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'sometimes|required|unique:coupons,code,' . $coupon->id,
            'expiry_date' => 'sometimes|required|date',
            'type' => 'sometimes|required|in:percentage,fixed',
            'value' => 'sometimes|required|numeric',
            'usage_limit' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);
        $coupon->update($request->all());
        return response()->json($coupon);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return response()->json(null, 204);
    }

    /**
     * Validate a coupon code and calculate discount.
     */
    public function validateCoupon(Request $request)
    {
        // 1. Validate the incoming request
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        // 2. Find the coupon
        $coupon = Coupon::where('code', $request->code)->first();

        // 3. Check if coupon exists
        if (!$coupon) {
            return response()->json(['message' => 'Invalid coupon code.'], 404);
        }

        // 4. Check if expired
        // Ensure your Coupon model casts 'expiry_date' to datetime, or use Carbon
        if (\Carbon\Carbon::parse($coupon->expiry_date)->isPast()) {
            return response()->json(['message' => 'This coupon has expired.'], 400);
        }

        // 5. Check usage limit (if applicable)
        // Assuming 'usage_limit' decreases when used, or is 0 when finished
        if (!is_null($coupon->usage_limit) && $coupon->usage_limit <= 0) {
            return response()->json(['message' => 'This coupon is no longer valid.'], 400);
        }

        // 6. Calculate Discount Amount
        $subtotal = $request->subtotal;
        $discountAmount = 0;

        if ($coupon->type === 'percentage') {
            // Example: 10% of 5000 = 500
            $discountAmount = ($coupon->value / 100) * $subtotal;
        } elseif ($coupon->type === 'fixed') {
            // Example: Flat 1000 naira off
            $discountAmount = $coupon->value;
        }

        // Ensure discount doesn't exceed the subtotal (cannot have negative total)
        if ($discountAmount > $subtotal) {
            $discountAmount = $subtotal;
        }

        // 7. Return success response
        return response()->json([
            'message' => 'Coupon applied successfully!',
            'discount_amount' => round($discountAmount, 2),
            'code' => $coupon->code,
            'type' => $coupon->type
        ], 200);
    }
}
