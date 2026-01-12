@extends('adminlte::page')

@section('title', 'Asignaciones')

@section('content_header')
    <h1 class="text-center">Asignación de Operadores a Áreas</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="asignaciones" class="table">
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
                                            <td>{{ $usuario->name }}</td>
                                            <td>{{ $usuario->DNI ?? 'N/A' }}</td>
                                            <td>
                                                @if ($usuario->operatingAreas->count() > 0)
                                                    @foreach ($usuario->operatingAreas as $area)
                                                        <span class="badge badge-info p-2 mb-1" style="font-size: 0.85rem; font-weight: 500;">
                                                            <i class="fas fa-tag mr-1"></i> {{ $area->name }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="badge badge-light border text-muted">Sin áreas asignadas</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('assignments.edit', $usuario->id) }}" class="btn btn-primary rounded-pill px-4 btn-sm shadow-sm" title="Gestionar Áreas">
                                                    <i class="fas fa-tasks mr-1"></i> Gestionar
                                                </a>
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
        /* Estilos idénticos a tu módulo de usuarios */
        .card { border-radius: 1rem !important; border: none; box-shadow: 0 8px 24px rgba(0,0,0,0.07); }
        #asignaciones thead { background-color: #f8f9fa; }
        #asignaciones thead th { color: #495057; font-weight: 600; border: none; padding-top: 1rem; padding-bottom: 1rem; }
        .dataTables_filter input[type="search"] { width: 350px !important; border-radius: 20px !important; }
        .dt-buttons .btn { border-radius: 0.5rem; min-width: 105px; margin-right: 5px; }
    </style>
@stop

@section('js')
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
            $('#asignaciones').DataTable({
                responsive: true,
                autoWidth: false,
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' },
                dom: '<"d-flex justify-content-between mb-3"Bf>rtip',
                buttons: [
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-success btn-sm' },
                    { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-danger btn-sm' }
                ]
            });
        });
    </script>
@stop
