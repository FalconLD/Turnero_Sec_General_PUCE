@extends('layouts.app')

@section('title', 'Registro de Estudiante')

@section('layout_sidebar', false)

@section('content_header')
<h1 class="text-center mb-4 fw-bold text-primary">Registro de Estudiante</h1>
@stop

@section('content')
<div class="container">
    <div class="card shadow-lg border-0 rounded-4 mb-5">
        <div class="card-body p-5">
            <h4 class="card-title text-center mb-4 text-primary"></h4>
            {{-- Wizard de pasos visual --}}
            <div class="steps mb-4">
                <ul class="step-list d-flex justify-content-between text-center list-unstyled">

                    <li class="step-item active" data-step="0">
                        <span class="step-number">1</span>
                        <span class="step-title">Términos</span>
                    </li>

                    <li class="step-item" data-step="1">
                        <span class="step-number">2</span>
                        <span class="step-title">Datos</span>
                    </li>

                    <li class="step-item" data-step="4">
                        <span class="step-number">3</span>
                        <span class="step-title">Agendamiento</span>
                    </li>

                    <li class="step-item" data-step="5">
                        <span class="step-number">4</span>
                        <span class="step-title">Confirmación</span>
                    </li>
                </ul>
            </div>

            {{-- Barra de progreso --}}
            <div class="progress mb-4" style="height: 8px;">
                <div id="progressBar" class="progress-bar bg-primary" style="width: 0%;"></div>
            </div>

            <form id="student-registration-form" method="POST" action="{{ route('student.finish') }}" enctype="multipart/form-data"> @csrf
                {{-- Paso 1: Términos --}}
                <div class="form-step">
                    <h5 class="text-secondary mb-3">Términos y Condiciones</h5>
                    <div class="border rounded p-3 bg-light mb-3" style="max-height: 200px; overflow-y: auto;">
                        @if($terminos)
                        {!! nl2br(e($terminos->descripcion)) !!}
                        @else
                        <p class="text-muted">No se han configurado los términos y condiciones.</p>
                        @endif
                    </div>

                    <div class="form-check text-center">

                        <input type="checkbox" id="acepta_terminos" name="acepta_terminos" value="1">

                        <label for="acepta_terminos">Acepto los términos y condiciones</label>

                        <!-- 🆕 Segundo checkbox agregado -->
                        <div class="form-check text-center">
                            <input type="checkbox" id="acepta_politicas" name="acepta_politicas" value="1">
                            <label for="acepta_politicas">Consiento el manejo de mis datos personales bajo las normas de privacidad y confidencialidad.</label>
                        </div>

                    </div>
                </div>

                {{-- Paso 2: Datos personales --}}
                @php
                $student = session('student_data');
                @endphp
                <div class="form-step" style="display:none;">
                    <h5 class="text-secondary mb-3">Datos Personales y de Contacto</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Nombre completo</label>
                            <input type="text" name="names" value="{{  old('names', $student_name ?? '')}}" class="form-control" readonly>
                        </div>

                        <div class="col-md-3">
                            <label>Cédula</label>
                            <input type="text" name="cedula" value="{{ old('cedula', $student_cedula ?? '')  }}" class="form-control" readonly>
                        </div>

                        <div class="col-md-3">
                            <label>Edad</label>
                            <input type="number"
                                class="form-control"
                                name="edad"
                                id="inputEdad"
                                min="17"
                                max="80"
                                required
                                oninput="if(this.value.length > 2) this.value = this.value.slice(0, 2);">
                            <div id="errorEdad" class="text-danger small mt-1" style="display:none;">
                                La edad debe ser mayor o igual a 17 años.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Correo electrónico</label>
                            <input type="email" class="form-control" name="correo_puce" value="{{  old('correo_puce', $student_correo ?? '')  }}" readonly>
                        </div>

                        <div class="col-md-6">
                            <label>Celular</label>

                            {{-- 1. Usamos un "input-group" de Bootstrap --}}
                            <div class="input-group">

                                {{-- 2. El prefijo "09" fijo --}}
                                <span class="input-group-text">09</span>

                                {{-- 3. El input (solo para los 8 dígitos restantes) --}}
                                <input type="text"
                                    class="form-control"
                                    id="inputTelefonoSuffix"
                                    maxlength="8" {{--Maxima Longitud cambiada a 8 --}}
                                    inputmode="numeric"
                                    placeholder="Ej: 12345678"
                                    pattern="[0-9]{8}"
                                    required>
                            </div>

                            {{-- 4. Un campo oculto para enviar el número completo (09 + sufijo) --}}
                            <input type="hidden" name="telefono" id="inputTelefono">

                            <div id="errorTelefono" class="text-danger small mt-1" style="display:none;">
                                Debe ingresar los 8 dígitos restantes.
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label>Dirección</label>
                            <input type="text" class="form-control" name="direccion" placeholder="Ingrese la dirección de su domicilio" required>
                        </div>

                        <div class="col-md-6">
                            <label>Fecha de nacimiento</label>
                            <input type="date"
                                class="form-control"
                                name="fecha_nacimiento"
                                id="inputFechaNacimiento"
                                required>
                            <small class="text-muted" id="infoFecha" style="display:none;">Año bloqueado por edad.</small>
                        </div>
                    </div>
                </div>

                {{-- Paso 3: Agendamiento --}}
                <div class="form-step" style="display:none;">
                    <h5 class="text-primary mb-4 text-center">📅 Seleccione fecha y horario disponible</h5>
                    <div class="row justify-content-center g-4">

                        {{-- Columna izquierda: calendario y modalidad --}}
                        <div class="col-lg-6 col-md-7">

                            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">

                                <h6 class="fw-bold text-secondary mb-3">Selección de fecha y horario</h6>
                                <div class="mb-3">
                                    <label for="fechaSeleccionada" class="form-label fw-bold">Fecha disponible</label>
                                    <input type="date" id="fechaSeleccionada" class="form-control shadow-sm"
                                        min="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="bg-light text-center p-4 rounded-3 border mt-3">
                                    <i class="bi bi-calendar-date text-primary" style="font-size:2rem;"></i>
                                    <p class="mt-2 text-muted small">Seleccione una fecha para mostrar los turnos</p>
                                </div>
                            </div>
                        </div>

                        {{-- Columna derecha: horarios disponibles --}}
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
                    <input type="hidden" name="date_shift" id="date_shift">
                    <input type="hidden" name="shift_time" id="shift_time">
                    <input type="hidden" name="modalidad_shift" id="modalidad_shift">
                </div>

                {{-- Paso 4: Confirmación --}}
                <div class="form-step" style="display:none;">
                    @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif
                    <h5 class="text-secondary mb-3 text-center">Confirmación de Registro</h5>
                    <p><strong>Cédula:</strong> <span id="cedulaConfirm">-</span></p>
                    <p><strong>Nombres:</strong> <span id="namesConfirm">-</span></p>
                    <p><strong>Facultad:</strong> <span id="facultadConfirm">-</span></p>
                    <p><strong>Carrera:</strong> <span id="carreraConfirm">-</span></p>
                    <p><strong>Correo PUCE:</strong> <span id="correoConfirm">-</span></p>
                    <p><strong>Teléfono:</strong> <span id="telefonoConfirm">-</span></p>
                    <p><strong>Fecha seleccionada:</strong> <span id="fechaConfirm">-</span></p>
                    <p><strong>Horario:</strong> <span id="horarioConfirm">-</span></p>
                    <div class="text-muted small">Al presionar *Confirmar y Guardar* se registrará su turno y se enviará un correo.</div>
                </div>

                {{-- Navegación --}}
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" id="prevBtn" class="btn btn-outline-secondary">Anterior</button>
                    <button type="button" id="nextBtn" class="btn btn-primary" disabled>Siguiente</button>
                    <button type="submit" id="submitBtn" class="btn btn-success" style="display:none;">Confirmar y Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="bg-light text-center py-3 mt-4 border-top">
    <div class="container">
        <small>Desarrollado por la Dirección de Informática - Pontificia Universidad Católica del Ecuador</small>
    </div>
