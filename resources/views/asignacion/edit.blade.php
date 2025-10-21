@extends('adminlte::page')

@section('title', 'Editar Pagina Asignación')

@section('content_header')
    
@stop


@section('content')
<div class="container">
    <h1>Editar Asignación</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('asignacion.update', $asignacion->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Select de Cubiculo -->
        <div class="mb-3">
            <label for="cubiculo_id" class="form-label">Cubículo</label>
            <select name="cubiculo_id" id="cubiculo_id" class="form-control" required>
                <option value="">-- Selecciona un cubículo --</option>
                @foreach($cubiculos as $cubiculo)
                    <option value="{{ $cubiculo->id }}" 
                        {{ $asignacion->cubiculo_id == $cubiculo->id ? 'selected' : '' }}>
                        {{ $cubiculo->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Select de Form -->
        <div class="mb-3">
            <label for="form_id" class="form-label">Formulario</label>
            <select name="form_id" id="form_id" class="form-control" required>
                <option value="">-- Selecciona un formulario --</option>
                @foreach($forms as $form)
                    <option value="{{ $form->id }}" 
                        {{ $asignacion->form_id == $form->id ? 'selected' : '' }}>
                        {{ $form->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Fecha de actualización -->
{{-- 
<div class="mb-3">
    <label for="fecha_actualizacion" class="form-label">Fecha de actualización</label>
    <input type="date" name="fecha_actualizacion" id="fecha_actualizacion" 
           class="form-control" 
           value="{{ old('fecha_actualizacion', $asignacion->fecha_actualizacion ? $asignacion->fecha_actualizacion->format('Y-m-d') : '') }}">
</div>
--}}

        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="{{ route('asignacion.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')

@stop