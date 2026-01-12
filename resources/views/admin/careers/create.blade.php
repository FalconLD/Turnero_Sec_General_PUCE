@extends('adminlte::page')

@section('title', 'Crear Carrera')

@section('content_header')
    <h1>Registrar Nueva Carrera</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('careers.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="operating_area_id">Área Operativa / de Atención</label>
                    <select name="operating_area_id" id="operating_area_id" class="form-control" required>
                        <option value="" disabled selected>-- Seleccione el Área --</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="career_code">Código de Carrera (Opcional)</label>
                    <input type="text" name="career_code" id="career_code" class="form-control" placeholder="Ej: Q343 o 'No existe programa'">
                </div>

                <div class="form-group">
                    <label for="name">Nombre de la Carrera</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar Carrera</button>
                    <a href="{{ route('careers.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@stop
