@extends('adminlte::page')

@section('title', 'Cubículos')

@section('content_header')
    <h1 class="text-center">Gestión de Cubículos (Atención Virtual)</h1>
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
                    <div class="card-header d-flex justify-content-end align-items-center">
                        @can('cubiculos.crear')
                            <a href="{{ route('cubiculos.create') }}" class="btn btn-primary rounded-pill px-5">
                                <i class="fas fa-plus"></i> Nuevo Cubículo
                            </a>
                        @endcan
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            {{-- Se añade la clase datatable-export y el data-page-title para el PDF --}}
                            <table id="cubiculos" class="table datatable-export" data-page-title="Listado de Cubículos Virtuales">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre del Cubículo</th>
                                        <th>Enlace de Reunión</th>
                                        <th>Usuario Asignado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cubiculos as $cubiculo)
                                        <tr>
                                            <td>{{ $cubiculo->id }}</td>
                                            <td>{{ $cubiculo->nombre }}</td>
                                            <td>
                                                @if ($cubiculo->enlace_o_ubicacion)
                                                    <a href="{{ $cubiculo->enlace_o_ubicacion }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-video"></i> Ir a reunión
                                                    </a>
                                                @else
                                                    <span class="text-muted">Sin enlace configurado</span>
                                                @endif
                                            </td>
                                            <td>{{ $cubiculo->users->name ?? 'No asignado' }}</td>

                                            <td class="text-nowrap">
                                                <div class="acciones-column">
                                                    @can('cubiculos.editar')
                                                        <a href="{{ route('cubiculos.edit', $cubiculo) }}"
                                                        class="btn btn-xs btn-default text-primary shadow-sm"
                                                        title="Editar">
                                                            <i class="fa fa-lg fa-fw fa-pen"></i>
                                                        </a>
                                                    @endcan

                                                    @can('cubiculos.eliminar')
                                                        <form action="{{ route('cubiculos.destroy', $cubiculo) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-xs btn-default text-danger shadow-sm"
                                                                    onclick="return confirm('¿Seguro de eliminar este cubículo?')">
                                                                <i class="fa fa-lg fa-fw fa-trash"></i>
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
