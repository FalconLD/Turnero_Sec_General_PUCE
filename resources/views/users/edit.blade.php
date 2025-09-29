@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
    <h1>Editar Usuario</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('users.update', $usuario) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" name="name" class="form-control" value="{{ $usuario->name }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" value="{{ $usuario->email }}" required>
                </div>

                <div class="form-group">
                    <label for="DNI">DNI</label>
                    <input type="text" name="DNI" class="form-control" value="{{ $usuario->DNI }}">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña (dejar en blanco si no desea cambiar)</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <button type="submit" class="btn btn-success">Actualizar</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
@stop
