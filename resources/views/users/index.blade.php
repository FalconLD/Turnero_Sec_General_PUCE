@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <h1 class="text-center">Gestión de Usuarios</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center"> {{-- Fila para centrar el contenido --}}
            <div class="col-md-11"> {{-- Define el ancho (11 de 12 columnas) --}}

                <div class="card">
                    <div class="card-header d-flex justify-content-end align-items-center">
                        <a href="{{ route('users.create') }}" class="btn btn-primary rounded-pill px-5">
                            <i class="fas fa-user-plus"></i> Nuevo
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="usuarios" class="table">
                                <thead>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>DNI</th>
                                    <th>Cubículos Asignados</th>
                                    <th>Acciones</th>
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
                                                    <ul class="mb-0">
                                                        @foreach ($usuario->cubiculos as $cub)
                                                            <li>{{ $cub->nombre }} ({{ ucfirst($cub->tipo_atencion) }})</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="badge bg-secondary">Sin cubículos</span>
                                                @endif
                                            </td>
                                            <td class="text-nowrap"> <a href="{{ route('users.edit', $usuario) }}" class="btn btn-link text-primary btn-sm me-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('users.destroy', $usuario) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-link text-danger btn-sm" onclick="return confirm('¿Seguro de eliminar este usuario?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div> {{-- Cierre de col-md-11 --}}
        </div> {{-- Cierre de row justify-content-center --}}
    </div> {{-- Cierre de container-fluid --}}
@stop

@section('css')
    {{-- Estilos DataTables y botones --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

    {{-- CÓDIGO CORREGIDO PARA FORZAR LA SEPARACIÓN --}}
    <style>
        .dt-buttons .btn:not(:first-child) {
            margin-left: 5px !important;
        }

        /* --- 1. ESTILOS PARA REDONDEAR (Como la imagen de 'posventav2') --- */
        .card {
            border-radius: 1rem !important; /* (16px) !important para sobreescribir AdminLTE */
            border: none;
            box-shadow: 0 8px 24px rgba(0,0,0,0.07); /* Sombra suave para efecto flotante */
        }

        /* Redondear la cabecera del card también */
        .card-header {
            border-top-left-radius: 1rem !important;
            border-top-right-radius: 1rem !important;
            background-color: #fff; /* Fondo blanco como en el ejemplo */
            border-bottom: 1px solid #f0f0f0; /* Línea sutil */
        }


        /* Cabecera de la tabla (después de quitar 'thead-primary') */
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

        /* Quitar bordes verticales de la tabla (como en el ejemplo) */
        #usuarios td, #usuarios th {
            border-left: none;
            border-right: none;

        }
        
        /* Redondear los botones de exportar */
        .dt-buttons .btn {
            border-radius: 0.5rem; 
        }
        /* --- 3. AUMENTAR ANCHO DEL CAMPO DE BÚSQUEDA --- */
        .dataTables_filter input[type="search"] {
            width: 400px !important; /* Puedes cambiar este valor (ej: 400px) */
        }
        .dt-buttons .btn {
            min-width: 105px; /* Ancho mínimo para todos los botones */
            text-align: center; /* Centra el icono y texto */
        }
        #usuarios tbody .btn .fas {
            font-size: 1.4rem; /* '1rem' es el normal. Ajusta este valor si te gusta */
        }
    </style>
@stop

@section('js')
    {{-- Librerías DataTables y exportación --}}
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
                        className: 'btn btn-success btn-sm' // Primer botón, sin margen
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm ms-2', // << AÑADIDO ms-2
                        orientation: 'landscape',
                        pageSize: 'A4',
                        title: 'Listado de Usuarios'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-secondary btn-sm ms-2' // << AÑADIDO ms-2
                    }
                ]
            });

            // Filtro por nombre de usuario
            $('#filtro_nombre').on('keyup', function () {
                table.column(1).search(this.value).draw();
            });
        });
    </script>
@stop
