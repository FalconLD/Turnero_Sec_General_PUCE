@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <h1 class="text-center">Gestión de Usuarios</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-11">

                {{-- Mensajes de notificación --}}
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
                        <h3 class="card-title text-bold">Listado de Usuarios</h3>
                        <div class="card-tools">
                            @can('usuarios.crear')
                                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-user-plus mr-1"></i> Nuevo Usuario
                                </a>
                            @endcan
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            {{-- Clase 'datatable-export' activa el script global y 'table-custom' el estilo global --}}
                            <table id="usuarios" class="table table-custom datatable-export" data-page-title="Listado de Usuarios">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>DNI</th>
                                        <th>Cubículos Asignados</th>
                                        <th>Roles</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $usuario)
                                        <tr>
                                            <td>{{ $usuario->id }}</td>
                                            <td class="font-weight-bold">{{ $usuario->name }}</td>
                                            <td>{{ $usuario->email }}</td>
                                            <td>{{ $usuario->DNI ?? 'N/A' }}</td>
                                            <td>
                                                @if ($usuario->cubiculos->count() > 0)
                                                    <ul class="mb-0 pl-3">
                                                        @foreach ($usuario->cubiculos as $cub)
                                                            <li>{{ $cub->nombre }} <small class="text-muted">({{ ucfirst($cub->tipo_atencion) }})</small></li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="badge badge-light border text-muted">Sin cubículos</span>
                                                @endif
                                            </td>

                                            <td>
                                                @forelse($usuario->roles as $rol)
                                                    @php
                                                        $badgeClass = 'secondary';
                                                        $rolName = strtolower($rol->name);
                                                        if(str_contains($rolName, 'admin')) $badgeClass = 'danger';
                                                        elseif(str_contains($rolName, 'recepcion')) $badgeClass = 'warning';
                                                        elseif(str_contains($rolName, 'psicologo')) $badgeClass = 'info';
                                                    @endphp
                                                    <span class="badge badge-{{ $badgeClass }} shadow-sm" style="font-size: 0.85rem;">
                                                        {{ $rol->name }}
                                                    </span>
                                                @empty
                                                    <span class="badge badge-light border text-muted">Sin Rol</span>
                                                @endforelse
                                            </td>

                                            <td class="text-center">
                                                <div class="acciones-column">
                                                    @can('usuarios.editar')
                                                        <a href="{{ route('users.edit', $usuario) }}" class="btn btn-xs btn-default text-primary mx-1 shadow-sm" title="Editar">
                                                            <i class="fa fa-lg fa-fw fa-pen"></i>
                                                        </a>
                                                    @endcan

                                                    @can('usuarios.eliminar')
                                                        <form action="{{ route('users.destroy', $usuario) }}" method="POST" class="d-inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow-sm" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este usuario?')">
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
