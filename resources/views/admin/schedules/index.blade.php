@extends('adminlte::page')

@section('title', 'Horarios')

@section('content_header')
    <h1 class="text-center">Gestión de Horarios</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-11">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header d-flex justify-content-end">
                        @can('horarios.crear')
                            <a href="{{ route('schedules.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus mr-1"></i> Nuevo
                            </a>
                        @endcan
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table
                                id="horarios"
                                class="table datatable-export"
                                data-page-title="Listado de Horarios"
                            >
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
                                            <td>
                                                <div class="acciones-column">
                                                    @can('horarios.editar')
                                                        <a href="{{ route('schedules.edit', $schedule->id_hor) }}"
                                                           class="btn btn-xs btn-default text-primary mx-1 shadow-sm"
                                                           title="Editar">
                                                            <i class="fa fa-lg fa-fw fa-pen"></i>
                                                        </a>
                                                    @endcan

                                                    @can('horarios.eliminar')
                                                        <form action="{{ route('schedules.destroy', $schedule->id_hor) }}"
                                                              method="POST">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-xs btn-default text-danger mx-1 shadow-sm"
                                                                    onclick="return confirm('¿Eliminar horario?')"
                                                                    title="Eliminar">
                                                                <i class="fa fa-lg fa-fw fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan

                                                    @if($schedule->cubicles->count() > 0)
                                                        <div class="dropdown">
                                                            <button class="btn btn-xs btn-default text-info mx-1 shadow-sm dropdown-toggle"
                                                                    data-toggle="dropdown">
                                                                <i class="fas fa-wrench"></i>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                @foreach($schedule->cubicles as $cubiculo)
                                                                    <a class="dropdown-item"
                                                                       href="{{ route('shifts.index', [
                                                                            'horario_id' => $schedule->id_hor,
                                                                            'cubiculo_id' => $cubiculo->id
                                                                       ]) }}">
                                                                        {{ $cubiculo->nombre }}
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>

                                            <td>{{ $schedule->start_time }}</td>
                                            <td>{{ $schedule->end_time }}</td>
                                            <td>{{ $schedule->attention_minutes }}'</td>
                                            <td>{{ $schedule->break_minutes }}'</td>
                                            <td>
                                                @foreach($schedule->days as $day)
                                                    <span class="badge badge-light">{{ $day->date_day->format('d/m') }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ optional($schedule->valid_from)->format('d/m/Y') ?? 'N/A' }}</td>
                                            <td>{{ $schedule->shifts_count }}</td>
                                            <td>{{ $schedule->occupied_shifts_count }}</td>
                                            <td>{{ $schedule->days_count }}</td>
                                            <td>{{ $schedule->cubicles->pluck('nombre')->join(', ') }}</td>
                                            <td>
                                                @foreach($schedule->breaks as $b)
                                                    <small class="d-block">
                                                        {{ \Carbon\Carbon::parse($b->start_break)->format('H:i') }}
                                                        -
                                                        {{ \Carbon\Carbon::parse($b->end_break)->format('H:i') }}
                                                    </small>
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
    <link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}">
@stop

@section('js')
    <script src="{{ asset('js/admin-init.js') }}"></script>
@stop
