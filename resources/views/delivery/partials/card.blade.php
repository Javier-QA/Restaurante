<div class="delivery-order-card"
     onclick="window.location='{{ route('delivery.show', $delivery->id) }}'">

    <div class="delivery-card-top">
        <div class="d-flex align-items-center gap-2">
            <span class="delivery-order-number">#{{ $delivery->id }}</span>

            @if($delivery->delivery_type === 'pickup')
                <span class="delivery-type-badge pickup">
                    <i class="bi bi-bag-check"></i>
                    Recojo
                </span>
            @else
                <span class="delivery-type-badge delivery">
                    <i class="bi bi-bicycle"></i>
                    Delivery
                </span>
            @endif
        </div>

        <span class="delivery-time">
            <i class="bi bi-clock"></i>
            {{ $delivery->created_at->format('H:i') }}
        </span>
    </div>

    <div class="delivery-customer">
        {{ $delivery->client_name ?: 'Consumidor final' }}
    </div>

    <div class="delivery-location">
        @if($delivery->delivery_type === 'pickup')
            <i class="bi bi-shop"></i>
            <span>Recojo en local</span>
        @else
            <i class="bi bi-geo-alt"></i>
            <span>{{ $delivery->address ?: 'Sin dirección registrada' }}</span>
        @endif
    </div>

    <div class="delivery-card-info">

        <div class="delivery-payment">
            @if($delivery->payment_method === 'cash')
                <i class="bi bi-cash"></i>
                <span>Efectivo</span>
            @elseif($delivery->payment_method === 'card')
                <i class="bi bi-credit-card"></i>
                <span>Tarjeta</span>
            @elseif($delivery->payment_method === 'yape')
                <i class="bi bi-phone"></i>
                <span>Yape</span>
            @elseif($delivery->payment_method === 'plin')
                <i class="bi bi-phone"></i>
                <span>Plin</span>
            @else
                <i class="bi bi-wallet2"></i>
                <span>Pago</span>
            @endif
        </div>

        @if($delivery->delivery_type === 'delivery' &&
            ($delivery->status === 'on_way' || $delivery->status === 'delivered'))

            <div class="delivery-driver" title="{{ $delivery->driver->name ?? 'Sin asignar' }}">
                <i class="bi bi-person"></i>
                <span>{{ $delivery->driver->name ?? 'Sin asignar' }}</span>
            </div>

        @elseif($delivery->delivery_type === 'pickup' &&
                ($delivery->status === 'on_way' || $delivery->status === 'delivered'))

            <div class="delivery-ready">
                <i class="bi bi-check-circle"></i>
                <span>
                    {{ $delivery->status === 'delivered' ? 'Entregado' : 'Listo' }}
                </span>
            </div>

        @elseif($delivery->status === 'pending')

            <div class="delivery-pending">
                <i class="bi bi-hourglass-split"></i>
                <span>Pendiente</span>
            </div>

        @elseif($delivery->status === 'preparing')

            <div class="delivery-preparing">
                <i class="bi bi-fire"></i>
                <span>Preparando</span>
            </div>

        @endif

    </div>

    <div class="delivery-card-footer">
        <div>
            <span class="delivery-total-label">Total</span>
            <div class="delivery-total">
                {{ $currency ?? 'S/' }}{{ number_format($delivery->total_with_fee, 2) }}
            </div>
        </div>

        <div class="delivery-open">
            Ver pedido
            <i class="bi bi-chevron-right"></i>
        </div>
    </div>

</div>