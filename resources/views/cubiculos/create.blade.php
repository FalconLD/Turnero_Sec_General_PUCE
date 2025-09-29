@extends('adminlte::page')

@section('title', 'Nuevo Cubículo')

@section('content_header')
    <h1>Crear Cubículo</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('cubiculos.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="nombre">Nombre del Cubículo</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="tipo_atencion">Tipo de Atención</label>
                    <select name="tipo_atencion" class="form-control" required>
                        <option value="virtual">Virtual</option>
                        <option value="presencial">Presencial</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="user_id">Usuario Asignado</label>
                    <select name="user_id" class="form-control" required>
                        <option value="">-- Seleccionar Usuario --</option>
                        @foreach ($users as $users)
                            <option value="{{ $users->id }}">{{ $users->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="{{ route('cubiculos.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
@stop
