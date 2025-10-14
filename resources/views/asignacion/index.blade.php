@extends('adminlte::page')

@section('title', 'Asignación')

@section('content_header')
    <h1>Gestión de Asignaciones</h1>
@stop

@section('content')
    <div class="card shadow-lg">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Listado de Asignaciones</h3>
            <a href="{{ route('asignacion.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Asignación
            </a>
        </div>

        <div class="card-body">
            {{-- Mensaje de éxito --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            {{-- Tabla de asignaciones --}}
            <div class="table-responsive">
                <table id="tabla-asignacion" class="table table-bordered table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Cubículo</th>
                            <th>Formulario</th>
                            <th>Fecha de Actualización</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asignaciones as $asignacion)
                            <tr>
                                <td>{{ $asignacion->id }}</td>
                                <td>{{ $asignacion->cubiculo->nombre ?? 'N/A' }}</td>
                                <td>{{ $asignacion->form->title ?? 'N/A' }}</td>
                                <td>
                                    @if($asignacion->fecha_actualizacion)
                                        {{ $asignacion->fecha_actualizacion->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">No asignada</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('asignacion.edit', $asignacion->id) }}" class="btn btn-warning btn-sm me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('asignacion.destroy', $asignacion->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar esta asignación?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No hay asignaciones registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Estilos DataTables y Botones --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
@stop

@section('js')
    {{-- Librerías necesarias --}}
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    {{-- Botones de exportación --}}
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(function () {
            $('#tabla-asignacion').DataTable({
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
                        className: 'btn btn-danger btn-sm',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        title: 'Listado de Asignaciones'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-secondary btn-sm'
                    }
                ],
                order: [[0, 'asc']]
            });
        });
    </script>
@stop
