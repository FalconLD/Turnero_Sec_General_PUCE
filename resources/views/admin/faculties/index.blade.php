@extends('adminlte::page')

@section('title', 'Facultades')

@section('content_header')
    <h1 class="text-center">Gestión de Facultades</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-11">

                @if (session('info'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('info') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Listado de Facultades</h3>
                        <div class="card-tools">
                            @can('facultades.crear')
                                <a href="{{ route('faculties.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus mr-1"></i> Nueva Facultad
                                </a>
                            @endcan
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            {{-- Agregamos la clase datatable-export para activar el JS global --}}
                            <table id="facultades" class="table datatable-export" data-page-title="Listado de Facultades">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Facultad</th>
                                        <th>Descripción Programa</th>
                                        <th>Nivel</th>
                                        @canany(['facultades.editar', 'facultades.eliminar'])
                                            <th class="text-center">Acciones</th>
                                        @endcanany
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($faculties as $faculty)
                                        <tr>
                                            <td>{{ $faculty->id }}</td>
                                            <td class="font-weight-bold">{{ $faculty->facultad }}</td>
                                            <td>{{ $faculty->programa_desc }}</td>
                                            <td>
                                                <span class="badge badge-info">{{ $faculty->nivel }}</span>
                                            </td>
                                            @canany(['facultades.editar', 'facultades.eliminar'])
                                                <td class="text-nowrap text-center">
                                                    <div class="acciones-column">
                                                        @can('facultades.editar')
                                                            <a href="{{ route('faculties.edit', $faculty) }}" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Editar">
                                                                <i class="fa fa-lg fa-fw fa-pen"></i>
                                                            </a>
                                                        @endcan

                                                        @can('facultades.eliminar')
                                                            <form action="{{ route('faculties.destroy', $faculty) }}" method="POST" style="display:inline">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta facultad?')">
                                                                    <i class="fa fa-lg fa-fw fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    </div>
                                                </td>
                                            @endcanany
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('css')
        <link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}">
@stop

@section('js')
    <script src="{{ asset('js/admin-init.js') }}"></script>
@stop
