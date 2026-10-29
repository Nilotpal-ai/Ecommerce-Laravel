<x-mail::message>
# Thank You for Your Order!

Hi {{ $order->shipping_name ?? $order->user->name ?? 'Customer' }},

Your order **#{{ $order->order_number ?? $order->id }}** has been received and is being processed.

<x-mail::table>
| Product | Qty | Price | Subtotal |
| :--- | :---: | :---: | :---: |
@foreach($order->items as $item)
| {{ $item->product_name ?? $item->product->name ?? 'Item' }} | {{ $item->quantity }} | ₹{{ number_format($item->unit_price, 2) }} | ₹{{ number_format($item->subtotal ?? ($item->unit_price * $item->quantity), 2) }} |
@endforeach
| **Total** | | | **₹{{ number_format($order->total_amount, 2) }}** |
</x-mail::table>

**Payment Method:** {{ strtoupper($order->payment_method ?? 'Online') }} ({{ ucfirst($order->payment_status ?? 'Paid') }})  
**Shipping Address:**  
{{ $order->shipping_name }}  
{{ $order->shipping_address_line1 }}{{ $order->shipping_address_line2 ? ', ' . $order->shipping_address_line2 : '' }}  
{{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_postal_code }}  
{{ $order->shipping_country }}

<x-mail::button :url="route('customer.orders.show', $order->id)">
View Order Details
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>