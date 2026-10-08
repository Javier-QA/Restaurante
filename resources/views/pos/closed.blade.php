@extends('layouts.app')
@section('content')
<div class="card p-4 text-center mx-auto" style="max-width:600px">
    <i class="bi bi-lock fs-1 text-warning" aria-hidden="true"></i>
    <h2 class="h4 mt-3">La caja está cerrada</h2>
    <p>Solicita al administrador o cajero que abra un turno para registrar pedidos y cobros.</p>
    <a href="{{ route('pos.index') }}" class="btn btn-primary">Comprobar apertura de caja</a>
</div>
@endsection
