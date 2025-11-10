@extends('adminlte::page')

@section('title', 'Cubículos')

@section('content_header')
    {{-- 1. Título centrado --}}
    <h1 class="text-center">Gestión de Cubículos</h1>
@stop

@section('content')
    {{-- 2. Contenedor centrado (11 de 12 columnas) --}}
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-11">

                <div class="card">
                    <div class="card-header d-flex justify-content-end align-items-center">
                        {{-- 3. Botón "Nuevo" redondeado (pill) --}}
                        <a href="{{ route('cubiculos.create') }}" class="btn btn-primary rounded-pill px-5">
                            <i class="fas fa-plus"></i> Nuevo
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="cubiculos" class="table"> {{-- ID y sin clases extra --}}
                                <thead> {{-- 4. Cabecera sin "table-primary" --}}
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Tipo de Atención</th>
                                        <th>Enlace / Ubicación</th>
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
                                                <span class="badge {{ $cubiculo->tipo_atencion == 'virtual' ? 'bg-info' : 'bg-success' }}">
                                                    {{ ucfirst($cubiculo->tipo_atencion) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($cubiculo->tipo_atencion === 'virtual' && $cubiculo->enlace_o_ubicacion)
                                                    <a href="{{ $cubiculo->enlace_o_ubicacion }}" target="_blank" class="text-primary">
                                                        {{ Str::limit($cubiculo->enlace_o_ubicacion, 30) }}
                                                    </a>
                                                @elseif ($cubiculo->tipo_atencion === 'presencial')
                                                    {{ $cubiculo->enlace_o_ubicacion ?? '—' }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            {{-- Corregido: 'users' es una relación, accede a 'name' --}}
                                            <td>{{ $cubiculo->users->name ?? 'No asignado' }}</td> 
                                            
                                            {{-- 5. Botones de acción como links (sin fondo) --}}
                                            <td class="text-nowrap">
                                                <a href="{{ route('cubiculos.edit', $cubiculo) }}" class="btn btn-link text-primary btn-sm me-1" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('cubiculos.destroy', $cubiculo) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-link text-danger btn-sm"
                                                        onclick="return confirm('¿Seguro de eliminar este cubículo?')">
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

            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- 6. Estilos de DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    
    {{-- 7. Bloque de estilos completo (tarjeta, tabla, botones) --}}
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

        /* Estilos adaptados para #cubiculos */
        #cubiculos thead {
            background-color: #f8f9fa; 
        }
        #cubiculos thead th {
            color: #495057; 
            font-weight: 600; 
            border: none;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        #cubiculos td, #cubiculos th {
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
        #cubiculos tbody .btn-link {
            border: none;
            background-color: transparent;
            box-shadow: none;
            padding: 0.25rem;
        }
        #cubiculos tbody .btn-link .fas {
            font-size: 1.2rem; /* Tamaño de icono sutil */
        }
        #cubiculos tbody .btn-link.text-primary:hover { color: #0056b3 !important; }
        #cubiculos tbody .btn-link.text-danger:hover { color: #dc3545 !important; }
    </style>
@stop

@section('js')
    {{-- 8. Librerías JS de DataTables --}}
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
        $(function() {
            var table = $('#cubiculos').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                dom: '<"d-flex justify-content-between mb-3"Bf>rtip',
                buttons: [
                    {{-- 9. Botones de exportación con estilo unificado --}}
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm ms-2', // ms-2 para margen
                        orientation: 'landscape',
                        pageSize: 'A4',
                        title: 'Listado de Cubículos'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-secondary btn-sm ms-2' // ms-2 para margen
                    }
                ]
            });

            // Se mantiene tu filtro personalizado
            $('#filtro_tipo').on('change', function() {
                var tipo = $(this).val();
                if (tipo) {
                    table.column(2).search(tipo, true, false).draw();
                } else {
                    table.column(2).search('').draw();
                }
            });
        });
    </script>
@stop