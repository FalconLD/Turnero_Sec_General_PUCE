@extends('adminlte::page')

@section('title', 'Editar Cubículo')

@section('content_header')
    <h1 class="text-center">Editar Cubículo Virtual</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        {{-- El parámetro 'modulo' debe coincidir con la definición en web.php --}}
                        <form action="{{ route('cubiculos.update', ['modulo' => $cubiculo->id]) }}" method="POST" id="cubiculo-form">
                            @csrf
                            @method('PUT')

                            {{-- Forzamos la modalidad virtual de forma interna --}}
                            <input type="hidden" name="tipo_atencion" value="virtual">

                            <div class="row">
                                {{-- Columna Izquierda: Gestión de Responsable y Área --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="user_id">Usuario Asignado <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user-check"></i></span>
                                            </div>
                                            <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                                <option value="">-- Seleccionar Usuario --</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ old('user_id', $cubiculo->user_id) == $user->id ? 'selected' : '' }}
                                                        data-areas='{{ $user->operatingAreas->map(function($a){ return ["id" => $a->id, "name" => $a->name]; })->toJson() }}'>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('user_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="operating_area_id">Área de Atención <span class="text-danger">*</span></label>
                                        <select name="operating_area_id" id="operating_area_id" class="form-control @error('operating_area_id') is-invalid @enderror" required>
                                            <option value="">-- Seleccionar Área --</option>
                                            {{-- Se llena vía JS al cargar o cambiar usuario --}}
                                        </select>
                                        @error('operating_area_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Columna Derecha: Identificación del Cubículo --}}
                                <div class="col-md-6">
                                    <div class="cubiculo-name-container">
                                        <h6 class="font-weight-bold text-center" style="margin-bottom: 0.9rem;">Identificador del Cubículo</h6>
                                        <div class="row">
                                            @php
                                                // Separamos el nombre actual para llenar los campos (ej: "C - 001")
                                                $parts = explode(' - ', $cubiculo->nombre);
                                                $prefijoActual = isset($parts[0]) ? $parts[0] . ' -' : 'C -';
                                                $numeroActual = $parts[1] ?? '';
                                            @endphp
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Prefijo</label>
                                                    <select name="prefijo" class="form-control" required>
                                                        <option value="C -" {{ old('prefijo', $prefijoActual) == 'C -' ? 'selected' : '' }}>C -</option>
                                                        <option value="P -" {{ old('prefijo', $prefijoActual) == 'P -' ? 'selected' : '' }}>P -</option>
                                                        <option value="SALA -" {{ old('prefijo', $prefijoActual) == 'SALA -' ? 'selected' : '' }}>SALA -</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Número</label>
                                                    <input type="text" name="numero" id="numero" class="form-control"
                                                        value="{{ old('numero', $numeroActual) }}"
                                                        placeholder="001" maxlength="3" pattern="[0-9]{3}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            {{-- Fila del Enlace: Siempre visible y obligatorio --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="enlace_o_ubicacion">Enlace de Reunión Virtual (Teams, Zoom, Meet) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-video text-primary"></i></span>
                                            </div>
                                            <input type="url" name="enlace_o_ubicacion" id="enlace_o_ubicacion"
                                                class="form-control @error('enlace_o_ubicacion') is-invalid @enderror"
                                                value="{{ old('enlace_o_ubicacion', $cubiculo->enlace_o_ubicacion) }}"
                                                placeholder="https://teams.microsoft.com/l/meetup-join/..." required>
                                        </div>
                                        @error('enlace_o_ubicacion')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="text-right mt-3">
                                <a href="{{ route('cubiculos.index') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary px-4">Actualizar Cubículo</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('css')
    <style>
        .cubiculo-name-container {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            padding: 1rem;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
    </style>
@endpush

@push('js')
    <script>
        $(document).ready(function() {
            const userSelect = $('#user_id');
            const areaSelect = $('#operating_area_id');
            const areaActual = "{{ $cubiculo->operating_area_id }}";

            // 1. Lógica de filtrado de áreas por usuario
            function cargarAreas() {
                const selectedOption = userSelect.find('option:selected');
                const areasData = selectedOption.data('areas');

                areaSelect.empty().append('<option value="">-- Seleccionar Área --</option>');

                if (areasData && areasData.length > 0) {
                    areasData.forEach(function(area) {
                        const isSelected = (area.id == areaActual) ? 'selected' : '';
                        areaSelect.append(`<option value="${area.id}" ${isSelected}>${area.name}</option>`);
                    });
                }
            }

            // Ejecución inicial y al cambiar usuario
            cargarAreas();
            userSelect.on('change', cargarAreas);

            // 2. Validación estricta del campo número (solo 3 dígitos)
            const inputNumero = $('#numero');
            inputNumero.on('keydown', function(e) {
                if (!['Backspace', 'Tab', 'Delete', 'ArrowLeft', 'ArrowRight'].includes(e.key) && !/^[0-9]$/.test(e.key)) {
                    e.preventDefault();
                }
            });

            inputNumero.on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
@endpush
