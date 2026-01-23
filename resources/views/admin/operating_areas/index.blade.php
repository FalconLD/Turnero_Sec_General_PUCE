@extends('adminlte::page')

@section('title', 'Áreas Operativas')

@section('content_header')
    <h1 class="text-center">Gestión de Áreas Operativas</h1>
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
                        <h3 class="card-title text-bold">Listado de Áreas</h3>
                        <div class="card-tools">
                            @can('areas.crear')
                                <a href="{{ route('operating-areas.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus mr-1"></i> Nueva Área
                                </a>
                            @endcan
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            {{-- datatable-export activa el JS global y table-custom el estilo global --}}
                            <table id="areas_table" class="table table-custom datatable-export" data-page-title="Listado de Áreas Operativas">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre del Área</th>
                                        <th>Facultad</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($areas as $area)
                                        <tr>
                                            <td>{{ $area->id }}</td>
                                            <td class="font-weight-bold">{{ $area->name }}</td>
                                            <td>
                                                <span class="badge badge-info px-3 py-2 shadow-sm" style="font-size: 0.85rem;">
                                                    <i class="fas fa-university mr-1"></i> {{ $area->faculty->facultad }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @can('areas.editar')
                                                    <a href="{{ route('operating-areas.edit', $area) }}"
                                                       class="btn btn-xs btn-default text-primary mx-1 shadow-sm"
                                                       title="Editar">
                                                        <i class="fa fa-lg fa-fw fa-pen"></i>
                                                    </a>
                                                @endcan

                                                @can('areas.eliminar')
                                                    <form action="{{ route('operating-areas.destroy', $area) }}" method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-xs btn-default text-danger mx-1 shadow-sm"
                                                                title="Eliminar"
                                                                onclick="return confirm('¿Está seguro de eliminar esta área?')">
                                                            <i class="fa fa-lg fa-fw fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
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
