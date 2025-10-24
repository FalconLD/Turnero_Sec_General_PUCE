@extends('adminlte::page')

@section('title', 'Schedules')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Schedules</h1>
        <a href="{{ route('schedules.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo horario
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
                    <th>ID</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Attention (min)</th>
                    <th>Break (min)</th>
                    <th>Cubicles</th>
                    <th>Breaks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                <tr>
                    <td>{{ $schedule->id_hor }}</td>
                    <td>{{ $schedule->start_time }}</td>
                    <td>{{ $schedule->end_time }}</td>
                    <td>{{ $schedule->attention_minutes }}</td>
                    <td>{{ $schedule->break_minutes }}</td>
                    <td>{{ $schedule->cubicles->pluck('name')->join(', ') }}</td>
                    <td>
                        @foreach($schedule->breaks as $b)
                            {{ $b->start_time }} - {{ $b->end_time }}<br>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('schedules.edit', $schedule->id_hor) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('schedules.destroy', $schedule->id_hor) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('¿Está seguro de eliminar este horario?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No schedules found.</td>
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
