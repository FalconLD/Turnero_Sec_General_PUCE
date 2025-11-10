@extends('adminlte::page')

@section('title', 'Nuevo Horario')

@section('content_header')
    <h1 class="text-center">Crear Nuevo Horario</h1>
@stop

@section('content')
<div class="container">
    {{-- Botón para Volver --}}
    <div class="mb-3 w-75 mx-auto">
        <a href="{{ route('schedules.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Regresar a Horarios
        </a>
    </div>

    <div class="card shadow w-75 mx-auto">
        <div class="card-body">
            {{-- Mostrar errores de validación --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('schedules.store') }}" method="POST" id="scheduleForm">
                @csrf

                {{-- Horario --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_time">Hora de Inicio</label>
                        <input type="time" name="start_time" class="form-control" required value="{{ old('start_time') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_time">Hora de Fin</label>
                        <input type="time" name="end_time" class="form-control" required value="{{ old('end_time') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="attention_minutes">Duración Atención (min)</label>
                        <input type="number" name="attention_minutes" class="form-control" min="1" required value="{{ old('attention_minutes', 1) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="break_minutes">Duración del Descanso (min)</label>
                        <input type="number" name="break_minutes" class="form-control" min="0" required value="{{ old('break_minutes', 0) }}">
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="valid_from">Válido desde</label>
                        <input 
                            type="date" 
                            name="valid_from" 
                            id="valid_from" 
                            class="form-control" 
                            required 
                            value="{{ old('valid_from', now()->toDateString()) }}">
                    </div>
                </div>


                {{-- Cubículos --}}
                <div class="mb-3">
                    <label for="cubicles">Cubículos</label>
                    <select name="cubicles[]" class="form-control" multiple required>
                        @foreach($cubicles as $cubicle)
                            <option value="{{ $cubicle->id }}" {{ (collect(old('cubicles'))->contains($cubicle->id)) ? 'selected' : '' }}>
                                {{ $cubicle->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Mantenga presionada la tecla Ctrl (Cmd en Mac) para seleccionar múltiples cubículos.</small>
                </div>

                {{-- Breaks dinámicos --}}
                <div class="mb-3">
                    <label>Breaks</label>
                    <div id="breaksContainer">
                        <div class="row break-row mb-2">
                            <div class="col-md-5">
                                <input type="time" name="breaks[0][start]" class="form-control" placeholder="Inicio break">
                            </div>
                            <div class="col-md-5">
                                <input type="time" name="breaks[0][end]" class="form-control" placeholder="Fin break">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-remove-break">Eliminar</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="addBreak" class="btn btn-secondary btn-sm mt-2">Agregar Break</button>
                </div>

                {{-- Botones --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Horario</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Scripts para breaks dinámicos --}}
@section('js')
<script>
    let breakIndex = 1;

    document.getElementById('addBreak').addEventListener('click', function () {
        const container = document.getElementById('breaksContainer');
        const row = document.createElement('div');
        row.classList.add('row', 'break-row', 'mb-2');
        row.innerHTML = `
            <div class="col-md-5">
                <input type="time" name="breaks[${breakIndex}][start]" class="form-control" placeholder="Inicio break">
            </div>
            <div class="col-md-5">
                <input type="time" name="breaks[${breakIndex}][end]" class="form-control" placeholder="Fin break">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-remove-break">Eliminar</button>
            </div>
        `;
        container.appendChild(row);
        breakIndex++;
    });

    document.addEventListener('click', function(e) {
        if(e.target && e.target.classList.contains('btn-remove-break')) {
            e.target.closest('.break-row').remove();
        }
    });
</script>
@stop

@endsection
