@extends('adminlte::page')

@section('title', 'Cubículos')

@section('content_header')
    <h1 class="text-center">Gestión de Cubículos (Atención Virtual)</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-11">

                {{-- Opcional: Alerta de sesión si la usas en el controlador --}}
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
                                                @can('cubiculos.editar')
                                                    <a href="{{ route('cubiculos.edit', $cubiculo) }}" class="btn btn-link text-primary btn-sm me-1" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('cubiculos.eliminar')
                                                    <form action="{{ route('cubiculos.destroy', $cubiculo) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger btn-sm"
                                                            onclick="return confirm('¿Seguro de eliminar este cubículo?')">
                                                            <i class="fas fa-trash"></i>
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
    {{-- Cargamos los estilos compartidos y los CDN necesarios --}}
    <link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
@stop

@section('js')
    {{-- Incluimos el script centralizado que inicializa automáticamente cualquier .datatable-export --}}
    @include('partials.datatables-scripts')
@stop
