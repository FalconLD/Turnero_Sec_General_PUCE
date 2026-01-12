@extends('adminlte::page')

@section('title', 'Horarios')

@section('content_header')
    {{-- 1. Título centrado (como en Usuarios) --}}
    <h1 class="text-center">Gestión de Horarios</h1>
@stop

@section('content')
    {{-- 2. Estructura de contenedor centrada (como en Usuarios) --}}
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-11">

                <div class="card">
                    {{-- 3. Botón "Nuevo" movido al card-header (como en Usuarios) --}}
                    <div class="card-header d-flex justify-content-end align-items-center">
                        <a href="{{ route('schedules.create') }}" class="btn btn-primary rounded-pill px-5">
                            <i class="fas fa-plus"></i>  Nuevo
                        </a>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        {{-- 4. Wrapper 'table-responsive' y ID 'horarios' para DataTables --}}
                        <div class="table-responsive">
                            <table id="horarios" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Acciones</th>
                                        <th>Inicio</th>
                                        <th>Fin</th>
                                        <th>Atención</th>
                                        <th>Descanso</th>
                                        <th>Días</th>
                                        <th>Vigencia</th>
                                        <th>Turnos</th>
                                        <th>Ocupados</th>
                                        <th># Días</th>
                                        <th>Cubículos</th>
                                        <th>Descansos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedules as $schedule)
                                    <tr>
                                        <td class="text-nowrap">
                                            <a href="{{ route('schedules.edit', $schedule->id_hor) }}" class="btn btn-link text-primary btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('schedules.destroy', $schedule->id_hor) }}" method="POST" style="display:inline-block">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger btn-sm" onclick="return confirm('¿Eliminar horario?')" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>

                                            @if($schedule->cubicles->count() > 0)
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-link text-info btn-sm dropdown-toggle" data-toggle="dropdown">
                                                        <i class="fas fa-wrench"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        @foreach($schedule->cubicles as $cubiculo)
                                                            <a class="dropdown-item" href="{{ route('shifts.index', ['horario_id' => $schedule->id_hor, 'cubiculo_id' => $cubiculo->id]) }}">
                                                                {{ $cubiculo->nombre }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $schedule->start_time }}</td>
                                        <td>{{ $schedule->end_time }}</td>
                                        <td>{{ $schedule->attention_minutes }}'</td>
                                        <td>{{ $schedule->break_minutes }}'</td>
                                        <td>
                                            @foreach($schedule->days as $day)
                                                <small class="badge badge-light">{{ $day->date_day->format('d/m') }}</small>
                                            @endforeach
                                        </td>
                                        <td>{{ $schedule->valid_from ? \Carbon\Carbon::parse($schedule->valid_from)->format('d/m/Y') : 'N/A' }}</td>
                                        <td>{{ $schedule->shifts_count }}</td>
                                        <td>{{ $schedule->occupied_shifts_count }}</td>
                                        <td>{{ $schedule->days_count }}</td>
                                        <td><small>{{ $schedule->cubicles->pluck('nombre')->join(', ') }}</small></td>
                                        <td>
                                            @foreach($schedule->breaks as $b)
                                                <small class="d-block">{{ \Carbon\Carbon::parse($b->start_break)->format('H:i') }}-{{ \Carbon\Carbon::parse($b->end_break)->format('H:i') }}</small>
                                            @endforeach
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
    {{-- 7. Todos los CSS de DataTables copiados de Usuarios --}}
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

        /* 8. Estilos adaptados para #horarios (en lugar de #usuarios) */
        #horarios thead {
            background-color: #f8f9fa;
        }
        #horarios thead th {
            color: #495057;
            font-weight: 600;
            border: none;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        #horarios td, #horarios th {
            border-left: none;
            border-right: none;
            text-align: center;
            vertical-align: middle;
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
        #horarios tbody .btn .fas {
            font-size: 1.4rem;
        }
        /* Estilo para los botones de acción sin fondo */
        #horarios tbody .btn-link {
            border: none;
            background-color: transparent;
            box-shadow: none;
            padding: 0.25rem; /* Ajuste de padding */
        }
        #horarios tbody .btn-link .fas {
            font-size: 1.2rem; /* Tamaño de icono más sutil */
        }
        #horarios tbody .btn-link.text-primary:hover { color: #0056b3 !important; }
        #horarios tbody .btn-link.text-danger:hover { color: #dc3545 !important; }
        #horarios tbody .btn-link.text-info:hover { color: #0dcaf0 !important; }

    </style>
@stop

@section('js')
    {{-- Solo cargamos los plugins necesarios. jQuery ya viene con AdminLTE --}}
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
        $(document).ready(function () {
            $('#horarios').DataTable({
                "responsive": true,
                "autoWidth": false,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
                },
                "dom": '<"d-flex justify-content-between mb-3"Bf>rtip',
                "buttons": [
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
                        title: 'Listado de Horarios'
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
