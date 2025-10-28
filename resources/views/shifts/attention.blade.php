@extends('adminlte::page')

@section('title', 'Atención de Turnos')

@section('content_header')
    <h1 class="text-center mb-4 fw-bold text-primary">Atención de Turnos</h1>
@stop

@section('content')
<div class="container">
    {{-- Selector de fecha --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <form id="dateForm" method="GET" action="{{ route('shifts.attention') }}">
                <div class="input-group">
                    <label for="date" class="input-group-text">Fecha</label>
                    <input
                        type="date"
                        id="date"
                        name="date"
                        value="{{ $selectedDate ?? \Carbon\Carbon::now()->toDateString() }}"
                        class="form-control"
                        onchange="document.getElementById('dateForm').submit()"
                    >
                    <button type="submit" class="btn btn-primary">Ver</button>
                </div>
                <small class="text-muted">Selecciona una fecha para ver los turnos</small>
            </form>
        </div>
    </div>

    <div class="row">
        @if($shifts->isEmpty())
            <div class="col-12">
                <div class="alert alert-info">
                    No hay turnos para {{ \Carbon\Carbon::parse($selectedDate ?? now())->format('d/m/Y') }}.
                </div>
            </div>
        @endif

        @foreach($shifts as $shift)
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        {{-- Nombre del estudiante --}}
                        <h5 class="card-title">
                            @if($shift->student)
                                {{ $shift->student->names }}
                            @else
                                <span class="text-secondary">Turno disponible</span>
                            @endif
                        </h5>

                        {{-- Cédula --}}
                        <p class="mb-1"><strong>Cédula:</strong>
                            {{ $shift->student->cedula ?? '---' }}
                        </p>

                        {{-- Fecha y hora --}}
                        <p class="mb-1">
                            <strong>Fecha y hora:</strong>
                            {{ \Carbon\Carbon::parse($shift->date_shift)->format('d/m/Y') }}
                            —
                            {{ \Carbon\Carbon::parse($shift->start_shift)->format('H:i') }}
                            @if($shift->end_shift)
                                a {{ \Carbon\Carbon::parse($shift->end_shift)->format('H:i') }}
                            @endif
                        </p>

                        {{-- Modalidad --}}
                        <p class="mb-1">
                            <strong>Modalidad:</strong>
                            {{ $shift->student->nivel_instruccion ?? 'N/A' }}
                        </p>

                        {{-- Correo --}}
                        @if($shift->person)
                            <p class="mb-0"><strong>Correo:</strong> {{ $shift->person->correo_puce }}</p>
                        @endif
                    </div>

                    {{-- Estado del turno --}}
                    <div class="card-footer text-end">
                        @if($shift->person)
                            <span class="badge bg-success">Tomado</span>
                        @else
                            <span class="badge bg-secondary">Disponible</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@stop
