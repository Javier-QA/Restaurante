@extends('layouts.app')
@section('content')
<div class="sys-ia">
<div class="page-head"><div><h1>Chat IA</h1><p>Conversa con tu sistema: pregunta por ventas, platos, insumos, compras, clientes o cómo usar cada módulo</p></div><button type="button" class="btn btn-soft px-3" id="btnChatCfg"><i class="bi bi-gear me-1"></i>Configurar IA</button></div>
<div id="chatPage"></div>
@include('assistant.config')
</div>
@endsection
@push('scripts')
<script src="{{ asset('sys-ia/chat-config.js') }}"></script>
@endpush