</div>

{{-- === Estilos personalizados === --}}
<style>
    .steps .step-item {
        flex: 1;
        position: relative;
        /* Convertimos el 'paso' en un contenedor flex vertical */
        display: flex;
        flex-direction: column;
        /* Apila los hijos (número y título) */
        align-items: center;
        /* Centra los hijos horizontalmente */
    }

    .steps .step-number {
        background-color: #dee2e6;
        color: #495057;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 6px;
        font-weight: bold;
    }

    .steps .step-item.active .step-number {
        background-color: #0d6efd;
        color: #fff;
    }

    .steps .step-title {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .steps .step-item.active .step-title {
        color: #0d6efd;
        font-weight: 600;
    }

    .progress {
        border-radius: 10px;
    }

    #progressBar {
        border-radius: 10px;
        transition: width 0.4s ease;
    }

    .form-step {
        animation: fadeIn 0.4s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
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
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        border-color: #0d6efd;
    }

    .turno-card.selected {
        border: 2px solid #0d6efd;
        background-color: #e8f0fe;
        box-shadow: 0 0 10px rgba(13, 110, 253, 0.2);
    }
</style>

<style>
    /* === Mejora visual de los combo box (selects) === */
    .form-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 10px;
        padding: 10px 38px 10px 14px;
        font-size: 0.95rem;
        color: #495057;
        line-height: 1.5;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.25s ease-in-out;
        background-image: url("data:image/svg+xml;utf8,<svg fill='%236c757d' viewBox='0 0 16 16' xmlns='http://www.w3.org/2000/svg'><path d='M4.646 6.646a.5.5 0 0 1 .708 0L8 9.293l2.646-2.647a.5.5 0 0 1 .708.708L8.354 10.354a.5.5 0 0 1-.708 0L4.646 7.354a.5.5 0 0 1 0-.708z'/></svg>");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 14px;
    }

    .form-select:hover {
        border-color: #86b7fe;
        box-shadow: 0 0 6px rgba(13, 110, 253, 0.15);
    }

    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        outline: none;
    }

    /* === Etiquetas y alineación === */
    label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 6px;
    }

    /* === Efecto suave en inputs generales === */
    .form-control,
    .form-select {
        transition: all 0.3s ease;
    }

    /* === Sombras suaves al enfocar === */
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.2);
    }

    /* === Versión centrada para selects del paso 5 === */
    #fechaSeleccionada {
        border-radius: 12px;
        max-width: 360px;
        font-weight: 500;
    }

    /* Mejor apariencia del paso de agendamiento */
    #turnosContainer .turno-card {
        flex: 1 1 40%;
        min-width: 160px;
        max-width: 220px;
    }

    @media (max-width: 768px) {
        #turnosContainer .turno-card {
            flex: 1 1 100%;
        }
    }
