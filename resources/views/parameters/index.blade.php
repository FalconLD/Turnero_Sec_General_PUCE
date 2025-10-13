
@extends('adminlte::page')

@section('title', 'Formularios')

@section('content_header')
    <h1 class="text-center">Sección Parametros</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('parameters.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table caption-top">
                <thead>
                    <tr>
                        <th scope="col" class="table-primary">Clave</th>
                        <th scope="col" class="table-primary">Descripción</th>
                        <th scope="col" class="table-primary">Parámetro</th>
                        <th scope="col" class="table-primary">Creado</th>
                        <th scope="col" class="table-primary">Actualizado</th>
                        <th scope="col" class="table-primary">Acciones</th>
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
                                <a href="{{ route('parameters.edit', $param) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('parameters.destroy', $param) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este parámetro?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
</div>
@endsection
