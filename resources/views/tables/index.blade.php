@extends('layouts.app')

@section('content')
<div class="container-fluid tables-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0" style="color: #000 !important;"><i class="bi bi-grid-3x3-gap-fill me-2" style="color: #000 !important;"></i> Diseño de Salón</h2>
            <p class="text-muted mb-0">Arrastra las mesas y guarda la distribución</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn tables-action-btn tables-zone-btn" data-bs-toggle="modal" data-bs-target="#areaModal">
                <i class="bi bi-plus-circle me-2"></i> Nueva Zona
            </button>
            <button class="btn tables-action-btn tables-table-btn" data-bs-toggle="modal" data-bs-target="#tableModal">
                <i class="bi bi-plus-lg me-2"></i> Nueva Mesa
            </button>
            <button class="btn tables-action-btn tables-save-btn fw-bold px-4" onclick="savePositions()" id="btnSave">
                <i class="bi bi-save me-2"></i> Guardar Diseño
            </button>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="areaTabs" role="tablist">
        @foreach($areas as $index => $area)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $index == 0 ? 'active' : '' }} fw-bold" 
                        id="tab-{{ $area->id }}" 
                        data-bs-toggle="tab" 
                        data-bs-target="#area-{{ $area->id }}" 
                        type="button" role="tab">
                    {{ $area->name }}
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content" id="areaTabsContent">
        @foreach($areas as $index => $area)
            <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="area-{{ $area->id }}" role="tabpanel">
                
                <div class="d-flex justify-content-between align-items-center mb-2 bg-white p-2 border rounded">
                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Arrastra las mesas y luego presiona <b>Guardar Diseño</b>.</small>
                    <form action="{{ route('tables.destroyArea', $area->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta zona y sus mesas?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm tables-delete-zone"><i class="bi bi-trash3 me-1"></i> Eliminar Zona</button>
                    </form>
                </div>

                <div class="salon-canvas border rounded-3 shadow-sm position-relative" style="height: 720px; background-image: radial-gradient(#dee2e6 1px, transparent 1px); background-size: 20px 20px; overflow: hidden;">
                    @foreach($area->tables as $table)
                        <div class="draggable-table position-absolute d-flex flex-column align-items-center justify-content-center bg-white border shadow-sm rounded-3"
                             id="table-{{ $table->id }}"
                             data-id="{{ $table->id }}"
                             style="width: 155px; height: 155px; 
                                    left: {{ $table->x_pos }}px; 
                                    top: {{ $table->y_pos }}px; 
                                    cursor: grab; z-index: 10;
                                    transition: box-shadow 0.2s;">
                            
                            <i class="bi bi-display fs-3 {{ $table->status == 'available' ? 'text-success' : 'text-danger' }} mb-1"></i>
                            <span class="fw-bold small text-center text-truncate w-100 px-1">{{ $table->name }}</span>
                            
                            <form action="{{ route('tables.destroyTable', $table->id) }}" method="POST" class="position-absolute top-0 end-0 m-1">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm p-0 text-danger opacity-25 hover-opacity-100" onclick="return confirm('¿Borrar mesa?')">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="modal fade" id="areaModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form action="{{ route('tables.storeArea') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-light"><h5 class="modal-title fw-bold">Nueva Zona</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><label>Nombre</label><input type="text" name="name" class="form-control" required></div>
            <div class="modal-footer"><button class="btn btn-primary w-100">Crear</button></div>
        </form>
    </div>
