@php
    [$sectionTitle, $sectionIcon] = match (true) {
        request()->routeIs('pos.*', 'delivery.*', 'reservations.*', 'sales.*', 'expenses.*', 'kitchen.*', 'barra.*') => ['Operaciones', 'bi-shop'],
        request()->routeIs('cash_registers.*', 'billing.*', 'credit_notes.*', 'daily_summaries.*') => ['Caja / Arqueo', 'bi-cash-stack'],
        request()->routeIs('clients.*', 'categories.*', 'products.*', 'menu.*', 'tables.*', 'users.*', 'settings.*', 'system.*') => ['Gestión', 'bi-gear'],
        request()->routeIs('ai.*', 'assistant.*', 'chatbot.*') => ['Inteligencia', 'bi-stars'],
        request()->routeIs('dashboard', 'reports.*') => ['General', 'bi-grid-1x2-fill'],
        default => ['Sistema de Restaurante', 'bi-grid-1x2-fill'],
    };
@endphp
<span class="topbar-module-icon"><i class="bi {{ $sectionIcon }}"></i></span>
<h5 class="mb-0">{{ $sectionTitle }}</h5>
