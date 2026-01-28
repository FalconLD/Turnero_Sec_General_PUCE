@extends('adminlte::page')

@section('title', 'Asignaciones')

@section('content_header')
    <h1 class="text-center font-weight-bold text-dark">Asignación de Operadores a Áreas</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-11">

                {{-- Las alertas ahora se auto-eliminan gracias al JS global --}}
                @if (session('info'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('info') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card shadow-sm"> {{-- Clase shadow-sm ahora centralizada en CSS --}}
                    <div class="card-header">
                        <h3 class="card-title text-bold">Listado de Asignaciones</h3>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            {{-- datatable-export activa el JS global y data-page-title define el nombre del PDF/Excel --}}
                            <table class="table table-hover datatable-export" data-page-title="Reporte de Asignaciones de Operadores">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>DNI</th>
                                        <th>Áreas de Atención</th>
                                        @canany(['asignaciones.editar', 'asignaciones.eliminar'])
                                            <th class="text-center">Acciones</th>
                                        @endcanany
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $usuario)
                                        <tr>
                                            <td>{{ $usuario->id }}</td>
                                            <td class="font-weight-bold text-primary">{{ $usuario->name }}</td>
                                            <td>{{ $usuario->DNI ?? 'N/A' }}</td>
                                            <td>
                                                @forelse ($usuario->operatingAreas as $area)
                                                    <span class="badge badge-info p-2 mb-1 shadow-sm" style="font-weight: 500;">
                                                        <i class="fas fa-tag mr-1"></i> {{ $area->name }}
                                                    </span>
                                                @empty
                                                    <span class="badge badge-light border text-muted">Sin áreas asignadas</span>
                                                @endforelse
                                            </td>
                                            @canany(['asignaciones.editar', 'asignaciones.eliminar'])
                                                <td class="text-nowrap text-center">
                                                    @can('asignaciones.editar')
                                                        <a href="{{ route('assignments.edit', $usuario->id) }}"
                                                        class="btn btn-primary rounded-pill px-3 btn-sm shadow-sm d-inline-flex align-items-center"
                                                        title="Gestionar Áreas">
                                                            <i class="fas fa-tasks mr-2"></i>
                                                            <span>Gestionar</span>
                                                        </a>
                                                    @endcan
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
