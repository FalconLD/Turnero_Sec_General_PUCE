@extends('adminlte::page')

@section('title', 'Encuesta')

@section('content_header')
    <h1>Configuración de encuesta</h1>
@stop

@section('content')
<div class="card w-75 mb-3">
  <div class="card-body">

 {{-- Select --}}
    <div class="mb-3">
      <label for="opcion" class="form-label">Seleccione una opción</label>
      <select class="form-select" id="opcion">
        <option selected disabled>-- Seleccione --</option>
        <option value="1">Opción 1</option>
        <option value="2">Opción 2</option>
        <option value="3">Opción 3</option>
      </select>
    </div>

     {{-- Campo de texto --}}
    <div class="mb-3">
      <label for="nombre" class="form-label">Nombre</label>
      <input type="text" class="form-control" id="nombre" placeholder="Ingrese su nombre">
    </div>
    <h5 class="card-title">Card title</h5>
    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
    <a href="#" class="btn btn-primary">Button</a>
  </div>
</div>

@stop