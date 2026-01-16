@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboardx</h1>
@stop

@section('content')
    <h3>Selecciona los días para el Horario #{{ $schedule->id }}</h3>
    <form action="{{ route('schedules.storeDays', $schedule) }}" method="POST">
        @csrf
        @php
            $days = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
        @endphp

        @foreach($days as $num => $name)
        <div>
            <input type="checkbox" name="weekdays[]" value="{{ $num }}" id="day-{{ $num }}"
                {{-- Revisa si el día actual está en el array de días guardados --}}
                {{ in_array($num, $savedDays ?? []) ? 'checked' : '' }}
            >
            <label for="day-{{ $num }}">{{ $name }}</label>
        </div>
        @endforeach

        <button type="submit" class="btn btn-success">Finalizar y Guardar Horario</button>
    </form>
@stop


@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    
@stop