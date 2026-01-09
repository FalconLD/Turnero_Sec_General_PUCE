@extends('adminlte::page')

@section('title', 'Editar Área Operativa')

@section('content_header')
    <h1 class="text-center">Editar Área Operativa</h1>
@stop

@section('content')
<div class="container-fluid pt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        Modificar: {{ $operatingArea->name }}
                    </h3>
                </div>
                <div class="card-body p-4">
                    {{-- IMPORTANTE: La ruta lleva el ID y el método es PUT --}}
                    <form action="{{ route('operating-areas.update', $operatingArea->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-4">
                            <label class="form-label">Nombre del Área</label>
                            <input type="text" name="name"
                                   class="form-control rounded-pill px-4"
                                   value="{{ old('name', $operatingArea->name) }}"
                                   required>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">Facultad</label>
                            <select name="faculty_id" class="form-control rounded-pill px-4" required>
                                <option value="">Seleccione una facultad...</option>
                                @foreach($faculties as $fac)
                                    <option value="{{ $fac->id }}"
                                        {{ $operatingArea->faculty_id == $fac->id ? 'selected' : '' }}>
                                        {{ $fac->facultad }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-center mt-5">
                            <a href="{{ route('operating-areas.index') }}" class="btn btn-light rounded-pill px-5 border mr-2">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow">
                                <i class="fas fa-save mr-2"></i> Guardar Cambios
                            </button>
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
    /* Mantenemos tu estética de bordes redondeados y sombras suaves */
    .card {
        border-radius: 1.5rem !important;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
    .card-header {
        background-color: transparent;
        border-bottom: 1px solid #f0f0f0;
    }
    .form-control {
        height: 45px;
        border: 1px solid #dee2e6;
    }
    .form-control:focus {
        box-shadow: none;
        border-color: #00a0df;
    }
</style>
@stopx1
