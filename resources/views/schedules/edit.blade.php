@extends('adminlte::page')

@section('title', 'Editar Horario')

@section('content_header')
    <h1>Editar Horario - Paso 1</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            {{-- El formulario enviará los datos al método 'update' --}}
            <form action="{{ route('schedules.update', $schedule) }}" method="POST">
                @csrf
                @method('PUT') {{-- Directiva para indicar que es una actualización --}}

                <div class="row">
                    {{-- Cubículo --}}
                    <div class="form-group col-md-6">
                        <label for="cubiculo_id">Cubículo</label>
                        <select name="cubiculo_id" id="cubiculo_id" class="form-control @error('cubiculo_id') is-invalid @enderror">
                            <option value="">Seleccione un cubículo</option>
                            @foreach($cubiculos as $cubiculo)
                                <option value="{{ $cubiculo->id }}" {{ old('cubiculo_id', $schedule->cubiculo_id) == $cubiculo->id ? 'selected' : '' }}>
                                    {{ $cubiculo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('cubiculo_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Duración de la Atención --}}
                    <div class="form-group col-md-6">
                        <label for="atencion">Duración de la Atención (minutos)</label>
                        <input type="number" name="atencion" id="atencion" class="form-control @error('atencion') is-invalid @enderror" value="{{ old('atencion', $schedule->atencion) }}" min="1">
                        @error('atencion')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Hora de Inicio --}}
                    <div class="form-group col-md-6">
                        <label for="hora_inicio">Hora de Inicio</label>
                        <input type="time" name="hora_inicio" id="hora_inicio" class="form-control @error('hora_inicio') is-invalid @enderror" value="{{ old('hora_inicio', \Carbon\Carbon::parse($schedule->hora_inicio)->format('H:i')) }}">
                        @error('hora_inicio')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Hora de Fin --}}
                    <div class="form-group col-md-6">
                        <label for="hora_fin">Hora de Fin</label>
                        <input type="time" name="hora_fin" id="hora_fin" class="form-control @error('hora_fin') is-invalid @enderror" value="{{ old('hora_fin', \Carbon\Carbon::parse($schedule->hora_fin)->format('H:i')) }}">
                        @error('hora_fin')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Vigencia Desde --}}
                    <div class="form-group col-md-6">
                        <label for="vigencia_desde">Vigencia Desde</label>
                        <input type="date" name="vigencia_desde" id="vigencia_desde" class="form-control @error('vigencia_desde') is-invalid @enderror" value="{{ old('vigencia_desde', $schedule->vigencia_desde->format('Y-m-d')) }}">
                        @error('vigencia_desde')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Vigencia Hasta --}}
                    <div class="form-group col-md-6">
                        <label for="vigencia_hasta">Vigencia Hasta</label>
                        <input type="date" name="vigencia_hasta" id="vigencia_hasta" class="form-control @error('vigencia_hasta') is-invalid @enderror" value="{{ old('vigencia_hasta', $schedule->vigencia_hasta->format('Y-m-d')) }}">
                        @error('vigencia_hasta')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <input type="hidden" name="descanso" value="0">

                <hr>
                
                <h4>Pausas Programadas</h4>
                    <div id="pausas-container">
                        {{-- Cargamos las pausas existentes --}}
                        @foreach(old('pausas', $schedule->pauses->toArray()) as $index => $pausa)
                            <div class="row align-items-center mb-2">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Inicio Pausa</label>
                                        {{-- CORRECCIÓN: Formatear la hora y añadir manejo de errores --}}
                                        <input type="time" name="pausas[{{ $index }}][hora_inicio]" class="form-control @error('pausas.'.$index.'.hora_inicio') is-invalid @enderror" value="{{ old('pausas.'.$index.'.hora_inicio', \Carbon\Carbon::parse($pausa['hora_inicio'])->format('H:i')) }}">
                                        @error('pausas.'.$index.'.hora_inicio')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Fin Pausa</label>
                                        {{-- CORRECCIÓN: Formatear la hora y añadir manejo de errores --}}
                                        <input type="time" name="pausas[{{ $index }}][hora_fin]" class="form-control @error('pausas.'.$index.'.hora_fin') is-invalid @enderror" value="{{ old('pausas.'.$index.'.hora_fin', \Carbon\Carbon::parse($pausa['hora_fin'])->format('H:i')) }}">
                                        @error('pausas.'.$index.'.hora_fin')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger remove-pausa" style="margin-top: 15px;">Eliminar</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-pausa" class="btn btn-secondary mt-2">Agregar Pausa</button>

                    <hr>

                    <button type="submit" class="btn btn-primary">Actualizar y Seleccionar Días</button>
                    <a href="{{ route('schedules.index') }}" class="btn btn-danger">Cancelar</a>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('pausas-container');
            const addButton = document.getElementById('add-pausa');
            // Empezamos el índice después de las pausas ya existentes
            let pausaIndex = {{ count(old('pausas', $schedule->pauses->toArray())) }};

            addButton.addEventListener('click', function () {
                const pausaDiv = document.createElement('div');
                pausaDiv.classList.add('row', 'align-items-center', 'mb-2');
                
                pausaDiv.innerHTML = `
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Inicio Pausa</label>
                            <input type="time" name="pausas[${pausaIndex}][hora_inicio]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Fin Pausa</label>
                            <input type="time" name="pausas[${pausaIndex}][hora_fin]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-pausa" style="margin-top: 15px;">Eliminar</button>
                    </div>
                `;
                
                container.appendChild(pausaDiv);
                pausaIndex++;
            });

            container.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-pausa')) {
                    e.target.closest('.row').remove();
                }
            });
        });
    </script>
@stop
