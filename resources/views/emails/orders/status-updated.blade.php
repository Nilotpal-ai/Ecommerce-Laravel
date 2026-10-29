<x-mail::message>
# Order Status Update

Hi {{ $order->user->name ?? 'Customer' }},

The status of your order **#{{ $order->id }}** has been updated to:

<x-mail::panel>
**{{ ucfirst($status) }}**
</x-mail::panel>

@if($status === 'completed')
Your package has been delivered or marked as completed. We hope you enjoy your purchase!
@elseif($status === 'cancelled')
Your order has been cancelled. Any pre-authorized charges or payments will be refunded per policy.
@elseif($status === 'processing')
Your order is currently being packed and prepared for dispatch.
@endif

<x-mail::button :url="route('shop.customer.orders.show', $order->id)">
Track Your Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>