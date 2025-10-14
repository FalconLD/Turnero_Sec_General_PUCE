@extends('adminlte::page')

@section('title', 'Encuesta')

@section('content_header')
    <h1 class="text-center mb-4" >Configuración de Encuesta</h1>
@stop

@section('content')
<div class="container w-50" style="background-color:#f8f9fa; padding:2rem; border-radius:1rem; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
  
  {{-- Checkbox --}}
  <div class="form-check form-switch mb-4">
    <input class="form-check-input" type="checkbox" id="activarCampos">
    <label class="form-check-label fw-semibold" for="activarCampos">
      Activar campos adicionales
    </label>
  </div>

  {{-- Select --}}
  <div class="mb-4">
    <label for="opcion" class="form-label fw-semibold">Seleccione una opción</label>
    <select class="form-select" id="opcion" disabled style="background-color:#e9ecef;">
      <option selected disabled>-- Seleccione --</option>
      <option value="nuevo">Nuevo</option>
      <option value="1">Opción 1</option>
      <option value="2">Opción 2</option>
    </select>
    <div class="form-text text-muted">
      Elija "Nuevo" para activar el campo de texto adicional.
    </div>
  </div>

  {{-- Input de texto --}}
  <div class="mb-4">
    <label for="textoExtra" class="form-label fw-semibold">Texto adicional</label>
    <input type="text" class="form-control" id="textoExtra" placeholder="Ingrese algo..." disabled style="background-color:#e9ecef;">
  </div>

  {{-- Botón --}}
  <div class="text-center">
    <button type="button" class="btn" style="background-color:#0dcaf0; color:white; font-weight:600; padding:0.5rem 2rem;">
      <i class="fas fa-save"></i> Guardar
    </button>
  </div>

</div>
@stop

@section('css')
<style>
  .form-check-input:checked {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
  }
  .form-label {
    color: #495057;
  }
  .form-text {
    color: #6c757d;
  }
</style>
@stop

@section('js')
<script>
  const checkbox = document.getElementById('activarCampos');
  const select = document.getElementById('opcion');
  const input = document.getElementById('textoExtra');

  checkbox.addEventListener('change', function() {
    select.disabled = !this.checked;
    input.disabled = true;
    input.value = "";
  });

  select.addEventListener('change', function() {
    if (this.value === "nuevo") {
      input.disabled = false;
    } else {
      input.disabled = true;
      input.value = "";
    }
  });
</script>
@stop
