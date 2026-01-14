@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <h1 class="text-center">Gestión de Usuarios</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-11">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Listado de Usuarios</h3>
                        <div class="card-tools">
                            {{-- Solo quien puede crear ve el botón --}}
                            @can('usuarios.crear')
                                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">Nuevo Usuario</a>
                            @endcan
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="usuarios" class="table">
                                <thead>
                                    <tr> {{-- Agregué <tr> que faltaba por buenas prácticas --}}
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>DNI</th>
                                        <th>Cubículos Asignados</th>
                                        <th>Roles</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $usuario)
                                        <tr>
                                            <td>{{ $usuario->id }}</td>
                                            <td>{{ $usuario->name }}</td>
                                            <td>{{ $usuario->email }}</td>
                                            <td>{{ $usuario->DNI ?? 'N/A' }}</td>
                                            <td>
                                                @if ($usuario->cubiculos->count() > 0)
                                                    <ul class="mb-0 pl-3"> {{-- pl-3 para mejor indentación --}}
                                                        @foreach ($usuario->cubiculos as $cub)
                                                            <li>{{ $cub->nombre }} ({{ ucfirst($cub->tipo_atencion) }})</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="badge badge-light border text-muted">Sin cubículos</span>
                                                @endif
                                            </td>

                                            {{-- LÓGICA DE ROLES --}}
                                            <td>
                                                @if($usuario->roles->isNotEmpty())
                                                    @foreach($usuario->roles as $rol)
                                                        @php
                                                            // Lógica de colores para los badges
                                                            $badgeClass = 'secondary';
                                                            $rolName = strtolower($rol->name);

                                                            if(str_contains($rolName, 'admin')) $badgeClass = 'danger';     // Rojo
                                                            elseif(str_contains($rolName, 'recepcion')) $badgeClass = 'warning'; // Amarillo
                                                            elseif(str_contains($rolName, 'psicologo')) $badgeClass = 'info';    // Azul
                                                        @endphp
                                                        <span class="badge badge-{{ $badgeClass }}" style="font-size: 0.9rem;">
                                                            {{ $rol->name }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="badge badge-light border text-muted">Sin Rol</span>
                                                @endif
                                            </td>

                                            <td>
                                                @can('usuarios.editar')
                                                    <a href="{{ route('users.edit', $usuario) }}" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                                        <i class="fa fa-lg fa-fw fa-pen"></i>
                                                    </a>
                                                @endcan

                                                @can('usuarios.eliminar')
                                                    <form action="{{ route('users.destroy', $usuario) }}" method="POST" style="display:inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

    <style>
        .dt-buttons .btn:not(:first-child) {
            margin-left: 5px !important;
        }
        .card {
            border-radius: 1rem !important;
            border: none;
            box-shadow: 0 8px 24px rgba(0,0,0,0.07);
        }
        .card-header {
            border-top-left-radius: 1rem !important;
            border-top-right-radius: 1rem !important;
            background-color: #fff;
            border-bottom: 1px solid #f0f0f0;
        }
        #usuarios thead {
            background-color: #f8f9fa;
        }
        #usuarios thead th {
            color: #495057;
            font-weight: 600;
            border: none;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        #usuarios td, #usuarios th {
            border-left: none;
            border-right: none;
            vertical-align: middle; /* Alineación vertical centrada se ve mejor */
        }
        .dt-buttons .btn {
            border-radius: 0.5rem;
            min-width: 105px;
            text-align: center;
        }
        .dataTables_filter input[type="search"] {
            width: 400px !important;
        }
        #usuarios tbody .btn .fas {
            font-size: 1.2rem; /* Ajusté un poco el tamaño para que no se vea gigante */
        }
    </style>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(function () {
            var table = $('#usuarios').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                dom: '<"d-flex justify-content-between mb-3"Bf>rtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm ms-2',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        title: 'Listado de Usuarios'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-secondary btn-sm ms-2'
                    }
                ]
            });
        });
    </script>
@stop
