@extends('adminlte::page')

@section('title', 'Horarios')

@section('content_header')
<<<<<<< HEAD
    <h1 class="text-center">Sección Horarios</h1>
=======
    <h1>Sección Horarios</h1>
>>>>>>> 770481b4c5da99829f5c325c95116d71cc39b8aa
@stop

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Gestión de Horarios</h3>
            <a href="{{ route('schedules.create') }}" class="btn btn-primary">
                <i class="fas fa-calendar-plus"></i> Crear Nuevo Horario
            </a>
        </div>

        <div class="card-body">
            {{-- Mensaje de éxito --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            {{-- Filtro por cubículo --}}
           <!-- <div class="row mb-3">
                <div class="col-md-4">
                    <label for="filtro_cubiculo" class="form-label">Filtrar por Cubículo:</label>
                    <select id="filtro_cubiculo" class="form-control">
                        <option value="">Todos los cubículos</option>
                        @foreach($schedules->pluck('cubiculo.nombre')->unique() as $cubiculoNombre)
                            @if($cubiculoNombre)
                                <option value="{{ $cubiculoNombre }}">{{ $cubiculoNombre }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>-->

            @php
                $diasSemana = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
            @endphp

            <div class="table-responsive">
                <table id="tabla-horarios" class="table table-bordered table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Cubículo</th>
                            <th>Horario Laboral</th>
                            <th>Duración Total (min)</th>
                            <th>Vigencia</th>
                            <th>Tiempo de Atención</th>
                            <th>Días de la Semana</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($schedules as $schedule)
                            <tr>
                                <td>{{ $schedule->id }}</td>
                                <td>{{ $schedule->cubiculo->nombre ?? 'No asignado' }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ \Carbon\Carbon::parse($schedule->hora_inicio)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($schedule->hora_fin)->format('H:i') }}
                                    </span>
                                </td>
                                <td>{{ $schedule->total_duration_in_minutes }}</td>
                                <td>
                                    <small class="text-muted">
                                        Del <strong>{{ \Carbon\Carbon::parse($schedule->vigencia_desde)->format('d/m/Y') }}</strong><br>
                                        al <strong>{{ \Carbon\Carbon::parse($schedule->vigencia_hasta)->format('d/m/Y') }}</strong>
                                    </small>
                                </td>
                                <td>{{ $schedule->atencion }}</td>
                                <td>
                                    @foreach($schedule->days as $day)
                                        <span class="badge bg-secondary">{{ $diasSemana[$day->weekday] ?? 'Día inválido' }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-sm btn-warning me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('schedules.destroy', $schedule) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este horario?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No hay horarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- DataTables estilos --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
@stop

@section('js')
    {{-- DataTables scripts --}}
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    {{-- Exportación: PDF, Excel, Print --}}
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(function () {
            var tabla = $('#tabla-horarios').DataTable({
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
                        title: 'Listado de Horarios'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-secondary btn-sm'
                    }
                ],
                order: [[1, 'asc']]
            });

            // Filtro por cubículo
            $('#filtro_cubiculo').on('change', function () {
                tabla.column(1).search(this.value).draw();
            });
        });
    </script>
@stop
