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
                Seleccione la modalidad, fecha y horario disponible para agendar su cita.
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

                            <div class="mb-3">
                                <label for="modalidadSelect" class="form-label fw-bold">Modalidad</label>
                                <select id="modalidadSelect" name="modalidad" class="form-select shadow-sm" required>
                                    <option value="" selected disabled>Seleccione...</option>
                                    <option value="presencial">Presencial</option>
                                    <option value="virtual">Virtual</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="fechaSeleccionada" class="form-label fw-bold">Fecha disponible</label>
                                <input type="date" id="fechaSeleccionada" name="fecha"
                                       class="form-control shadow-sm"
                                       min="{{ date('Y-m-d') }}" required>
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
                                    <em>Seleccione modalidad y fecha para ver los turnos disponibles...</em>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Campos ocultos --}}
                <input type="hidden" name="turno_id" id="turno_id">
                <input type="hidden" name="cedula" value="{{ $student->cedula }}">

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success px-5 fw-semibold">
                        Confirmar y Guardar
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
    background-image: url("data:image/svg+xml;utf8,<svg fill='%236c757d' viewBox='0 0 16 16' xmlns='http://www.w3.org/2000/svg'><path d='M4.646 6.646a.5.5 0 0 1 .708 0L8 9.293l2.646-2.647a.5.5 0 0 1 .708.708L8.354 10.354a.5.5 0 0 1-.708 0L4.646 7.354a.5.5 0 0 1 0-.708z'/></svg>");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 14px;
}
.form-select:focus, .form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.2);
}
.turno-card {
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 16px;
    background-color: #fff;
    transition: all 0.2s ease;
    cursor: pointer;
}
.turno-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    border-color: #0d6efd;
}
.turno-card.selected {
    border: 2px solid #0d6efd;
    background-color: #e8f0fe;
    box-shadow: 0 0 10px rgba(13,110,253,0.2);
}
</style>

{{-- === Script funcional === --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalidadSelect = document.getElementById('modalidadSelect');
    const fechaInput = document.getElementById('fechaSeleccionada');
    const turnosContainer = document.getElementById('turnosContainer');
    const turnoInput = document.getElementById('turno_id');

    function cargarTurnos() {
        const modalidad = modalidadSelect.value;
        const fecha = fechaInput.value;

        if (!modalidad || !fecha) return;

        turnosContainer.innerHTML = '<p class="text-center text-muted">Cargando turnos...</p>';

        fetch(`/shifts/${fecha}?modalidad=${modalidad}`)
            .then(res => res.json())
            .then(data => {
                turnosContainer.innerHTML = '';

                // ✅ Validar la estructura del JSON que devuelve el backend
                if (!data.success || !Array.isArray(data.data) || data.data.length === 0) {
                    turnosContainer.innerHTML = '<p class="text-center text-muted">No hay turnos disponibles para esta fecha.</p>';
                    return;
                }

                // ✅ Recorrer correctamente los turnos
                data.data.forEach(turno => {
                    const div = document.createElement('div');
                    div.className = 'turno-card text-center';
                    div.innerHTML = `
                        <strong>${turno.start_shift}</strong><br>
                        <span class="text-muted small">${turno.end_shift}</span><br>
                        <span class="badge bg-light text-dark mt-1">${turno.cubiculo}</span>
                    `;
                    div.onclick = () => {
                        turnoInput.value = turno.id_shift;
                        document.querySelectorAll('.turno-card').forEach(c => c.classList.remove('selected'));
                        div.classList.add('selected');
                    };
                    turnosContainer.appendChild(div);
                });
            })
            .catch(error => {
                console.error("Error al cargar los turnos:", error);
                turnosContainer.innerHTML = '<p class="text-danger text-center">Error al cargar los turnos.</p>';
            });
    }

    modalidadSelect.addEventListener('change', cargarTurnos);
    fechaInput.addEventListener('change', cargarTurnos);
});
</script>

@stop