</style>


{{-- === Script de funcionalidad === --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const steps = document.querySelectorAll('.form-step');
        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const submitBtn = document.getElementById('submitBtn');
        const aceptaTerminos = document.getElementById('acepta_terminos');
        const aceptaPoliticas = document.getElementById('acepta_politicas');
        const stepIndicators = document.querySelectorAll('.step-item');
        const inputTelefonoSuffix = document.getElementById('inputTelefonoSuffix');
        const inputTelefonoFull = document.getElementById('inputTelefono');

        let currentStep = 0;

        function updateStepIndicator() {
            stepIndicators.forEach((s, i) => s.classList.toggle('active', i === currentStep));
        }

        function showStep(step) {
            steps.forEach((s, i) => s.style.display = (i === step) ? 'block' : 'none');
            prevBtn.style.display = step === 0 ? 'none' : 'inline-block';
            nextBtn.style.display = step === steps.length - 1 ? 'none' : 'inline-block';
            submitBtn.style.display = step === steps.length - 1 ? 'inline-block' : 'none';
            document.getElementById('progressBar').style.width = ((step + 1) / steps.length) * 100 + '%';
            updateStepIndicator();
            if (step === steps.length - 1) populateConfirmation();
        }

        function validarChecks() {
            nextBtn.disabled = !(aceptaTerminos.checked && aceptaPoliticas.checked);
        }

        // Eventos de ambos checks
        aceptaTerminos.addEventListener('change', validarChecks);
        aceptaPoliticas.addEventListener('change', validarChecks);

        // Botón desactivado al inicio
        nextBtn.disabled = true;

        // ✅ Botón SIGUIENTE con todas las validaciones
        nextBtn.onclick = async () => {
            if (!validateCurrentStep()) {
                return;
            }

            if (currentStep === 0) {
                if (!aceptaTerminos.checked || !aceptaPoliticas.checked) {
                    alert("Debes aceptar ambos términos para continuar.");
                    return;
                }
            }

            if (currentStep === 1) {
                // Validar teléfono completo
                if (inputTelefonoSuffix.value.length !== 8) {
                    alert("El teléfono debe tener 8 dígitos (sin contar el 09 inicial).");
                    inputTelefonoSuffix.focus();
                    return;
                }
                
                currentStep++;
                showStep(currentStep);
                return;
            }

            // Validación para el paso de agendamiento (paso 3 = índice 2)
            if (currentStep === 2) {
                const turnoSeleccionado = turnoIdInput.value;
                const fechaSeleccionada = dateShiftInput.value;

                if (!turnoSeleccionado) {
                    alert('Por favor seleccione un turno antes de continuar.');
                    return;
                }
                
                // Validar que no sea una fecha pasada
                const hoy = new Date().toISOString().split('T')[0];
                if (fechaSeleccionada < hoy) {
                    alert('No puede seleccionar fechas pasadas.');
                    return;
                }
                
                // Validar que si es hoy, la hora no sea pasada
                if (fechaSeleccionada === hoy) {
                    const ahora = new Date();
                    const horaActual = ahora.getHours().toString().padStart(2, '0') + ':' + 
                                    ahora.getMinutes().toString().padStart(2, '0');
                    const horaTurno = shiftTimeInput.value.split(' - ')[0];
                    
                    if (horaTurno <= horaActual) {
                        alert('No puede seleccionar un turno que ya ha pasado.');
                        return;
                    }
                }
            }

            currentStep++;
            showStep(currentStep);
        };

        // Botón ANTERIOR
        prevBtn.onclick = () => {
            currentStep--;
            showStep(currentStep);
        };

        // === CONTROL DE TELÉFONO COMBINADO ===
        if (inputTelefonoSuffix && inputTelefonoFull) {
            function actualizarTelefonoCompleto() {
                inputTelefonoFull.value = '09' + inputTelefonoSuffix.value;
            }
            inputTelefonoSuffix.addEventListener('input', actualizarTelefonoCompleto);
            actualizarTelefonoCompleto();
        }

        // === VALIDACIÓN DE CAMPOS REQUERIDOS ===
        function validateCurrentStep() {
            const step = steps[currentStep];
            const requireds = step.querySelectorAll('[required]');

            for (let el of requireds) {
                if (el.type === 'radio') {
                    const name = el.name;
                    if (!step.querySelector(`input[name="${name}"]:checked`)) {
                        el.focus();
                        return false;
                    }
                } else if (!el.value || el.value.trim() === '') {
                    el.focus();
                    return false;
                }
            }

            if (currentStep === 0) {
                if (!aceptaTerminos.checked || !aceptaPoliticas.checked) {
                    return false;
                }
            }

            return true;
        }

        // === AGENDAMIENTO ===
        const fechaInput = document.getElementById('fechaSeleccionada');
        const turnosContainer = document.getElementById('turnosContainer');
        const turnoIdInput = document.getElementById('turno_id');
        const dateShiftInput = document.getElementById('date_shift');
        const shiftTimeInput = document.getElementById('shift_time');
        const modalidadShiftInput = document.getElementById('modalidad_shift');

    function cargarTurnos() {
        const fecha = fechaInput.value;

        if (!fecha) {
            turnosContainer.innerHTML = '<div class="text-muted text-center"><em>Seleccione una fecha...</em></div>';
            return;
        }

        turnoIdInput.value = '';
        turnosContainer.innerHTML = '<p class="text-center"><span class="spinner-border spinner-border-sm me-2"></span>Cargando turnos...</p>';

        console.log('🔍 Cargando turnos para fecha:', fecha);

        fetch(`/shifts/${fecha}`)
            .then(res => {
                console.log('📡 Response status:', res.status);
                if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                return res.json();
            })
            .then(data => {
                console.log('📦 Turnos recibidos:', data);

                turnosContainer.innerHTML = '';

                if (!data.success || !Array.isArray(data.data) || data.data.length === 0) {
                    turnosContainer.innerHTML = `
                        <div class="alert alert-warning text-center w-100">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            No hay turnos disponibles para esta fecha.
                        </div>
                    `;
                    return;
                }

                // ✅ FILTRO PARA HORAS PASADAS DEL DÍA ACTUAL
                const hoy = new Date().toISOString().split('T')[0]; // Fecha actual YYYY-MM-DD
                const ahora = new Date();
                const horaActual = ahora.getHours().toString().padStart(2, '0') + ':' + 
                                ahora.getMinutes().toString().padStart(2, '0') + ':00';
                
                console.log('🕐 Hora actual:', horaActual);
                console.log('📅 Fecha seleccionada:', fecha);
                console.log('📅 Hoy:', hoy);

                let turnosDisponibles = data.data;
                
                // Si es hoy, filtrar por horas futuras
                if (fecha === hoy) {
                    turnosDisponibles = data.data.filter(turno => {
                        console.log(`⏰ Comparando: ${turno.start_shift} > ${horaActual} = ${turno.start_shift > horaActual}`);
                        return turno.start_shift > horaActual;
                    });
                    
                    console.log('✅ Turnos después del filtro:', turnosDisponibles.length);
                    
                    if (turnosDisponibles.length === 0) {
                        turnosContainer.innerHTML = `
                            <div class="alert alert-info text-center w-100">
                                <i class="bi bi-info-circle me-2"></i>
                                No hay turnos disponibles para el resto del día.<br>
                                <small>Por favor, seleccione otra fecha.</small>
                            </div>
                        `;
                        return;
                    }
                }

                turnosDisponibles.forEach(turno => {
                    const div = document.createElement('div');
                    div.className = 'turno-card text-center';
                    div.setAttribute('data-turno-id', turno.id_shift);
                    
                    // Mostrar indicador si es turno de hoy
                    const esHoy = fecha === hoy;
                    const horaIndicador = esHoy ? `<small class="text-success">• Hoy</small>` : '';
                    
                    div.innerHTML = `
                        <i class="bi bi-clock text-primary mb-2" style="font-size:1.5rem;"></i><br>
                        <strong class="d-block">${turno.start_shift}</strong>
                        <span class="text-muted small">a ${turno.end_shift}</span><br>
                        ${horaIndicador}
                        <div class="mt-2">
                            <span class="badge bg-light text-dark">${turno.cubiculo}</span>
                        </div>
                    `;

                    div.onclick = () => {
                        console.log('✅ Turno seleccionado:', turno.id_shift);

                        turnoIdInput.value = turno.id_shift;
                        dateShiftInput.value = data.fecha_consulta || fecha;
                        shiftTimeInput.value = turno.start_shift + ' - ' + turno.end_shift;
                        modalidadShiftInput.value = 'virtual';

                        document.querySelectorAll('.turno-card').forEach(c => c.classList.remove('selected'));
                        div.classList.add('selected');
                        
                        // ✅ Habilitar botón siguiente si estamos en paso de agendamiento
                        if (currentStep === 2) {
                            nextBtn.disabled = false;
                        }
                    };

                    turnosContainer.appendChild(div);
                });

                const totalInfo = document.createElement('div');
                totalInfo.className = 'w-100 text-center text-muted small mt-3';
                totalInfo.innerHTML = `<i class="bi bi-info-circle me-1"></i> ${turnosDisponibles.length} turno(s) disponible(s)`;
                turnosContainer.appendChild(totalInfo);
            })
            .catch(error => {
                console.error("❌ Error al cargar los turnos:", error);
                turnosContainer.innerHTML = `
                    <div class="alert alert-danger text-center w-100">
                        <i class="bi bi-x-circle me-2"></i>
                        Error al cargar los turnos.<br>
                        <small>Por favor intente nuevamente.</small>
                    </div>
                `;
            });
    }

        fechaInput.addEventListener('change', cargarTurnos);

        // === CONFIRMACIÓN FINAL ===
        function populateConfirmation() {
            document.getElementById('cedulaConfirm').textContent = document.querySelector('[name="cedula"]').value;
            document.getElementById('namesConfirm').textContent = document.querySelector('[name="names"]').value;
            document.getElementById('correoConfirm').textContent = document.querySelector('[name="correo_puce"]').value;
            document.getElementById('telefonoConfirm').textContent = document.querySelector('[name="telefono"]').value;
            document.getElementById('fechaConfirm').textContent = dateShiftInput.value || '-';
            document.getElementById('horarioConfirm').textContent = shiftTimeInput.value || '-';
            document.getElementById('facultadConfirm').textContent = '{{ session("student_facultad") }}' || '-';
            document.getElementById('carreraConfirm').textContent = '{{ session("student_carrera") }}' || '-';
        }

        // === CONTROL DE EDAD Y FECHA DE NACIMIENTO ===
        const inputEdad = document.getElementById('inputEdad');
        const inputFecha = document.getElementById('inputFechaNacimiento');
        const errorEdad = document.getElementById('errorEdad');
        const infoFecha = document.getElementById('infoFecha');

        if (inputEdad && inputFecha) {
            // Función para calcular año de nacimiento
            function calcularFechaPorEdad(edad) {
                const anioActual = new Date().getFullYear();
                const anioNacimiento = anioActual - edad;
                return {
                    anio: anioNacimiento,
                    primerDia: `${anioNacimiento}-01-01`,
                    ultimoDia: `${anioNacimiento}-12-31`
                };
            }

            // Función para validar y ajustar fecha
            function validarYAjustarFecha() {
                let edad = parseInt(inputEdad.value) || 0;

                // Validar rango de edad
                if (edad < 17 || edad > 80) {
                    inputEdad.classList.add('is-invalid');
                    if (errorEdad) errorEdad.style.display = 'block';
                    inputFecha.min = '';
                    inputFecha.max = '';
                    if (infoFecha) infoFecha.style.display = 'none';
                    return false;
                } else {
                    inputEdad.classList.remove('is-invalid');
                    if (errorEdad) errorEdad.style.display = 'none';
                }

                // Calcular fechas
                const { primerDia, ultimoDia, anio } = calcularFechaPorEdad(edad);
                
                // Restringir rango de fechas
                inputFecha.min = primerDia;
                inputFecha.max = ultimoDia;
                
                // ✅ FORZAR la fecha al 1 de enero (evita problemas de diciembre)
                if (!inputFecha.value || new Date(inputFecha.value).getFullYear() !== anio) {
                    inputFecha.value = primerDia;
                }

                // Mostrar información
                if (infoFecha) {
                    infoFecha.textContent = `Solo fechas del año ${anio}`;
                    infoFecha.style.display = 'block';
                }
                
                return true;
            }

            // Eventos
            inputEdad.addEventListener('input', validarYAjustarFecha);
            inputEdad.addEventListener('change', validarYAjustarFecha);
            
            // ✅ También validar cuando el usuario cambia la fecha manualmente
            inputFecha.addEventListener('change', function() {
                const edad = parseInt(inputEdad.value) || 0;
                if (edad < 17 || edad > 80) return;
                
                const anioNacimiento = new Date().getFullYear() - edad;
                const fechaSeleccionada = new Date(this.value).getFullYear();
                
                // Si el usuario selecciona una fecha de otro año, corregirla
                if (fechaSeleccionada !== anioNacimiento) {
                    alert(`Debe seleccionar una fecha del año ${anioNacimiento}`);
                    this.value = `${anioNacimiento}-01-01`;
                }
            });

            // ✅ Inicializar si ya hay un valor en edad al cargar la página
            if (inputEdad.value) {
                validarYAjustarFecha();
            }
        }
        showStep(currentStep);
    });
</script>

@stop