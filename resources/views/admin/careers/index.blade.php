@extends('adminlte::page')

@section('title', 'Carreras')

@section('content_header')
    <h1 class="text-center">Gestión de Carreras Universitarias</h1>
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
                        <h3 class="card-title text-bold">Listado de Carreras</h3>
                        <div class="card-tools">
                            @can('carreras.crear')
                                <a href="{{ route('careers.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus mr-1"></i> Nueva Carrera
                                </a>
                            @endcan
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            {{-- Clase datatable-export para activar el JS y table-custom para el CSS global --}}
                            <table id="carreras" class="table table-custom datatable-export" data-page-title="Listado de Carreras">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre de la Carrera</th>
                                        <th>Área de Atención</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($careers as $career)
                                        <tr>
                                            <td>
                                                <span class="badge badge-secondary shadow-sm" style="font-size: 0.85rem;">
                                                    {{ $career->career_code ?? 'S/N' }}
                                                </span>
                                            </td>
                                            <td class="font-weight-bold">{{ $career->name }}</td>
                                            <td>
                                                <span class="text-muted">
                                                    <i class="fas fa-map-marker-alt mr-1 text-info"></i>
                                                    {{ $career->operatingArea->name ?? 'Sin área' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @can('carreras.editar')
                                                    <a href="{{ route('careers.edit', $career) }}"
                                                       class="btn btn-xs btn-default text-primary mx-1 shadow-sm"
                                                       title="Editar">
                                                        <i class="fa fa-lg fa-fw fa-pen"></i>
                                                    </a>
                                                @endcan

                                                @can('carreras.eliminar')
                                                    <form action="{{ route('careers.destroy', $career) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-xs btn-default text-danger mx-1 shadow-sm"
                                                                title="Eliminar"
                                                                onclick="return confirm('¿Está seguro de eliminar esta carrera?')">
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
    {{-- Hereda los estilos globales del archivo que creamos --}}
    <link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
@stop

@section('js')
    {{-- Hereda la inicialización automática de DataTables y botones --}}
    @include('partials.datatables-scripts')
@stop
