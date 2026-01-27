@extends('adminlte::page')

@section('title', 'Gestión de Turnos')

@section('content_header')
    <h1 class="text-center">Gestión de Turnos</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-11">

                <div class="mb-3">
                    <a href="{{ route('schedules.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Volver a Horarios
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table
                                id="turnos"
                                class="table datatable-export"
                                data-page-title="Listado de Turnos"
                            >
                                <thead>
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
                                            <td>
                                                <div class="acciones-column">
                                                    <form action="{{ route('shifts.destroy', $shift->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-xs btn-default text-danger shadow"
                                                                onclick="return confirm('¿Seguro de que deseas borrar este turno?')"
                                                                title="Eliminar">
                                                            <i class="fa fa-lg fa-fw fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>

                                            <td>{{ $shift->cubicle_name ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($shift->date_shift)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</td>
                                            <td>{{ $shift->student_dni ?? 'N/A' }}</td>
                                            <td>{{ $shift->student_name ?? 'Disponible' }}</td>
                                            <td>{{ $shift->student_email ?? 'N/A' }}</td>
                                            <td>
                                                @if($shift->status == 1)
                                                    <span class="badge badge-success">Activo</span>
                                                @else
                                                    <span class="badge badge-secondary">Agendado</span>
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
    <link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}">
@stop

@section('js')
    <script src="{{ asset('js/admin-init.js') }}"></script>
@stop
