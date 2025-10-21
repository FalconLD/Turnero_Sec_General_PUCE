@extends('adminlte::page')

@section('title', 'Cubículos')

@section('content_header')
    <h1 class="text-center">Listado de Cubículos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-end align-items-center">            
            <a href="{{ route('cubiculos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo
            </a>
        </div>

        <div class="card-body">
            <table id="cubiculos" class="table">
                <thead class="table-primary">
                    <tr>
                        <th  >ID</th>
                        <th >Nombre</th>
                        <th >Tipo de Atención</th>
                        <th >Enlace / Ubicación</th>
                        <th >Usuario Asignado</th>
                        <th >Acciones</th>
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
                            <td>{{ $cubiculo->users->name ?? 'No asignado' }}</td>
                            <td>
                                <a href="{{ route('cubiculos.edit', $cubiculo) }}" class="btn btn-warning btn-sm me-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('cubiculos.destroy', $cubiculo) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm"
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
@stop

@section('css')
    {{-- Estilos DataTables y botones --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <style>
        .dt-buttons .btn:not(:first-child) {
            margin-left: 5px !important;
        }
    </style>

@stop

@section('js')
    {{-- Librerías --}}
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    {{-- Botones PDF / Excel --}}
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
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Exportar Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> Exportar PDF',
                        className: 'btn btn-danger btn-sm',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        title: 'Listado de Cubículos'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-secondary btn-sm ms-2' // << AÑADIDO ms-2
                    }

                ]
            });

            // Filtro por tipo de atención
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
