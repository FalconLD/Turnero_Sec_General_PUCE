@extends('adminlte::page')

@section('title', 'Horarios')

@section('content_header')
    <h1 class="text-center">Sección Horarios</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            {{-- Botón para crear nuevos horarios --}}
            <a href="{{ route('schedules.create') }}" class="btn btn-primary"> 
                <i class="fas fa-plus"></i> Nuevo
            </a>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Helper para traducir los números de los días a texto --}}
            @php
                $diasSemana = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
            @endphp

            <table class="table caption-top">
                <thead>
                    <tr>
                        <th scope="col" class="table-primary">ID</th>
                        <th scope="col" class="table-primary">Cubículo</th>
                        <th scope="col" class="table-primary">Horario Laboral</th>
                        <th scope="col" class="table-primary">Duración total de tiempo atender(en min)</th>
                        <th scope="col" class="table-primary">Vigencia</th>
                        <th scope="col" class="table-primary">Tiempo de atención</th>
                        <th scope="col" class="table-primary">Días de la Semana</th>
                        <th scope="col" class="table-primary">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->id }}</td>
                            <td>{{ $schedule->cubiculo->nombre ?? 'No asignado' }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($schedule->hora_inicio)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($schedule->hora_fin)->format('H:i') }}
                            </td>
                            
                            <td>
                                {{ $schedule->total_duration_in_minutes }}
                            </td>

                            <td>
                                Del {{ \Carbon\Carbon::parse($schedule->vigencia_desde)->format('d/m/Y') }} <br>
                                al {{ \Carbon\Carbon::parse($schedule->vigencia_hasta)->format('d/m/Y') }}
                            </td>
                            <td>
                                {{ $schedule->atencion }}
                            </td>
                            <td>
                                {{-- Iteramos sobre los días y los mostramos como etiquetas --}}
                                @foreach($schedule->days as $day)
                                    <span class="badge badge-secondary">{{ $diasSemana[$day->weekday] ?? 'Día inválido' }}</span>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('schedules.destroy', $schedule) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este horario?');">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay horarios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop



@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop
