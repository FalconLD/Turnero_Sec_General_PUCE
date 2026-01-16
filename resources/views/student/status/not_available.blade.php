@extends('layouts.app')

@section('title', 'Registro no disponible')
@section('layout_topnav', true)
@section('content_header')
    <h1 class="text-center text-danger fw-bold mt-4">🚫 Registro no disponible</h1>
@stop

@section('content')
<div class="container mt-5">
    <div class="alert alert-warning text-center shadow p-5 rounded-4">
        <h4 class="fw-bold mb-3">Aún no se encuentra habilitado el registro de estudiantes</h4>
        <p>El formulario estará disponible a partir del <strong>{{ $startDate }}</strong>.</p>
        <p class="text-muted mb-0">Por favor, vuelve a ingresar en la fecha indicada.</p>
    </div>
</div>
@stop
