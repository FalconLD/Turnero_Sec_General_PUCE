@extends('adminlte::page')

@section('layout_topnav', true)
@section('title', 'Registro de Estudiante')

@section('content_header')
<div class="text-center mb-4">
    <h2 class="fw-bold">Proceso de Matrícula</h2>
    <p class="text-muted">Sigue los pasos para completar tu registro</p>
</div>

{{-- Línea de tiempo de pasos --}}
<div class="steps d-flex justify-content-center mb-4">
    <div class="step active">1. Términos</div>
    <div class="step">2. Datos Personales</div>
    <div class="step">3. Datos Representante</div>
    <div class="step">4. Pago</div>
    <div class="step">5. Confirmación</div>
</div>

<style>
.steps .step {
    padding: 8px 15px;
    border-radius: 20px;
    margin: 0 5px;
    background: #dcdcdc;
    font-size: 14px;
}
.steps .step.active {
    background: #1a73e8;
    color: white;
    font-weight: bold;
}
</style>
@stop

@section('content')
<div class="container d-flex justify-content-center">
    <div class="card shadow-lg w-75 border-0">
        <div class="card-body">

            <h4 class="text-primary mb-3">Términos y Condiciones</h4>

            <div class="border p-3 rounded" style="max-height: 260px; overflow-y:auto;">
                {!! $terminos->valor !!} {{-- Contenido desde BD --}}
            </div>

            <div class="form-check mt-3">
                <input type="checkbox" class="form-check-input" id="aceptarTerminos">
                <label class="form-check-label" for="aceptarTerminos">
                    Acepto los términos y condiciones.
                </label>
            </div>

            <form method="POST" action="{{ route('student.accept.terms') }}" id="formTerminos">
                @csrf
                <button type="submit" id="btnSiguiente" class="btn btn-primary mt-4 w-100" disabled>
                    Siguiente →
                </button>
            </form>

        </div>
    </div>
</div>

<script>
// ✅ Activar botón solo cuando se marque el checkbox
document.getElementById('aceptarTerminos').addEventListener('change', function() {
    document.getElementById('btnSiguiente').disabled = !this.checked;
});
</script>

@stop
