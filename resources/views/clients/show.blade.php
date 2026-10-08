@extends('layouts.app')

@section('content')
<style>
    .client-profile-page {
        --profile-blue: #2563eb;
        --profile-green: #16a34a;
        --profile-amber: #f59e0b;
    }

    .client-profile-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
    }

    .client-profile-heading {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .client-back-btn {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        background: var(--card-bg);
        color: var(--text-main);
        text-decoration: none;
        transition: .2s ease;
        box-shadow: 0 3px 10px rgba(15, 23, 42, .04);
    }

    .client-back-btn:hover {
        color: var(--primary);
        border-color: color-mix(in srgb, var(--primary) 35%, var(--border-soft));
        transform: translateX(-2px);
    }

    .client-profile-title {
        margin: 0;
        color: var(--text-main);
        font-size: 1.65rem;
        font-weight: 800;
        letter-spacing: -.03em;
    }

    .client-profile-subtitle {
        margin: 3px 0 0;
        color: var(--text-muted);
        font-size: .9rem;
    }

    .client-profile-card,
    .client-history-card {
        background: var(--card-bg);
        border: 1px solid var(--border-soft);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-soft);
        overflow: hidden;
    }

    .client-profile-card {
        height: 100%;
    }

    .client-identity {
        position: relative;
        padding: 30px 24px 25px;
        text-align: center;
        border-bottom: 1px solid var(--border-soft);
        background:
            linear-gradient(
                180deg,
                color-mix(in srgb, var(--primary) 8%, var(--card-bg)) 0%,
                var(--card-bg) 100%
            );
    }

    .client-avatar-wrap {
        position: relative;
        width: 96px;
        height: 96px;
        margin: 0 auto 17px;
    }

    .client-avatar {
        width: 96px;
        height: 96px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 26px;
        background: var(--primary);
        color: #fff;
        font-size: 2.4rem;
        font-weight: 800;
        text-transform: uppercase;
        box-shadow: 0 10px 24px color-mix(in srgb, var(--primary) 25%, transparent);
    }

    .client-rank {
        position: absolute;
        right: -13px;
        bottom: -7px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 10px;
        border: 3px solid var(--card-bg);
        border-radius: 999px;
        background: var(--card-bg);
        color: var(--text-main);
        font-size: .72rem;
        font-weight: 800;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .12);
        white-space: nowrap;
    }

    .client-rank-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--primary);
    }

    .client-name {
        margin-bottom: 5px;
        color: var(--text-main);
        font-size: 1.22rem;
        font-weight: 800;
    }

    .client-address {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        gap: 6px;
        margin: 0;
        color: var(--text-muted);
        font-size: .84rem;
    }

    .client-address i {
        margin-top: 2px;
        color: var(--primary);
    }

    .client-details {
        padding: 10px 22px 18px;
    }

    .client-detail-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px 0;
        border-bottom: 1px solid var(--border-soft);
    }

    .client-detail-row:last-child {
        border-bottom: 0;
    }

    .client-detail-icon {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 11px;
        background: color-mix(in srgb, var(--primary) 9%, var(--card-bg));
        color: var(--primary);
        font-size: 1rem;
    }

    .client-detail-content {
        min-width: 0;
        flex: 1;
    }

    .client-detail-label {
        display: block;
        margin-bottom: 2px;
        color: var(--text-muted);
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .client-detail-value {
        display: block;
        overflow-wrap: anywhere;
        color: var(--text-main);
        font-size: .87rem;
        font-weight: 650;
    }

    .client-stat-card {
        position: relative;
        height: 100%;
        min-height: 118px;
        padding: 20px 20px 20px 22px;
        overflow: hidden;
        border: 1px solid var(--border-soft);
        border-radius: var(--radius-xl);
        box-shadow: 0 5px 16px rgba(15, 23, 42, .06);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .client-stat-card::before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 5px;
        border-radius: var(--radius-xl) 0 0 var(--radius-xl);
    }

    .client-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 9px 22px rgba(15, 23, 42, .09);
    }

    .client-stat-card.spent {
        background: var(--card-bg);
    }

    .client-stat-card.spent::before {
        background: linear-gradient(180deg, #3b82f6 0%, #93c5fd 55%, var(--surface-blue, #dbeafe) 100%);
    }

    .client-stat-card.visits {
        background: var(--card-bg);
    }

    .client-stat-card.visits::before {
        background: linear-gradient(180deg, #22c55e 0%, #86efac 55%, var(--surface-green, #dcfce7) 100%);
    }

    .client-stat-card.favorite {
        background: var(--card-bg);
    }

    .client-stat-card.favorite::before {
        background: linear-gradient(180deg, #f59e0b 0%, #fbbf24 48%, var(--surface-amber, #ffedd5) 100%);
    }

    .client-stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 15px;
    }

    .client-stat-label {
        color: var(--text-muted);
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .client-stat-icon {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 11px;
        font-size: 1rem;
    }

    .client-stat-card.spent .client-stat-icon {
        background: var(--surface-blue, #eff6ff);
        color: var(--profile-blue);
    }

    .client-stat-card.visits .client-stat-icon {
        background: var(--surface-green, #f0fdf4);
        color: var(--profile-green);
    }

    .client-stat-card.favorite .client-stat-icon {
        background: var(--surface-amber, #fffbeb);
        color: var(--profile-amber);
    }

    .client-stat-value {
        margin: 0;
        color: var(--text-main);
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .client-stat-value.favorite-value {
        max-width: 100%;
        overflow: hidden;
        font-size: 1rem;
        line-height: 1.3;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .client-history-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--border-soft);
    }

    .client-history-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        color: var(--text-main);
        font-size: .95rem;
        font-weight: 800;
    }

    .client-history-title-icon {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: color-mix(in srgb, var(--primary) 9%, var(--card-bg));
        color: var(--primary);
    }

    .client-history-count {
        padding: 5px 9px;
        border-radius: 999px;
        background: color-mix(in srgb, var(--primary) 8%, var(--card-bg));
        color: var(--primary);
        font-size: .7rem;
        font-weight: 800;
    }

    .client-orders-table {
        margin: 0;
    }

    .client-orders-table thead th {
        padding-top: 13px;
        padding-bottom: 13px;
        border-bottom: 1px solid var(--border-soft);
        background: color-mix(in srgb, var(--light-bg) 70%, var(--card-bg));
        color: var(--text-muted);
        font-size: .69rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .client-orders-table tbody td {
        padding-top: 14px;
        padding-bottom: 14px;
        border-color: var(--border-soft);
        color: var(--text-main);
        font-size: .84rem;
        vertical-align: middle;
    }

    .client-order-date {
        font-weight: 700;
    }

    .client-order-time {
        display: block;
        margin-top: 2px;
        color: var(--text-muted);
        font-size: .72rem;
        font-weight: 500;
    }

    .client-order-folio {
        display: inline-flex;
        padding: 5px 9px;
        border: 1px solid var(--border-soft);
        border-radius: 8px;
        background: var(--light-bg);
        color: var(--text-main);
        font-size: .76rem;
        font-weight: 750;
    }

    .client-ticket-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border-soft);
        border-radius: 9px;
        background: var(--card-bg);
        color: var(--text-main);
        text-decoration: none;
        transition: .18s ease;
    }

    .client-ticket-btn:hover {
        border-color: var(--primary);
        background: color-mix(in srgb, var(--primary) 8%, var(--card-bg));
        color: var(--primary);
    }

    .client-empty {
        padding: 55px 20px !important;
        text-align: center;
    }

    .client-empty-icon {
        width: 52px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        border-radius: 15px;
        background: var(--light-bg);
        color: var(--text-muted);
        font-size: 1.3rem;
    }

    .client-empty-title {
        margin-bottom: 4px;
        color: var(--text-main);
        font-size: .9rem;
        font-weight: 750;
    }

    .client-empty-text {
        margin: 0;
        color: var(--text-muted);
        font-size: .78rem;
    }

    @media (max-width: 767.98px) {
        .client-profile-header {
            align-items: flex-start;
        }

        .client-profile-title {
            font-size: 1.35rem;
        }

        .client-identity {
            padding-top: 25px;
        }

        .client-history-header {
            padding: 15px;
        }
    }
</style>

<div class="container-fluid client-profile-page">

    <div class="client-profile-header">
        <div class="client-profile-heading">
            <a href="{{ route('clients.index') }}"
               class="client-back-btn"
               title="Volver a clientes">
                <i class="bi bi-arrow-left"></i>
            </a>

            <div>
                <h2 class="client-profile-title">Perfil de Cliente</h2>
                <p class="client-profile-subtitle">
                    Información, historial de consumo y preferencias del cliente
                </p>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- PERFIL --}}
        <div class="col-xl-4 col-lg-5">
            <div class="client-profile-card">

                <div class="client-identity">
                    <div class="client-avatar-wrap">
                        <div class="client-avatar">
                            {{ mb_strtoupper(mb_substr(trim($client->name), 0, 1)) }}
                        </div>

                        <span class="client-rank">
                            <span class="client-rank-dot"></span>
                            {{ $rank }}
                        </span>
                    </div>

                    <h4 class="client-name">{{ $client->name }}</h4>

                    <p class="client-address">
                        <i class="bi bi-geo-alt"></i>
                        <span>{{ $client->address ?? 'Dirección no registrada' }}</span>
                    </p>
                </div>

                <div class="client-details">

                    <div class="client-detail-row">
                        <div class="client-detail-icon">
                            <i class="bi bi-person-vcard"></i>
                        </div>
                        <div class="client-detail-content">
                            <span class="client-detail-label">Documento</span>
                            <span class="client-detail-value">
                                {{ $client->document_number ?? 'No registrado' }}
                            </span>
                        </div>
                    </div>

                    <div class="client-detail-row">
                        <div class="client-detail-icon">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div class="client-detail-content">
                            <span class="client-detail-label">Teléfono</span>
                            <span class="client-detail-value">
                                {{ $client->phone ?? 'No registrado' }}
                            </span>
                        </div>
                    </div>

                    <div class="client-detail-row">
                        <div class="client-detail-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="client-detail-content">
                            <span class="client-detail-label">Correo electrónico</span>
                            <span class="client-detail-value">
                                {{ $client->email ?? 'No registrado' }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- RESUMEN E HISTORIAL --}}
        <div class="col-xl-8 col-lg-7">

            <div class="row g-3 mb-4">

                <div class="col-md-4">
                    <div class="client-stat-card spent">
                        <div class="client-stat-top">
                            <span class="client-stat-label">Total gastado</span>
                            <span class="client-stat-icon">
                                <i class="bi bi-wallet2"></i>
                            </span>
                        </div>
                        <h3 class="client-stat-value">
                            S/ {{ number_format($totalSpent, 2) }}
                        </h3>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="client-stat-card visits">
                        <div class="client-stat-top">
                            <span class="client-stat-label">Visitas</span>
                            <span class="client-stat-icon">
                                <i class="bi bi-shop"></i>
                            </span>
                        </div>
                        <h3 class="client-stat-value">{{ $visitCount }}</h3>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="client-stat-card favorite">
                        <div class="client-stat-top">
                            <span class="client-stat-label">Plato favorito</span>
                            <span class="client-stat-icon">
                                <i class="bi bi-heart"></i>
                            </span>
                        </div>
                        <h3 class="client-stat-value favorite-value"
                            title="{{ $favoriteProduct }}">
                            {{ $favoriteProduct }}
                        </h3>
                    </div>
                </div>

            </div>

            <div class="client-history-card">
                <div class="client-history-header">
                    <h6 class="client-history-title">
                        <span class="client-history-title-icon">
                            <i class="bi bi-clock-history"></i>
                        </span>
                        Historial de Pedidos
                    </h6>

                    <span class="client-history-count">
                        {{ $visitCount }}
                        {{ $visitCount == 1 ? 'pedido' : 'pedidos' }}
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle client-orders-table">
                        <thead>
                            <tr>
                                <th class="ps-4">Fecha</th>
                                <th>Folio</th>
                                <th>Mesa</th>
                                <th class="text-end">Total</th>
                                <th class="text-center pe-4">Ticket</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td class="ps-4">
                                        <span class="client-order-date">
                                            {{ $order->created_at->format('d/m/Y') }}
                                        </span>
                                        <span class="client-order-time">
                                            {{ $order->created_at->format('H:i') }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="client-order-folio">
                                            #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($order->delivery)
                                            <span class="badge rounded-pill bg-primary-subtle text-primary">
                                                <i class="bi bi-bicycle me-1"></i>
                                                Delivery
                                            </span>
                                        @elseif($order->table)
                                            {{ $order->table->name }}
                                        @else
                                            Barra
                                        @endif
                                    </td>

                                    <td class="text-end fw-bold">
                                        S/ {{ number_format($order->collected_total, 2) }}
                                    </td>

                                    <td class="text-center pe-4">
                                        <a href="{{ route('sales.ticket', $order->id) }}"
                                           target="_blank"
                                           class="client-ticket-btn"
                                           title="Ver ticket">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="client-empty">
                                        <div class="client-empty-icon">
                                            <i class="bi bi-receipt"></i>
                                        </div>
                                        <div class="client-empty-title">
                                            Sin pedidos registrados
                                        </div>
                                        <p class="client-empty-text">
                                            Los pedidos realizados por este cliente aparecerán aquí.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection