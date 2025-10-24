@extends('adminlte::page')

@section('title', 'Términos y Condiciones')

@section('content_header')
    <h1>Matrículas</h1>
@stop

@section('content')
<div class="card w-75 mx-auto mt-4">
  <div class="card-body">
    <h5 class="font-weight-bold">Términos y Condiciones</h5>
    <p>{{ $terminos->descripcion ?? 'No se encontraron términos.' }}</p>

    <form action="{{ route('student.accept.terms') }}" method="POST">
      @csrf
      <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="acepta_terminos" name="acepta_terminos" required>
        <label class="form-check-label" for="acepta_terminos">
          Acepto los términos y condiciones
        </label>
      </div>

      <div class="d-flex justify-content-between">
        <a href="#" class="btn btn-secondary">Regresar</a>
        <button type="submit" class="btn btn-primary">Siguiente</button>
      </div>
    </form>
  </div>
</div>
@stop
