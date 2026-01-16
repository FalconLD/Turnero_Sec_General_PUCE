@extends('adminlte::page')

@section('title', 'Editar Horario')

@section('content_header')
    <h1 class="text-center">Editar Horario</h1>
@stop

@section('content')

<td class="text-nowrap">
    @can('horarios.editar')
        @php
            // Verificamos si el usuario actual es el asignado a alguno de los cubículos de este horario
            $esDuenio = $schedule->cubicles->contains('user_id', auth()->id());
            // Opcional: permitir si pertenece a su misma facultad/área
            $misAreas = auth()->user()->operatingAreas->pluck('id')->toArray();
            $esMismaArea = $schedule->cubicles->whereIn('operating_area_id', $misAreas)->count() > 0;
        @endphp

        @if($esDuenio || $esMismaArea)
            <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-link text-primary btn-sm" title="Editar">
                <i class="fas fa-edit"></i>
            </a>
        @else
            <span class="text-muted small"><i class="fas fa-lock"></i> No asignado</span>
        @endif
    @endcan
</td>

<div class="container">
    <div class="card shadow w-75 mx-auto">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('schedules.update', $schedule->id_hor) }}" method="POST" id="scheduleForm">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_time">Hora de Inicio</label>
                        <input type="time" name="start_time" class="form-control" required value="{{ old('start_time', $schedule->start_time) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_time">Hora de Fin</label>
                        <input type="time" name="end_time" class="form-control" required value="{{ old('end_time', $schedule->end_time) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="attention_minutes">Duración Atención (min)</label>
                        <input type="number" name="attention_minutes" class="form-control" min="1" required value="{{ old('attention_minutes', $schedule->attention_minutes) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="break_minutes">Duración del Descanso (min)</label>
                        <input type="number" name="break_minutes" class="form-control" min="0" required value="{{ old('break_minutes', $schedule->break_minutes) }}">
                    </div>                    
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="valid_from">Válido desde</label>
                        <input 
                            type="date" 
                            name="valid_from" 
                            class="form-control" 
                            required 
                            value="{{ old('valid_from', \Carbon\Carbon::parse($schedule->valid_from)->format('Y-m-d')) }}">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="cubicles">Cubículos</label>
                    <select name="cubicles[]" class="form-control" multiple required>
                        @foreach($cubicles as $cubicle)
                            <option value="{{ $cubicle->id }}" {{ (collect(old('cubicles', $schedule->cubicles->pluck('id')))->contains($cubicle->id)) ? 'selected' : '' }}>
                                {{ $cubicle->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Mantenga presionada la tecla Ctrl (Cmd en Mac) para seleccionar múltiples cubículos.</small>
                </div>

                <div class="mb-3">
                    <label>Breaks</label>
                    <div id="breaksContainer">
                        @php
                            $breaks = old('breaks', $schedule->breaks->map(function ($break) {
                                return [
                                    'start' => \Carbon\Carbon::parse($break->start_break)->format('H:i'),
                                    'end' => \Carbon\Carbon::parse($break->end_break)->format('H:i'),
                                ];
                            })->toArray());
                        @endphp
                        @foreach($breaks as $index => $break)
                        <div class="row break-row mb-2">
                            <div class="col-md-5">
                                <input type="time" name="breaks[{{ $index }}][start]" class="form-control" placeholder="Inicio break" value="{{ $break['start'] ?? '' }}">
                            </div>
                            <div class="col-md-5">
                                <input type="time" name="breaks[{{ $index }}][end]" class="form-control" placeholder="Fin break" value="{{ $break['end'] ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-remove-break">Eliminar</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="addBreak" class="btn btn-secondary btn-sm mt-2">Agregar Break</button>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <div>
                        <button type="submit" class="btn btn-primary">Actualizar Horario</button>
                        <a href="{{ route('days.edit', ['schedule' => $schedule->id_hor]) }}" class="btn btn-info">Editar Días</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@section('js')
<script>
    let breakIndex = {{ count(old('breaks', $schedule->breaks->toArray())) }};

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