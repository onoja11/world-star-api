@component('mail::layout')
# [!] ACQUISITION_RECEIPT

**Order_Ref:** #{{ $order->id }}  
**Logistics_Target:** {{ $order->shipping_address }}

Your order has been validated and synced with our fulfillment archive.

@component('mail::table')
| Item | Quant | Price |
| :--- | :---: | :--- |
@foreach($order->items as $item)
| {{ $item->product->name }} | {{ $item->quantity }} | ₦{{ number_format($item->price) }} |
@endforeach
@endcomponent

**Subtotal:** ₦{{ number_format($order->total_amount + $order->discount_amount) }}  
**Reduction:** -₦{{ number_format($order->discount_amount) }}  
**Total_Settled:** ₦{{ number_format($order->total_amount) }}

@component('mail::button', ['url' => config('app.url') . '/profile'])
VIEW_ACQUISITION_LOGS
@endcomponent

© {{ date('Y') }} WORLD STAR. EST_MMXXVI
@endcomponent