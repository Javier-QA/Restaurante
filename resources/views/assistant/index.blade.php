@extends('layouts.app')
@section('content')
<div class="sys-ia">
<div class="page-head">
  <div><h1>Asistente IA</h1><p>Pregunta en lenguaje natural y obtén tablas, gráficos y resúmenes de tu restaurante</p></div>
  <div class="d-flex gap-2"><button class="btn btn-soft px-3" id="btnEsq"><i class="bi bi-diagram-3 me-1"></i> Qué datos ve la IA</button><button class="btn btn-soft px-3" id="btnCfg"><i class="bi bi-gear me-1"></i> Configurar IA</button></div>
</div>

<div class="ia-hero"><h2><i class="bi bi-stars me-2"></i>¿Qué quieres saber de tu restaurante?</h2>
  <p>Escríbelo como se lo preguntarías a un analista. Ejemplo: «¿Cuáles fueron los 10 platos más vendidos este mes?»</p>
  <form class="ia-ask" id="frmAsk"><textarea id="iaQ" rows="1" maxlength="500" placeholder="Escribe tu pregunta aquí…" aria-label="Pregunta"></textarea><button id="iaGo"><i class="bi bi-send-fill"></i> Preguntar</button></form>
  <div class="ia-chips" id="iaChips"></div></div>

<div id="iaSetup" class="card-x h-auto ia-setup mb-3" style="display:none"><div class="body">
  <h6 class="fw-bold"><i class="bi bi-rocket-takeoff me-2" style="color:var(--accent)"></i>Activa el asistente en 2 minutos (gratis)</h6>
  <div class="st"><b>1</b><div>Entra a <a href="https://aistudio.google.com/apikey" target="_blank" rel="noopener" class="link-accent">aistudio.google.com/apikey</a> con tu cuenta de Google y crea una clave gratuita.</div></div>
  <div class="st"><b>2</b><div class="flex-grow-1">Pega la clave aquí y pulsa <b>Activar</b>:
    <form class="d-flex gap-2 mt-2 flex-wrap" id="frmQuick"><input type="password" class="form-control" id="qKey" style="max-width:340px" placeholder="Clave de Gemini" autocomplete="new-password"><button class="btn btn-accent px-3" id="qGo">Activar</button></form>
    <div class="small text-danger mt-1" id="qErr"></div></div></div>
  <div class="small text-muted mb-2">También puedes usar Groq, OpenRouter u Ollama (local, sin enviar datos a internet) desde la configuración avanzada.</div>
  <button class="btn btn-soft" type="button" id="btnCfg2"><i class="bi bi-gear me-1"></i> Configuración avanzada</button></div></div>

<div class="row g-3">
  <div class="col-lg-8"><div id="iaOut"><div class="card-x h-auto"><div class="body text-center text-muted py-5"><i class="bi bi-chat-square-text" style="font-size:2.4rem"></i><div class="mt-2">Aquí aparecerán tus resultados</div><div class="small">Puedes hacer preguntas de seguimiento, como «ahora solo delivery».</div></div></div></div></div>
  <div class="col-lg-4">
    <div class="card-x h-auto ia-side mb-3"><div class="body"><h6 class="fw-bold mb-2"><i class="bi bi-star-fill text-warning me-1"></i>Favoritas</h6><div id="iaFav"></div></div></div>
    <div class="card-x h-auto ia-side"><div class="body"><h6 class="fw-bold mb-2"><i class="bi bi-clock-history me-1"></i>Recientes</h6><div id="iaHis"></div></div></div>
  </div>
</div>

@include('assistant.config')

<div class="modal fade" id="mdlEsq" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable"><div class="modal-content border-0" style="border-radius:20px">
 <div class="modal-header border-0 px-4 pt-4"><h5 class="modal-title fw-bold"><i class="bi bi-diagram-3 me-2"></i>Datos que ve la IA</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
 <div class="modal-body px-4"><p class="text-muted">Puedes preguntar por la información registrada en estos módulos. La IA consulta los datos y te ayuda a entenderlos.</p><div class="row g-3"><div class="col-md-6"><div class="card-x h-auto"><div class="body pt-3"><h6 class="fw-bold">Ventas y pedidos</h6><p class="small mb-2">Importes vendidos, cantidad de pedidos, descuentos, impuestos, propinas y métodos de pago. Puedes comparar períodos o consultar ventas por mozo, mesa y tipo de pedido.</p><div class="small text-muted">Ejemplo: «¿Cuánto vendimos esta semana?»</div></div></div></div><div class="col-md-6"><div class="card-x h-auto"><div class="body pt-3"><h6 class="fw-bold">Platos, productos, recetas y costos</h6><p class="small mb-2">Productos más vendidos, categorías, precios y costos actuales de productos o recetas.</p><div class="small text-muted">Ejemplo: «¿Cuáles son los 5 platos más vendidos este mes?»</div></div></div></div><div class="col-md-6"><div class="card-x h-auto"><div class="body pt-3"><h6 class="fw-bold">Inventario y movimientos</h6><p class="small mb-2">Stock actual de insumos y productos, valor de las existencias, entradas, salidas y ajustes registrados.</p><div class="small text-muted">Ejemplo: «¿Qué insumos están sin stock?»</div></div></div></div><div class="col-md-6"><div class="card-x h-auto"><div class="body pt-3"><h6 class="fw-bold">Gastos</h6><p class="small mb-2">Conceptos, importes y fechas de los gastos registrados.</p><div class="small text-muted">Ejemplo: «¿Cuánto gastamos este mes?»</div></div></div></div><div class="col-md-6"><div class="card-x h-auto"><div class="body pt-3"><h6 class="fw-bold">Clientes</h6><p class="small mb-2">Cantidad de pedidos pagados, consumo acumulado y última compra de cada cliente.</p><div class="small text-muted">Ejemplo: «¿Qué clientes consumieron más este mes?»</div></div></div></div><div class="col-md-6"><div class="card-x h-auto"><div class="body pt-3"><h6 class="fw-bold">Reservas</h6><p class="small mb-2">Mesas, zonas, cantidad de personas, fechas y estado de las reservas.</p><div class="small text-muted">Ejemplo: «¿Cuántas reservas confirmadas tenemos hoy?»</div></div></div></div><div class="col-md-6"><div class="card-x h-auto"><div class="body pt-3"><h6 class="fw-bold">Caja</h6><p class="small mb-2">Aperturas y cierres, cajero, monto inicial, monto esperado, monto final y diferencias.</p><div class="small text-muted">Ejemplo: «¿Qué cierres de caja tuvieron faltantes?»</div></div></div></div><div class="col-md-6"><div class="card-x h-auto"><div class="body pt-3"><h6 class="fw-bold">Delivery y recojo</h6><p class="small mb-2">Pedidos, repartidor asignado, estado de entrega, hora prometida y costo de envío.</p><div class="small text-muted">Ejemplo: «¿Qué pedidos de delivery están pendientes de entrega?»</div></div></div></div></div><div class="ia-privacy mt-3 small"><b>Ten en cuenta:</b> los reportes de ventas consideran pedidos pagados. Los costos se calculan con los valores actuales. La IA puede consultar y explicar; no modifica pedidos ni inventario. No consulta contraseñas ni claves de acceso.</div></div></div></div></div>

</div>
@endsection
@push('scripts')
<script src="{{ asset('sys-ia/chartjs/chart.umd.js') }}"></script>
<script src="{{ asset('sys-ia/asistente.js') }}"></script>
@endpush