</div>
<div class="modal fade" id="tableModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form action="{{ route('tables.storeTable') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-light"><h5 class="modal-title fw-bold">Nueva Mesa</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label>Nombre</label><input type="text" name="name" class="form-control" placeholder="Mesa 1" required></div>
                <div class="mb-3"><label>Zona</label><select name="area_id" class="form-select">@foreach($areas as $area) <option value="{{ $area->id }}">{{ $area->name }}</option> @endforeach</select></div>
            </div>
            <div class="modal-footer"><button class="btn btn-primary w-100">Crear</button></div>
        </form>
    </div>
</div>


<style>
    /* =========================================================
       MODULO MESAS - PALETA DINAMICA
    ========================================================== */

    .tables-page {
        --tables-primary: var(--primary, #ff8c00);
        --tables-primary-hover: var(--primary-hover, #e07b00);
        --tables-dark: var(--dark-bg, #063970);
        --tables-dark-2: var(--dark-bg-2, #0b4f8a);
        --tables-accent-1: var(--accent-1, #16a34a);
        --tables-accent-2: var(--accent-2, #0ea5e9);
        --tables-card: var(--card-bg, #ffffff);
        --tables-text: var(--text-main, #172033);
        --tables-muted: var(--text-muted, #64748b);
        --tables-border: var(--border-soft, #dce7f1);
    }

    .tables-page h2 {
        color: var(--tables-dark) !important;
    }

    /* PESTAÑAS DE ZONAS */

    .tables-page #areaTabs {
        border-bottom: 0;
        gap: 8px;
    }

    .tables-page #areaTabs .nav-link {
        color: var(--tables-muted);
        background: var(--tables-card);
        border: 1px solid var(--tables-border);
        border-radius: 10px;
        padding: 9px 22px;
        transition: all .2s ease;
    }

    .tables-page #areaTabs .nav-link:hover {
        color: var(--tables-primary);
        border-color: var(--tables-primary);
        transform: translateY(-1px);
    }

    .tables-page #areaTabs .nav-link.active {
        background: var(--tables-primary);
        border-color: var(--tables-primary);
        color: #fff;
        box-shadow: 0 5px 14px
            color-mix(in srgb, var(--tables-primary) 28%, transparent);
    }

    /* LIENZO DEL SALON */

    .tables-page .salon-canvas {
        background-color:
            color-mix(
                in srgb,
                var(--tables-primary) 12%,
                #ffffff
            ) !important;

        background-image:
            radial-gradient(
                color-mix(
                    in srgb,
                    var(--tables-dark) 28%,
                    transparent
                ) 1px,
                transparent 1px
            ) !important;

        background-size: 22px 22px !important;
        border-color: var(--tables-border) !important;
        box-shadow:
            inset 0 0 30px rgba(15, 23, 42, .03) !important;
    }

    /* TARJETAS DE MESAS */

    .tables-page .draggable-table {
        background: var(--tables-card) !important;
        border: 2px solid
            color-mix(
                in srgb,
                var(--tables-primary) 55%,
                var(--tables-border)
            ) !important;

        color: var(--tables-text);
        border-radius: 12px !important;
        box-shadow: 0 5px 14px rgba(15, 23, 42, .10) !important;
        transition:
            box-shadow .2s ease,
            transform .2s ease !important;
    }

    .tables-page .draggable-table::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        border-radius: 10px 10px 0 0;
        background: var(--tables-primary);
    }

    .tables-page .draggable-table:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(15, 23, 42, .16) !important;
    }

    .tables-page .draggable-table > span {
        color: var(--tables-text) !important;
        font-size: .88rem;
        font-weight: 800 !important;
    }

    .tables-page .draggable-table > .bi-display {
        font-size: 2.6rem !important;
    }

    /* BOTONES PRINCIPALES */

    .tables-page .btn-primary {
        background: var(--tables-primary);
        border-color: var(--tables-primary);
    }

    .tables-page .btn-primary:hover {
        background: var(--tables-primary-hover);
        border-color: var(--tables-primary-hover);
    }

    .tables-page .btn-outline-primary {
        color: var(--tables-primary);
        border-color: var(--tables-primary);
    }

    .tables-page .btn-outline-primary:hover {
        color: #fff;
        background: var(--tables-primary);
        border-color: var(--tables-primary);
    }

    /* BOTON GUARDAR */

    .tables-page #btnSave {
        background: var(--tables-accent-1);
        border-color: var(--tables-accent-1);
    }

    .tables-page #btnSave:hover {
        background: color-mix(in srgb, var(--tables-accent-1) 85%, black);
        border-color: color-mix(in srgb, var(--tables-accent-1) 85%, black);
    }


    /* BOTON NUEVA ZONA */

    .tables-page .tables-zone-btn {
        background: var(--tables-dark);
        border: 1px solid var(--tables-dark);
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(15, 23, 42, .10);
        transition: all .2s ease;
    }

    .tables-page .tables-zone-btn:hover,
    .tables-page .tables-zone-btn:focus {
        background: var(--tables-dark-2);
        border-color: var(--tables-dark-2);
        color: #ffffff !important;
        transform: translateY(-1px);
    }

    .tables-page .tables-zone-btn i {
        color: #ffffff !important;
    }


    /* =========================================================
       BOTONES PRINCIPALES - PALETA DEL SISTEMA
    ========================================================== */

    .tables-page .tables-action-btn {
        color: #fff !important;
        border: 0 !important;
        border-radius: 9px;
        padding: 9px 16px;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(15, 23, 42, .10);
        transition: transform .2s ease, filter .2s ease, box-shadow .2s ease;
    }

    /* Nueva Zona */
    .tables-page .tables-zone-btn {
        background: var(--tables-accent-2) !important;
    }

    /* Nueva Mesa */
    .tables-page .tables-table-btn {
        background: var(--tables-primary) !important;
    }

    /* Guardar Diseño */
    .tables-page .tables-save-btn {
        background: var(--tables-accent-1) !important;
    }

    .tables-page .tables-action-btn:hover,
    .tables-page .tables-action-btn:focus {
        color: #fff !important;
        filter: brightness(.90);
        transform: translateY(-2px);
        box-shadow: 0 7px 15px rgba(15, 23, 42, .16);
    }

    .tables-page .tables-action-btn i {
        color: #fff !important;
    }


    /* BOTON ELIMINAR ZONA */

    .tables-page .tables-delete-zone {
        background: #dc3545 !important;
        border: 1px solid #dc3545 !important;
        color: #ffffff !important;
        font-weight: 700;
        border-radius: 8px;
        padding: 6px 12px;
        transition: all .2s ease;
    }

    .tables-page .tables-delete-zone:hover,
    .tables-page .tables-delete-zone:focus {
        background: #bb2d3b !important;
        border-color: #bb2d3b !important;
        color: #ffffff !important;
        transform: translateY(-1px);
    }

    .tables-page .tables-delete-zone i {
        color: #ffffff !important;
    }

</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const draggables = document.querySelectorAll('.draggable-table');
        let activeDrag = null;
        let initialX, initialY, currentX, currentY;

        draggables.forEach(el => el.addEventListener('mousedown', dragStart));
        document.addEventListener('mouseup', dragEnd);
        document.addEventListener('mousemove', drag);

        function dragStart(e) {
            if (e.target.closest('form')) return; // No arrastrar si clickea en borrar
            activeDrag = e.currentTarget;
            
            // Obtener posición actual real
            let rect = activeDrag.getBoundingClientRect();
            let parentRect = activeDrag.parentElement.getBoundingClientRect();
            
            // Calculamos la posición relativa al contenedor
            let styleLeft = activeDrag.offsetLeft;
            let styleTop = activeDrag.offsetTop;

            initialX = e.clientX - styleLeft;
            initialY = e.clientY - styleTop;

            activeDrag.style.cursor = 'grabbing';
            activeDrag.style.zIndex = 100;
            activeDrag.classList.add('shadow-lg');
        }

        function dragEnd() {
            if(!activeDrag) return;
            activeDrag.style.cursor = 'grab';
            activeDrag.style.zIndex = 10;
            activeDrag.classList.remove('shadow-lg');
            activeDrag = null;
        }

        function drag(e) {
            if (activeDrag) {
                e.preventDefault();
                currentX = e.clientX - initialX;
                currentY = e.clientY - initialY;

                // Límites simples (evitar que salga mucho)
                if(currentX < 0) currentX = 0;
                if(currentY < 0) currentY = 0;

                activeDrag.style.left = currentX + "px";
                activeDrag.style.top = currentY + "px";
            }
        }
    });

    function savePositions() {
        let btn = document.getElementById('btnSave');
        let originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Guardando...';
        btn.disabled = true;

        let positions = [];
        document.querySelectorAll('.draggable-table').forEach(el => {
            positions.push({
                id: el.getAttribute('data-id'),
                x: parseInt(el.style.left.replace('px', '') || 0),
                y: parseInt(el.style.top.replace('px', '') || 0)
            });
        });

        fetch("{{ route('tables.updatePositions') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ positions: positions })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en el servidor: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if(data.status === 'success') {
                alert('¡Diseño guardado con éxito! ✅');
            } else {
                alert('Error al guardar: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('¡Ocurrió un error al guardar! \nVerifica que hayas ejecutado "php artisan migrate". \nDetalle: ' + error.message);
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
</script>
@endsection