@if(auth()->check() && auth()->user()->role === 'admin')
<link rel="stylesheet" href="{{ asset('sys-ia/sys-ia.css') }}?v={{ filemtime(public_path('sys-ia/sys-ia.css')) }}">
<script>window.CSRF=@json(csrf_token());window.API_IA=@json(route('assistant.api'));window.API_CHAT=@json(route('chatbot.api'));window.MONEDA='S/';</script>
<script src="{{ asset('sys-ia/common.js') }}"></script>
<script src="{{ asset('sys-ia/chat.js') }}"></script>
@endif
