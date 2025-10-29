@extends('adminlte::page')

@section('title', 'Gestión de Turnos')

@section('content_header')
    {{-- 1. Título centrado --}}
    <h1 class="text-center">Gestión de Turnos</h1>
@stop

@section('content')
    {{-- 2. Contenedor centrado (11 de 12 columnas) --}}
    <div class="container-fluid">
        <div class="row justify-content-center">

            <div class="col-md-11">
                    <div class="mb-3">
                        <a href="{{ route('schedules.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a Horarios
                        </a>
                    </div>
                <div class="card">
                    

                    <div class="card-body">
                        {{-- 3. Wrapper 'table-responsive' y ID 'turnos' para DataTables --}}
                        <div class="table-responsive">
                            <table id="turnos" class="table"> {{-- ID 'turnos' y clase 'table' simple --}}
                                <thead> {{-- 4. Cabecera sin clases de estilo --}}
                                    <tr>
                                        <th>Acciones</th>
                                        <th>Cubículo</th>
                                        <th>Fecha</th>
                                        <th>Inicio</th>
                                        <th>Fin</th>
                                        <th>DNI</th>
                                        <th>Nom. Estudiante</th>
                                        <th>Correo Electrónico</th>
                                        <th>Estado</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($shifts as $shift)
                                    <tr>
                                        <td class="text-nowrap">
                                            <form action="{{ route('shifts.destroy', $shift->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger btn-sm" 
                                                        onclick="return confirm('¿Seguro de que deseas borrar este turno?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td>{{ $shift->cubicle_name ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($shift->date_shift)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</td>
                                        <td>{{ $shift->student_dni ?? 'N/A' }}</td>
                                        <td>{{ $shift->student_name ?? 'Disponible' }}</td>
                                        <td>{{ $shift->student_email ?? 'N/A' }}</td>                                        
                                        <td>
                                            {{-- 5. Lógica de estado mejorada con badges --}}
                                            @if($shift->status == 1)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-secondary">Agendado</span>
                                            @endif
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
    {{-- 6. Todos los CSS de DataTables --}}
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

        /* 7. Estilos adaptados para #turnos */
        #turnos thead {
            background-color: #f8f9fa; 
        }
        #turnos thead th {
            color: #495057; 
            font-weight: 600; 
            border: none;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        #turnos td, #turnos th {
            border-left: none;
            border-right: none;
            text-align: center;     /* <-- Centrado horizontal */
            vertical-align: middle; /* <-- Centrado vertical */
        }
        .dt-buttons .btn {
            border-radius: 0.5rem; 
        }
        .dataTables_filter input[type="search"] {
            width: 400px !important;
        }
        .dt-buttons .btn {
            min-width: 105px;
            text-align: center;
        }
        /* Esta tabla no tiene botones de acción, así que no se necesita el estilo .btn-link */
    </style>
@stop

@section('js')
    {{-- 8. Todos los JS de DataTables --}}
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
            // 9. Script inicializado para #turnos y título de PDF actualizado
            var table = $('#turnos').DataTable({
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
                        title: 'Listado de Turnos' // <-- Título actualizado
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