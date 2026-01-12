@extends('adminlte::page')

@section('title', 'Gestionar Áreas')

@section('content_header')
    <h1 class="text-center">Configurar Áreas de Atención</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-11">
            {{-- Encabezado de Usuario --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 1rem;">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 45px; height: 45px;">
                        <i class="fas fa-user-tie text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 font-weight-bold">{{ $user->name }}</h5>
                        <p class="text-muted mb-0 small">{{ $user->email }} | DNI: {{ $user->DNI ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('assignments.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    @foreach($areas->groupBy('faculty_id') as $facultyId => $group)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm" style="border-radius: 1rem;">
                                <div class="card-header bg-white py-3" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem; border-bottom: 1px solid #f4f4f4;">
                                    <h6 class="mb-0 font-weight-bold text-dark text-uppercase" style="letter-spacing: 1px;">
                                        <i class="fas fa-university text-primary mr-2"></i>
                                        {{ $group->first()->faculty->facultad }}
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    @foreach($group as $area)
                                        {{-- USAMOS FLEXBOX PARA ALINEACIÓN PERFECTA --}}
                                        <div class="d-flex align-items-center border-bottom px-4 py-3 switch-row">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox"
                                                       name="areas[]"
                                                       value="{{ $area->id }}"
                                                       class="custom-control-input"
                                                       id="area_{{ $area->id }}"
                                                       {{ $user->operatingAreas->contains($area->id) ? 'checked' : '' }}>
                                                <label class="custom-control-label font-weight-normal text-secondary"
                                                       for="area_{{ $area->id }}"
                                                       style="cursor: pointer; padding-left: 10px; font-size: 1rem; line-height: 1.2;">
                                                    {{ $area->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-4 mb-5">
                    <a href="{{ route('assignments.index') }}" class="btn btn-default rounded-pill px-5 mr-3 shadow-sm border">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-success rounded-pill px-5 shadow">
                        <i class="fas fa-save mr-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    /* 1. Mantenemos tus bordes redondeados del proyecto */
    .card { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important; }

    /* 2. Corregimos el switch nativo de Bootstrap para que no se desfase */
    .custom-switch .custom-control-label::before {
        left: -2.25rem;
        width: 1.75rem; /* Tamaño estándar balanceado */
        height: 1rem;
        border-radius: 1rem;
    }

    .custom-switch .custom-control-label::after {
        left: calc(-2.25rem + 2px);
        width: calc(1rem - 4px);
        height: calc(1rem - 4px);
        border-radius: 1rem;
    }

    /* 3. Color verde de éxito cuando está activo */
    .custom-control-input:checked ~ .custom-control-label::before {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }

    /* 4. Alineación del texto con el switch */
    .custom-control-label::before,
    .custom-control-label::after {
        top: 0.1rem; /* Ajuste milimétrico para el eje medio */
    }

    /* 5. Efecto de fila profesional */
    .switch-row:hover {
        background-color: #fbfbfb;
        transition: 0.2s;
    }
</style>
@stop
