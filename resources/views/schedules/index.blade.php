@extends('adminlte::page')

@section('title', 'Horarios')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Horarios</h1>
        <a href="{{ route('schedules.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>  Nuevo
        </a>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                   <!-- <th>ID</th>--> 
                    <th>Acciones</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Atención (Min)</th>
                    <th>Descanso (Min)</th>
                    <th>Días</th>
                    <th>Fecha de Vigencia</th>  
                    <th># de turnos</th>
                    <th>Ocupados</th>
                    <th># de Días</th>
                    <th>Cubículos</th>
                    <th>Descansos</th>
                    
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                <tr>
                    <td>
                        <a href="{{ route('schedules.edit', $schedule->id_hor) }}" class="btn btn-sm btn-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('schedules.destroy', $schedule->id_hor) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('¿Está seguro de eliminar este horario?')" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        <a href="{{ route('schedules.show', $schedule->id_hor) }}" class="btn btn-sm btn-info" title="Configurar Parametros">
                            <i class="fas fa-key"></i>
                        </a>
                    </td>
                    <td>{{ $schedule->start_time }}</td>
                    <td>{{ $schedule->end_time }}</td>
                    <td>{{ $schedule->attention_minutes }}</td>
                    <td>{{ $schedule->break_minutes }}</td>
                    <td>
                        @foreach($schedule->days as $day)
                            {{ $day->date_day->format('d/m/Y') }}<br>
                        @endforeach
                    </td>
                    <td>{{ $schedule->valid_from ? \Carbon\Carbon::parse($schedule->valid_from)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $schedule->shifts_count }}</td>
                    <td>{{ $schedule->occupied_shifts_count }}</td>
                    <td>{{ $schedule->days_count }}</td>
                    <td>{{ $schedule->cubicles->pluck('nombre')->join(', ') }}</td>
                    <td>
                        @foreach($schedule->breaks as $b)
                            {{ \Carbon\Carbon::parse($b->start_break)->format('H:i') }} - {{ \Carbon\Carbon::parse($b->end_break)->format('H:i') }}<br>
                        @endforeach
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center">No se encontraron horarios.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="mt-3">
            {{ $schedules->links() }}
        </div>
    </div>
</div>
@stop
