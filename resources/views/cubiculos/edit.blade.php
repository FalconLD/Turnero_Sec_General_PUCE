@extends('adminlte::page')

@section('title', 'Editar Cubículo')

@section('content_header')
    <h1>Editar Cubículo</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('cubiculos.update', $cubiculo) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="nombre">Nombre del Cubículo</label>
                    <input type="text" name="nombre" class="form-control" value="{{ $cubiculo->nombre }}" required>
                </div>

                <div class="form-group">
                    <label for="tipo_atencion">Tipo de Atención</label>
                    <select name="tipo_atencion" class="form-control" required>
                        <option value="virtual" {{ $cubiculo->tipo_atencion == 'virtual' ? 'selected' : '' }}>Virtual</option>
                        <option value="presencial" {{ $cubiculo->tipo_atencion == 'presencial' ? 'selected' : '' }}>Presencial</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="user_id">Usuario Asignado</label>
                    <select name="user_id" class="form-control" required>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ $cubiculo->user_id == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Actualizar</button>
                <a href="{{ route('cubiculos.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
@stop
