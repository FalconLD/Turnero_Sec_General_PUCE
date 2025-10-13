@extends('adminlte::page')

@section('title', 'Formularios')

@section('content_header')
    <h1 class="text-center">Sección Encuestas</h1>
@stop

@section('content')
  <div class="card">
    <div class="card-body">
      {{-- <h5 class="card-title">Formulario con Checkbox</h5>

      {{-- Checkbox --}}
      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="activarCampos">
        <label class="form-check-label" for="activarCampos">
          Activar campos adicionales
        </label>
      </div>

      {{-- Select (deshabilitado al inicio) --}}
      <div class="mb-3">
        <label for="opcion" class="form-label">Seleccione una opción</label>
        <select class="form-select" id="opcion" disabled>
          <option selected disabled>-- Seleccione --</option>
          <option value="nuevo">Nuevo</option>
          <option value="1">Opción 1</option>
          <option value="2">Opción 2</option>
          
        </select>
      </div>

      {{-- Input de texto (deshabilitado al inicio) --}}
      <div class="mb-3">
        <label for="textoExtra" class="form-label">Texto adicional</label>
        <input type="text" class="form-control" id="textoExtra" placeholder="Ingrese algo..." disabled>
      </div>

      {{-- Botón --}}
      <button type="button" class="btn btn-primary">Guardar</button>
    </div>
  </div>

@endsection
{{-- Script para controlar el checkbox y select --}}
@section('js')
<script>
  const checkbox = document.getElementById('activarCampos');
  const select = document.getElementById('opcion');
  const input = document.getElementById('textoExtra');

  // Controlar activación del select con el checkbox
  checkbox.addEventListener('change', function() {
    select.disabled = !this.checked;
    input.disabled = true; // siempre lo bloqueamos al inicio
    input.value = ""; // limpiar input al desactivar
  });

  // Controlar input según la opción del select
  select.addEventListener('change', function() {
    if (this.value === "nuevo") {
      input.disabled = false;
    } else {
      input.disabled = true;
      input.value = ""; // limpiar si no es "nuevo"
    }
  });
</script>
@endsection
