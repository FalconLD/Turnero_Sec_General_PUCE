@extends('adminlte::page')

@section('title', 'Parámetros')

@section('content_header')
    <h1 class="text-center">Gestión de Parámetros del Sistema</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-11">

            {{-- Alertas estandarizadas --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-end align-items-center">
                    <a href="{{ route('parameters.create') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-plus"></i> Nuevo Parámetro
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        {{-- datatable-export activa el JS global y data-page-title define el título del reporte --}}
                        <table id="tabla-parametros" class="table datatable-export" data-page-title="Listado de Parámetros del Sistema">
                            <thead>
                                <tr>
                                    <th>Clave</th>
                                    <th>Descripción</th>
                                    <th>Parámetro</th>
                                    <th>Creado</th>
                                    <th>Actualizado</th>
                                    @canany(['parametros.editar', 'parametros.eliminar'])
                                        <th class="text-center">Acciones</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($parameters as $param)
                                    <tr>
                                        <td class="font-weight-bold text-primary">{{ $param->clave }}</td>
                                        <td>{{ $param->descripcion }}</td>
                                        <td><code class="text-dark">{{ $param->parametro }}</code></td>
                                        <td>{{ $param->created_at?->format('Y-m-d H:i') }}</td>
                                        <td>{{ $param->updated_at?->format('Y-m-d H:i') }}</td>
                                        @canany(['parametros.editar', 'parametros.eliminar'])
                                            <td class="text-nowrap">
                                                <div class="acciones-column">
                                                    @can('parametros.editar')
                                                        <a href="{{ route('parameters.edit', $param) }}"
                                                        class="btn btn-xs btn-default text-primary shadow-sm"
                                                        title="Editar">
                                                            <i class="fas fa-lg fa-pen"></i>
                                                        </a>
                                                    @endcan

                                                    @can('parametros.eliminar')
                                                        <form action="{{ route('parameters.destroy', $param) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-xs btn-default text-danger shadow-sm"
                                                                    onclick="return confirm('¿Eliminar este parámetro?')"
                                                                    title="Eliminar">
                                                                <i class="fas fa-lg fa-trash"></i>
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
