@extends('layouts.app')

@section('title', 'Agendamiento de Cita')

@section('layout_sidebar', false)

@section('content_header')
    <h1 class="text-center mb-4 fw-bold text-primary">📅 Agendamiento de Cita</h1>
@stop

@section('content')
<div class="container">
    <div class="card shadow-lg border-0 rounded-4 mb-5">
        <div class="card-body p-5">
            <p class="text-muted text-center mb-4">
                Bienvenido/a <strong>{{ $student->names }}</strong>.<br>
                Seleccione la fecha y horario disponible para agendar su cita virtual.
            </p>

            {{-- Mensajes flash --}}
            @if(session('info'))
                <div class="alert alert-info text-center">{{ session('info') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success text-center">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger text-center">{{ $errors->first() }}</div>
            @endif

            <form id="form-agendamiento" method="POST" action="{{ route('student.agendarTurno') }}">
                @csrf

                <div class="row justify-content-center g-4">
                    {{-- Columna izquierda --}}
                    <div class="col-lg-6 col-md-7">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                            <h6 class="fw-bold text-secondary mb-3">Configuración de cita</h6>

                            {{-- Modalidad fija en Virtual --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Modalidad</label>
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-camera-video"></i> 
                                    <strong>Atención Virtual</strong>
                                    <p class="small mb-0 mt-1">Todas las citas son mediante videollamada</p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="fechaSeleccionada" class="form-label fw-bold">Fecha disponible</label>
                                <input type="date" 
                                       id="fechaSeleccionada" 
                                       name="fecha"
                                       class="form-control shadow-sm"
                                       min="{{ date('Y-m-d') }}" 
                                       required>
                            </div>

                            <div class="bg-light text-center p-4 rounded-3 border mt-3">
                                <i class="bi bi-calendar-date text-primary" style="font-size:2rem;"></i>
                                <p class="mt-2 text-muted small">Seleccione una fecha para mostrar los turnos</p>
                            </div>
                        </div>
                    </div>

                    {{-- Columna derecha --}}
                    <div class="col-lg-6 col-md-5">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                            <h6 class="fw-bold text-secondary mb-3 text-center">Horarios disponibles</h6>
                            <div id="turnosContainer" class="d-flex flex-wrap justify-content-center align-items-start gap-3">
                                <div class="text-muted text-center">
                                    <em>Seleccione una fecha para ver los turnos disponibles...</em>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Campos ocultos --}}
                <input type="hidden" name="turno_id" id="turno_id" value="">
                <input type="hidden" name="cedula" id="cedula" value="{{ $student->cedula }}">

                <div class="text-center mt-4">
                    <button type="submit" id="btnConfirmar" class="btn btn-success px-5 fw-semibold" disabled>
                        <i class="bi bi-check-circle"></i> Confirmar y Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- === Estilos personalizados === --}}
<style>
.form-select {
    appearance: none;
    background-color: #fff;
    border: 1px solid #ced4da;
    border-radius: 12px;
    padding: 10px 38px 10px 14px;
    font-size: 0.95rem;
    color: #495057;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 14px;
}
.form-select:focus, .form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.2);
}
.turno-card {
    border: 2px solid #dee2e6;
    border-radius: 12px;
    padding: 16px;
    background-color: #fff;
    transition: all 0.2s ease;
    cursor: pointer;
    min-width: 120px;
}
.turno-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    border-color: #0d6efd;
}
.turno-card.selected {
    border: 3px solid #0d6efd;
    background-color: #e8f0fe;
    box-shadow: 0 0 15px rgba(13,110,253,0.3);
}
</style>

{{-- === Script funcional CORREGIDO === --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fechaInput = document.getElementById('fechaSeleccionada');
    const turnosContainer = document.getElementById('turnosContainer');
    const turnoInput = document.getElementById('turno_id');
    const btnConfirmar = document.getElementById('btnConfirmar');

    // ✅ Función para cargar turnos (solo necesita fecha, modalidad es siempre virtual)
    function cargarTurnos() {
        const fecha = fechaInput.value;

        if (!fecha) {
            turnosContainer.innerHTML = '<div class="text-muted text-center"><em>Seleccione una fecha...</em></div>';
            return;
        }

        // Limpiar selección previa
        turnoInput.value = '';
        btnConfirmar.disabled = true;
        
        turnosContainer.innerHTML = '<p class="text-center text-muted"><i class="bi bi-hourglass-split"></i> Cargando turnos...</p>';

        // ✅ Fetch corregido - usa la ruta correcta
        fetch(`/shifts/${fecha}`)
            .then(res => {
                console.log('Response status:', res.status); // Debug
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                console.log('Turnos recibidos:', data); // Debug

                turnosContainer.innerHTML = '';

                if (!data.success || !Array.isArray(data.data) || data.data.length === 0) {
                    turnosContainer.innerHTML = `
                        <div class="alert alert-warning text-center w-100">
                            <i class="bi bi-exclamation-triangle"></i><br>
                            No hay turnos disponibles para esta fecha.
                        </div>
                    `;
                    return;
                }

                // ✅ Renderizar turnos disponibles
                data.data.forEach(turno => {
                    const div = document.createElement('div');
                    div.className = 'turno-card text-center';
                    div.setAttribute('data-turno-id', turno.id_shift); // Para debugging
                    div.innerHTML = `
                        <i class="bi bi-clock text-primary" style="font-size:1.5rem;"></i><br>
                        <strong>${turno.start_shift}</strong><br>
                        <span class="text-muted small">${turno.end_shift}</span><br>
                        <span class="badge bg-light text-dark mt-2">${turno.cubiculo}</span>
                    `;
                    
                    // ✅ Event listener para seleccionar turno
                    div.onclick = () => {
                        console.log('Turno seleccionado:', turno.id_shift); // Debug
                        
                        // Guardar ID en input oculto
                        turnoInput.value = turno.id_shift;
                        
                        // Remover selección previa
                        document.querySelectorAll('.turno-card').forEach(c => c.classList.remove('selected'));
                        
                        // Marcar como seleccionado
                        div.classList.add('selected');
                        
                        // Habilitar botón confirmar
                        btnConfirmar.disabled = false;
                        
                        console.log('Valor guardado en input:', turnoInput.value); // Debug
                    };
                    
                    turnosContainer.appendChild(div);
                });

                // Mostrar total de turnos
                const totalInfo = document.createElement('div');
                totalInfo.className = 'w-100 text-center text-muted small mt-3';
                totalInfo.innerHTML = `<i class="bi bi-info-circle"></i> ${data.total} turno(s) disponible(s)`;
                turnosContainer.appendChild(totalInfo);
            })
            .catch(error => {
                console.error("Error al cargar los turnos:", error);
                turnosContainer.innerHTML = `
                    <div class="alert alert-danger text-center w-100">
                        <i class="bi bi-x-circle"></i><br>
                        Error al cargar los turnos. Por favor intente nuevamente.
                    </div>
                `;
            });
    }

    // ✅ Event listener para cambio de fecha
    fechaInput.addEventListener('change', cargarTurnos);

    // ✅ Validación antes de enviar formulario
    document.getElementById('form-agendamiento').addEventListener('submit', function(e) {
        const turnoId = document.getElementById('turno_id').value;
        
        console.log('Submit - Turno ID:', turnoId); // Debug
        
        if (!turnoId) {
            e.preventDefault();
            alert('Por favor seleccione un turno antes de confirmar.');
            return false;
        }
        
        // Confirmar antes de enviar
        if (!confirm('¿Confirma que desea agendar este turno?')) {
            e.preventDefault();
            return false;
        }
        
        return true;
    });
});
</script>

@stop