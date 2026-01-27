@extends('layouts.app')

@section('title', 'Mi Turno Actual')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Tarjeta principal --}}
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                
                {{-- Encabezado --}}
                <div class="card-header text-center p-4" style="background-color: #f8f9fa;">
                    <h3 class="mb-1 fw-bold text-dark">📅 Turno Asignado</h3>
                    <p class="mb-0 text-muted">Revisa la información de tu cita programada</p>
                </div>

                {{-- Contenido --}}
                <div class="card-body p-4">
                    <div class="table-responsive mb-4">
                        <table class="table table-borderless align-middle mb-0">
                            <tbody>
                                <tr>
                                    <th class="text-end text-secondary" style="width: 35%">📆 Fecha:</th>
                                    <td class="text-start fw-semibold fs-5">{{ \Carbon\Carbon::parse($turnoActual->date_shift)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-end text-secondary">⏰ Hora:</th>
                                    <td class="text-start fw-semibold fs-5">{{ $turnoActual->start_shift }} - {{ $turnoActual->end_shift }}</td>
                                </tr>
                                <tr>
                                    <th class="text-end text-secondary">🪪 Cédula:</th>
                                    <td class="text-start fw-semibold fs-5">{{ $student->cedula }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Mensaje informativo --}}
                    <div class="alert alert-light border-start border-4 border-primary d-flex align-items-center rounded-4 shadow-sm fade show">
                        <i class="bi bi-info-circle-fill me-3 fs-4 text-primary"></i>
                        <div class="text-dark">
                            Ya tiene un turno agendado.  
                            Si desea cambiarlo, puede eliminar este turno y agendar uno nuevo.
                        </div>
                    </div>

                    {{-- Botón eliminar --}}
                    <form action="{{ route('student.turno.eliminar') }}" method="POST"
                          onsubmit="return confirm('¿Está seguro que desea eliminar su turno actual?');" class="text-center mt-4">
                        @csrf
                        <input type="hidden" name="cedula" value="{{ $student->cedula }}">
                        <button type="submit" class="btn btn-outline-danger btn-lg px-5 py-2 rounded-pill shadow-sm">
                            <i class="bi bi-trash3 me-2"></i>Eliminar Turno
                        </button>
                    </form>
                </div>

                {{-- Pie --}}
                <div class="card-footer bg-white text-center py-3">
                    <small class="text-muted">Sistema de Turnos - PUCE</small>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')