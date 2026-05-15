@component('mail::layout')
# [!] NEW_ORDER_INBOUND

**Entity:** {{ $order->user->name }} ({{ $order->user->email }})  
**Order_ID:** #{{ $order->id }}  
**Revenue:** ₦{{ number_format($order->total_amount) }}

**Items_to_Process:**
@foreach($order->items as $item)
* {{ $item->product->name }} [x{{ $item->quantity }}]
@endforeach

@component('mail::button', ['url' => config('app.url') . '/admin/orders/view/' . $order->id])
INITIALIZE_FULFILLMENT
@endcomponent

SYSTEM_MMXXVI // INBOUND_LOG_v2.5
@endcomponent