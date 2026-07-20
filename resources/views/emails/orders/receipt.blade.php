@component('mail::layout')
# Order Confirmation

**Order Number:** #{{ $order->id }}  
**Delivery Address:** {{ $order->shipping_address }}

Your payment has been successfully processed and your order is now being prepared for shipping.

@component('mail::table')
| Item | Qty | Price |
| :--- | :---: | :--- |
@foreach($order->items as $item)
| {{ $item->product->name }} | {{ $item->quantity }} | ₦{{ number_format($item->price) }} |
@endforeach
@endcomponent

**Subtotal:** ₦{{ number_format($order->total_amount + $order->discount_amount) }}  
**Discount Applied:** -₦{{ number_format($order->discount_amount) }}  
**Total Paid:** ₦{{ number_format($order->total_amount) }}

@component('mail::button', ['url' => config('app.url') . '/profile'])
View Order History
@endcomponent

© {{ date('Y') }} World Star. All rights reserved.
@endcomponent