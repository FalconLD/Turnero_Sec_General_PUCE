@extends('adminlte::page')

@section('title', 'Horarios')

@section('content_header')
    <h1>Sección horarios</h1>
@stop


@section('content')
<div class="container">
    <h2>Lista de Parámetros</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('parameters.create') }}" class="btn btn-primary mb-3">Crear nuevo</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Clave</th>
                <th>Descripción</th>
                <th>Parámetro</th>
                <th>Creado</th>
                <th>Actualizado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($parameters as $param)
                <tr>
                    <td>{{ $param->clave }}</td>
                    <td>{{ $param->descripcion }}</td>
                    <td>{{ $param->parametro }}</td>
                    <td>{{ $param->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $param->updated_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('parameters.edit', $param) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('parameters.destroy', $param) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este parámetro?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
