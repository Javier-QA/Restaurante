@extends('layouts.app')
@section('content')
<div class="card p-4 mx-auto" style="max-width:720px">
    <h2 class="h4">{{ isset($client) ? 'Editar cliente' : 'Registrar cliente' }}</h2>
    <form method="POST" action="{{ isset($client) ? route('clients.update', $client) : route('clients.store') }}">
        @csrf
        @if(isset($client)) @method('PUT') @endif
        @foreach(['name' => 'Nombre / Razón social', 'document_number' => 'DNI / RUC', 'phone' => 'Teléfono', 'email' => 'Correo', 'address' => 'Dirección'] as $field => $label)
            <div class="mb-3">
                <label for="client_{{ $field }}" class="form-label">{{ $label }}</label>
                <input id="client_{{ $field }}" name="{{ $field }}" class="form-control @error($field) is-invalid @enderror"
                    type="{{ $field === 'email' ? 'email' : 'text' }}" value="{{ old($field, $client->$field ?? '') }}"
                    maxlength="{{ $field === 'document_number' ? 11 : ($field === 'phone' ? 30 : 255) }}" @required($field === 'name')>
                @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        @endforeach
        <button class="btn btn-primary">Guardar cliente</button>
        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">Volver</a>
    </form>
</div>
@endsection
