@extends('adminlte::page')

@section('title', 'Asignaciones')

@section('content_header')
    <h1 class="text-center">Asignación de Operadores a Áreas</h1>
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
                        <h3 class="card-title text-bold">Listado de Asignaciones</h3>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            {{-- datatable-export activa el JS global y table-custom el estilo global --}}
                            <table id="asignaciones" class="table table-custom datatable-export" data-page-title="Reporte de Asignaciones">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>DNI</th>
                                        <th>Áreas de Atención</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $usuario)
                                        <tr>
                                            <td>{{ $usuario->id }}</td>
                                            <td class="font-weight-bold">{{ $usuario->name }}</td>
                                            <td>{{ $usuario->DNI ?? 'N/A' }}</td>
                                            <td>
                                                @forelse ($usuario->operatingAreas as $area)
                                                    <span class="badge badge-info p-2 mb-1 shadow-sm" style="font-size: 0.8rem; font-weight: 500;">
                                                        <i class="fas fa-tag mr-1"></i> {{ $area->name }}
                                                    </span>
                                                @empty
                                                    <span class="badge badge-light border text-muted">Sin áreas asignadas</span>
                                                @endforelse
                                            </td>
                                            <td class="text-center">
                                                @can('asignaciones.editar')
                                                    <a href="{{ route('assignments.edit', $usuario->id) }}"
                                                       class="btn btn-primary rounded-pill px-4 btn-sm shadow-sm"
                                                       title="Gestionar Áreas">
                                                        <i class="fas fa-tasks mr-1"></i> Gestionar
                                                    </a>
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
    {{-- Carga estilos globales --}}
    <link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
@stop

@section('js')
    {{-- Carga la lógica de DataTables centralizada --}}
    @include('partials.datatables-scripts')
@stop
