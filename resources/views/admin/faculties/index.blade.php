@extends('adminlte::page')

@section('title', 'Facultades')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Listado de Facultades</h1>
        {{-- Botón de creación protegido --}}
        @can('facultades.crear')
            <a href="{{ route('faculties.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Facultad
            </a>
        @endcan
    </div>
@stop

@section('content')
    @if (session('info'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>ID</th>
                        <th>Nombre de Facultad</th>
                        <th>Descripción Programa</th>
                        <th>Nivel</th>
                        <th width="150px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($faculties as $faculty)
                        <tr>
                            <td>{{ $faculty->id }}</td>
                            <td>{{ $faculty->facultad }}</td>
                            <td>{{ $faculty->programa_desc }}</td>
                            <td>{{ $faculty->nivel }}</td>
                            <td>
                                <div class="btn-group">
                                    @can('facultades.editar')
                                        <a href="{{ route('faculties.edit', $faculty) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('facultades.eliminar')
                                        <form action="{{ route('faculties.destroy', $faculty) }}" method="POST" style="display:inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta facultad?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
