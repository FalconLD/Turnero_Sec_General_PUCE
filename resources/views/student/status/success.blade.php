@extends('layouts.app')

@section('title', 'Registro Exitoso')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            {{-- Tarjeta de éxito --}}
            <div class="card border-success shadow">
                <div class="card-header bg-success text-white text-center py-4">
                    <i class="bi bi-check-circle display-4 d-block mb-3"></i>
                    <h4 class="mb-0">Turno Confirmado</h4>
                </div>
                
                <div class="card-body text-center py-5">
                    <p class="lead mb-4">
                        Tu turno ha sido agendado exitosamente.
                    </p>
                    
                    {{-- Botón principal --}}
                    <a href="{{ route('student.agendamiento') }}" 
                       class="btn btn-success btn-lg w-100 mb-3">
                        <i class="bi bi-eye me-2"></i> Ver detalles de mi turno
                    </a>
                    
                    {{-- Botón secundario --}}
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-left me-2"></i> Volver al inicio
                    </a>
                </div>
                
                <div class="card-footer text-center text-muted py-3">
                    <small>© Sistema de Turnos - PUCE</small>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
