@component('mail::layout')
# New Order Received

**Customer:** {{ $order->user->name }} ({{ $order->user->email }})  
**Order Number:** #{{ $order->id }}  
**Total Amount:** ₦{{ number_format($order->total_amount) }}

**Items to Fulfill:**
@foreach($order->items as $item)
* {{ $item->product->name }} (Quantity: {{ $item->quantity }})
@endforeach

@component('mail::button', ['url' => config('app.url') . '/admin/orders/view/' . $order->id])
View Order Details
@endcomponent

World Star Store // Order Notification
@endcomponent