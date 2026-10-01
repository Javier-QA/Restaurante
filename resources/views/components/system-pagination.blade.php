@props([
    'paginator',
    'label' => 'resultados'
])

@if($paginator->total() > 0)
    <div class="system-pagination">
        <div class="system-pagination-info">
            Mostrando
            <strong>{{ $paginator->firstItem() ?? 0 }}</strong>
            a
            <strong>{{ $paginator->lastItem() ?? 0 }}</strong>
            de
            <strong>{{ $paginator->total() }}</strong>
            {{ $label }}
        </div>

        @if($paginator->hasPages())
            <div class="system-pagination-controls">

                @if($paginator->onFirstPage())
                    <span class="system-page-btn disabled" title="Página anterior">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                       class="system-page-btn"
                       title="Página anterior">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                @endif

                @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                    @if($page == $paginator->currentPage())
                        <span class="system-page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="system-page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}"
                       class="system-page-btn"
                       title="Página siguiente">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @else
                    <span class="system-page-btn disabled" title="Página siguiente">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                @endif

            </div>
        @endif
    </div>
@endif

@once
<style>
.system-pagination {
    width: 100%;
    min-height: 82px;
    padding: 14px 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: var(--card-bg);
    border-top: 1px solid var(--border-soft);
}

.system-pagination-info {
    width: 100%;
    text-align: center;
    color: var(--text-muted);
    font-size: .82rem;
}

.system-pagination-info strong {
    color: var(--text-main);
    font-weight: 700;
}

.system-pagination-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 6px;
}

.system-page-btn {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--border-soft);
    border-radius: 10px;
    background: var(--card-bg);
    color: var(--text-main);
    font-size: .82rem;
    font-weight: 700;
    text-decoration: none;
    transition: .18s ease;
}

.system-page-btn:hover {
    background: color-mix(in srgb, var(--primary) 8%, var(--card-bg));
    border-color: color-mix(in srgb, var(--primary) 30%, var(--border-soft));
    color: var(--primary);
}

.system-page-btn.active {
    background: var(--primary);
    border-color: var(--primary);
    color: #fff;
    box-shadow: 0 4px 12px color-mix(in srgb, var(--primary) 22%, transparent);
}

.system-page-btn.disabled {
    opacity: .4;
    cursor: default;
    pointer-events: none;
}

@media (max-width: 575.98px) {
    .system-pagination {
        padding: 14px 10px;
    }

    .system-page-btn {
        width: 34px;
        height: 34px;
    }
}
</style>
@endonce