@extends('adminlte::page')

@section('title', 'Nuevo Cubículo')

@section('content_header')
    <h1 class="text-center">Crear Nuevo Cubículo Virtual</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('cubiculos.store') }}" method="POST" id="create-cubiculo-form">
                            @csrf

                            {{-- Modalidad forzada a Virtual (Invisible para el usuario) --}}
                            <input type="hidden" name="tipo_atencion" value="virtual">

                            {{-- Fila 1: Usuario Asignado --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="user_id">Usuario Responsable <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user-check"></i></span>
                                            </div>
                                            <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                                <option value="">-- Seleccionar Operador --</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}" data-areas='{{ $user->operatingAreas->map(function($a){ return ["id" => $a->id, "name" => $a->name]; })->toJson() }}'>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('user_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Fila 2: Área y Nombre del Cubículo --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="operating_area_id">Área de Atención <span class="text-danger">*</span></label>
                                        <select name="operating_area_id" id="operating_area_id" class="form-control @error('operating_area_id') is-invalid @enderror" required>
                                            <option value="">-- Seleccione un usuario primero --</option>
                                        </select>
                                        @error('operating_area_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="cubiculo-name-container">
                                        <h6 class="font-weight-bold text-center">Identificador</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select name="prefijo" class="form-control" required>
                                                    <option value="C -">C -</option>
                                                    <option value="P -">P -</option>
                                                    <option value="SALA -">SALA -</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" name="numero" class="form-control" placeholder="001" maxlength="3" pattern="[0-9]{3}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Fila 3: Configuración del Enlace --}}
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="enlace_o_ubicacion">Enlace de Reunión (Teams / Zoom / Meet) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-link"></i></span>
                                            </div>
                                            <input type="url" name="enlace_o_ubicacion" class="form-control @error('enlace_o_ubicacion') is-invalid @enderror"
                                                   placeholder="https://teams.microsoft.com/..." required value="{{ old('enlace_o_ubicacion') }}">
                                        </div>
                                        @error('enlace_o_ubicacion')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="text-right mt-4">
                                <a href="{{ route('cubiculos.index') }}" class="btn btn-danger">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Guardar Cubículo</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .cubiculo-name-container {
        background-color: #f4f6f9;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        padding: 0.8rem;
    }
    .card { border-radius: 1rem; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        const userSelect = $('#user_id');
        const areaSelect = $('#operating_area_id');

        userSelect.on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const areasData = selectedOption.data('areas');

            areaSelect.empty().append('<option value="">-- Seleccionar Área --</option>');

            if (areasData && areasData.length > 0) {
                areasData.forEach(function(area) {
                    areaSelect.append(`<option value="${area.id}">${area.name}</option>`);
                });
            } else if ($(this).val() !== "") {
                areaSelect.append('<option value="" disabled>Sin áreas asignadas</option>');
            }
        });
    });
</script>
@stop
