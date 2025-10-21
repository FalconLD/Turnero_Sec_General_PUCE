@extends('adminlte::page')

@section('title', 'pagina_asignación')

@section('content_header')
    <h1>Crear asignación</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card-body">    
            <form action="{{ route('asignacion.store') }}" method="POST">
                @csrf

                <!-- Select de Cubiculo -->
                <div class="mb-3">
                    <label for="cubiculo_id" class="form-label">Cubículo</label>
                    <select name="cubiculo_id" id="cubiculo_id" class="form-control" required>
                        <option value="">-- Selecciona un cubículo --</option>
                        @foreach($cubiculos as $cubiculo)
                            <option value="{{ $cubiculo->id }}">{{ $cubiculo->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Select de Form -->
                <div class="mb-3">
                    <label for="form_id" class="form-label">Formulario</label>
                    <select name="form_id" id="form_id" class="form-control" required>
                        <option value="">-- Selecciona un formulario --</option>
                        @foreach($forms as $form)
                            <option value="{{ $form->id }}">{{ $form->title }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- 
                <!-- Fecha de actualización -->
                <div class="mb-3">
                    <label for="fecha_actualizacion" class="form-label">Fecha de actualización</label>
                    <input type="date" name="fecha_actualizacion" id="fecha_actualizacion" class="form-control" value="{{ old('fecha_actualizacion') }}">
                </div>
                --}}
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="{{ route('asignacion.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
    
</div>

@stop


@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')

@stop