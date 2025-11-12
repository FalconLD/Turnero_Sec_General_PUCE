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

                    <li class="step-item" data-step="2">

                        <span class="step-number">3</span>

                        <span class="step-title">Académicos</span>

                    </li>

                    <li class="step-item" data-step="3">

                        <span class="step-number">4</span>

                        <span class="step-title">Pago</span>

                    </li>

                    <li class="step-item" data-step="4">

                        <span class="step-number">5</span>

                        <span class="step-title">Agendamiento</span>

                    </li>

                    <li class="step-item" data-step="5">

                        <span class="step-number">6</span>

                        <span class="step-title">Confirmación</span>

                    </li>

                </ul>

            </div>



            {{-- Barra de progreso --}}

            <div class="progress mb-4" style="height: 8px;">

                <div id="progressBar" class="progress-bar bg-primary" style="width: 0%;"></div>

            </div>



            <form action="{{ route('student.finish') }}" method="POST" enctype="multipart/form-data" id="multiStepForm">

                @csrf



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
                            <input type="text" class="form-control" name="names" value="{{ old('names') }}" readonly>
                        </div>

                        <div class="col-md-3">

                            <label>Cédula</label>
                            <input type="text" class="form-control" name="cedula" value="{{ old('cedula') }}" readonly>
                        </div>

                        <div class="col-md-3">

                            <label>Edad</label>

                            <input type="number" class="form-control" name="edad" min="1" required>

                        </div>

                        <div class="col-md-6">

                            <label>Correo electrónico</label>
                            <input type="email" class="form-control" name="correo_puce" value="{{ old('correo_puce') }}" readonly>
                        </div>

                        <div class="col-md-6">

                            <label>Celular</label>

                            <input type="text" class="form-control" name="telefono" maxlength="10" pattern="\d{10}"

                                title="Debe contener exactamente 10 dígitos numéricos" required>

                        </div>

                        <div class="col-md-12">

                            <label>Dirección</label>

                            <input type="text" class="form-control" name="direccion" required>

                        </div>

                        <div class="col-md-6">

                            <label>Fecha de nacimiento</label>

                            <input type="date" class="form-control" name="fecha_nacimiento" required>

                        </div>

                    </div>

                </div>

                {{-- Paso 3: Académicos --}}
                <div class="form-step" style="display:none;">
                    <h5 class="text-secondary mb-3">Datos Académicos</h5>
                    <div class="row g-3">

                        {{-- 1. Nivel de Instrucción (EL TRIGGER) --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Nivel de instrucción</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="nivel_instruccion" id="tec" value="tec" required>
                                <label class="form-check-label" for="tec">Tec</label>
                            </div>                            
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="nivel_instruccion" id="grado" value="grado" required>
                                <label class="form-check-label" for="grado">Grado</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="nivel_instruccion" id="posgrado" value="posgrado" required>
                                <label class="form-check-label" for="posgrado">Posgrado</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="nivel_instruccion" id="especializacion" value="especializacion" required>
                                <label class="form-check-label" for="especializacion">Especialización</label>
                            </div>
                        </div>

                        {{-- 2. Facultad (Se llenará con AJAX) --}}
                        <div class="col-md-6">
                            <label>Facultad</label>
                            <select class="form-select" name="facultad" id="facultad_select" required disabled>
                                <option value="" selected disabled>Seleccione primero un nivel...</option>
                                {{-- Opciones se agregarán con JS --}}
                            </select>
                        </div>

                        {{-- 3. Carrera (Se llenará con AJAX) --}}
                        <div class="col-md-12">
                            <label>Carrera</label>
                            {{-- CAMBIADO DE INPUT A SELECT --}}
                            <select class="form-select" name="carrera" id="carrera_select" required disabled>
                                <option value="" selected disabled>Seleccione primero una facultad...</option>
                                {{-- Opciones se agregarán con JS --}}
                            </select>
                        </div>

                        {{-- 4. Nivel (Semestre) (Se oculta/muestra) --}}
                        {{-- ID 'nivel_semestre_container' es nuevo --}}
                        <div class="col-md-12" id="nivel_semestre_container" style="display:none;">
                            <label>Nivel (Semestre)</label>
                            {{-- El name="nivel" original. ID 'nivel_semestre_select' es nuevo --}}
                            <select class="form-select" name="nivel" id="nivel_semestre_select" required>
                                <option value="" selected disabled>Seleccione...</option>
                                <option>Primero</option>
                                <option>Segundo</option>
                                <option>Tercero</option>
                                <option>Cuarto</option>
                                <option>Quinto</option>
                                <option>Sexto</option>
                                <option>Séptimo</option>
                                <option>Octavo</option>
                                <option>Noveno</option>
                                <option>Décimo</option>
                            </select>
                        </div>

                        {{-- 5. Beca (Se oculta/muestra) --}}
                        {{-- El ID 'beca_pregunta_container' es nuevo --}}
                        <div class="col-md-12 mb-3" id="beca_pregunta_container" style="display:none;">
                            <label class="form-label fw-semibold">¿Cuenta con beca San Ignacio?</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="beca_san_ignacio" id="beca_si" value="si">
                                <label class="form-check-label" for="beca_si">Sí</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="beca_san_ignacio" id="beca_no" value="no">
                                <label class="form-check-label" for="beca_no">No</label>
                            </div>
                        </div>

                        {{-- 6. Mensaje de Pago --}}
                        <div class="col-md-12 text-center">
                            <p id="mensaje_pago" class="text-primary fw-semibold"></p>
                        </div>
                    </div>
                </div>



                {{-- Paso 4: Pago --}}

                <div class="form-step" style="display:none;">

                    <h5 class="text-secondary mb-3">Datos de Pago y Motivo</h5>

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label>Tipo de pago</label>

                            <select class="form-select" id="tipo-pago" name="forma_pago" required>

                                <option value="" selected disabled>Seleccione...</option>

                                <option value="una_sola_vez">DE UNA</option>

                                <option value="transferencia">TRANSFERENCIA</option>

                                <option value="efectivo">Pago en Efectivo</option>

                            </select>

                        </div>

                        <div class="col-md-12" id="comprobante-container" style="display:none;">

                            <label>Subir comprobante</label>

                            <input type="file" class="form-control" accept=".pdf,.jpg,.png" name="comprobante">

                        </div>

                        <div class="alert alert-info" id="pago-efectivo-note" style="display:none;">

                            Una vez finalizada la inscripción, acérquese 10 minutos antes al Centro Médico de Fundación PuceSalud.

                        </div>

                        <div class="col-md-12">

                            <label>Motivo de consulta</label>

                            <textarea class="form-control" name="motivo" rows="3" required></textarea>

                        </div>

                    </div>

                </div>



                {{-- Paso 5: Agendamiento --}}

               {{-- Paso 5: Agendamiento --}}

                    <div class="form-step" style="display:none;">

                        <h5 class="text-primary mb-4 text-center">📅 Seleccione modalidad, fecha y horario disponible</h5>



                        <div class="row justify-content-center g-4">

                            {{-- Columna izquierda: calendario y modalidad --}}

                            <div class="col-lg-6 col-md-7">

                                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">

                                    <h6 class="fw-bold text-secondary mb-3">Configuración de cita</h6>



                                    <div class="mb-3">

                                        <label for="modalidadSelect" class="form-label fw-bold">Modalidad</label>

                                        <select id="modalidadSelect" class="form-select shadow-sm" required>

                                            <option value="" selected disabled>Seleccione...</option>

                                            <option value="presencial">Presencial</option>

                                            <option value="virtual">Virtual</option>

                                        </select>

                                    </div>



                                    <div class="mb-3">

                                        <label for="fechaSeleccionada" class="form-label fw-bold">Fecha disponible</label>

                                        <input type="date" id="fechaSeleccionada" class="form-control shadow-sm"

                                            min="{{ date('Y-m-d') }}" required>

                                    </div>



                                    {{-- Aquí podrías colocar un calendario visual si luego integras librerías como FullCalendar --}}

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







                {{-- Paso 6: Confirmación --}}

                <div class="form-step" style="display:none;">

                    @if (session('error'))

                        <div class="alert alert-danger">

                            {{ session('error') }}

                        </div>

                    @endif

                    <h5 class="text-secondary mb-3 text-center">Confirmación de Registro</h5>

                    <p><strong>Cédula:</strong> <span id="cedulaConfirm">-</span></p>

                    <p><strong>Nombres:</strong> <span id="namesConfirm">-</span></p>

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



{{-- === Estilos personalizados === --}}

<style>

    .steps .step-item {

        flex: 1;

        position: relative;

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

        from {opacity: 0; transform: translateY(10px);}

        to {opacity: 1; transform: translateY(0);}

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

        box-shadow: 0 2px 4px rgba(0,0,0,0.05);

        transition: all 0.25s ease-in-out;

        background-image: url("data:image/svg+xml;utf8,<svg fill='%236c757d' viewBox='0 0 16 16' xmlns='http://www.w3.org/2000/svg'><path d='M4.646 6.646a.5.5 0 0 1 .708 0L8 9.293l2.646-2.647a.5.5 0 0 1 .708.708L8.354 10.354a.5.5 0 0 1-.708 0L4.646 7.354a.5.5 0 0 1 0-.708z'/></svg>");

        background-repeat: no-repeat;

        background-position: right 12px center;

        background-size: 14px;

    }



    .form-select:hover {

        border-color: #86b7fe;

        box-shadow: 0 0 6px rgba(13,110,253,0.15);

    }



    .form-select:focus {

        border-color: #0d6efd;

        box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.25);

        outline: none;

    }



    /* === Etiquetas y alineación === */

    label {

        font-weight: 500;

        color: #495057;

        margin-bottom: 6px;

    }



    /* === Efecto suave en inputs generales === */

    .form-control, .form-select {

        transition: all 0.3s ease;

    }



    /* === Sombras suaves al enfocar === */

    .form-control:focus {

        border-color: #0d6efd;

        box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.2);

    }



    /* === Versión centrada para selects del paso 5 === */

    #modalidadSelect, #fechaSeleccionada {

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

        const stepIndicators = document.querySelectorAll('.step-item');

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



        aceptaTerminos.addEventListener('change', () => nextBtn.disabled = !aceptaTerminos.checked);



        // ✅ MODIFICADO: validación AJAX en el paso 2

        nextBtn.onclick = async () => {

            if (!validateCurrentStep()) return;



            // Si estamos en el paso 2 (índice 1)

            if (currentStep === 1) {

                const cedula = document.querySelector('[name="cedula"]').value.trim();

                const correo = document.querySelector('[name="correo_puce"]').value.trim();



                try {

                    const res = await fetch("{{ route('validar.datos') }}", {

                        method: "POST",

                        headers: {

                            "Content-Type": "application/json",

                            "X-CSRF-TOKEN": "{{ csrf_token() }}"

                        },

                        body: JSON.stringify({ cedula: cedula, correo_puce: correo })

                    });



                    const data = await res.json();



                    if (!data.success) {

                        alert(data.message); // ⚠️ Mensaje si ya existe

                        return; // No avanza

                    }

                } catch (error) {

                    console.error("Error al validar los datos:", error);

                    alert("Ocurrió un error al validar los datos. Intenta nuevamente.");

                    return;

                }

            }



            // ✅ Si pasa la validación, continúa al siguiente paso

            currentStep++;

            showStep(currentStep);

        };



        prevBtn.onclick = () => { currentStep--; showStep(currentStep); };



        function validateCurrentStep() {

            const step = steps[currentStep];

            const requireds = step.querySelectorAll('[required]');

            for (let el of requireds) {

                if (el.type === 'radio') {

                    const name = el.name;

                    if (!step.querySelector(`input[name="${name}"]:checked`)) { el.focus(); return false; }

                } else if (!el.value || el.value.trim() === '') { el.focus(); return false; }

            }

            return true;

        }



        // === LÓGICA NIVEL / BECA / PAGO ===

        const nivelRadios = document.querySelectorAll('input[name="nivel_instruccion"]');
        
        // Contenedores dinámicos
        const becaPreguntaContainer = document.getElementById('beca_pregunta_container');
        const nivelSemestreContainer = document.getElementById('nivel_semestre_container');
        const nivelSemestreSelect = document.getElementById('nivel_semestre_select');
        const becaRadios = document.querySelectorAll('input[name="beca_san_ignacio"]');
        const mensajePago = document.getElementById('mensaje_pago');

        // Selects dinámicos
        const facultadSelect = document.getElementById('facultad_select');
        const carreraSelect = document.getElementById('carrera_select');

        // ---Helper para poblar selects ---
        function populateSelect(selectElement, items, valueField, textField, defaultOptionText) {
            selectElement.innerHTML = `<option value="" selected disabled>${defaultOptionText}</option>`;
            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item[valueField];
                option.textContent = item[textField];
                selectElement.appendChild(option);
            });
            selectElement.disabled = false;
        }

        // ---Helper para resetear selects ---
        function resetSelect(selectElement, defaultOptionText) {
            selectElement.innerHTML = `<option value="" selected disabled>${defaultOptionText}</option>`;
            selectElement.disabled = true;
        }

         // ---actualizarPago ---   
        function actualizarPago() {
            const nivelSeleccionado = document.querySelector('input[name="nivel_instruccion"]:checked')?.value;
            const becaSeleccionada = document.querySelector('input[name="beca_san_ignacio"]:checked')?.value;

            if (!nivelSeleccionado) return;

            // --- GRUPO 1: Grado y Tec (Muestran beca y nivel de semestre) ---
            if (nivelSeleccionado === 'grado' || nivelSeleccionado === 'tec') {
                
                // Muestra Beca y Nivel(semestre)
                if (becaPreguntaContainer) becaPreguntaContainer.style.display = "block";
                if (nivelSemestreContainer) nivelSemestreContainer.style.display = "block";

                // Añade 'required' a los campos visibles
                nivelSemestreSelect.required = true;
                document.querySelectorAll('input[name="beca_san_ignacio"]').forEach(r => r.required = true);

                // Lógica de pago (Asumiendo que 'Tec' cuesta igual que 'Grado')
                if (becaSeleccionada === 'si') {
                    mensajePago.textContent = "Pago de $0.50 (Atención Psicológica Única - APSU con beca)";
                } else if (becaSeleccionada === 'no') {
                    mensajePago.textContent = "Pago de $2.50 (Atención Psicológica Única - APSU)";
                } else {
                    mensajePago.textContent = "Seleccione si cuenta con beca para mostrar el valor a pagar.";
                }

            // --- GRUPO 2: Posgrado y Especialización (Ocultan todo) ---
            } else if (nivelSeleccionado === 'posgrado' || nivelSeleccionado === 'especializacion') {
                
                // Oculta Beca y Nivel(semestre)
                if (becaPreguntaContainer) becaPreguntaContainer.style.display = "none";
                if (nivelSemestreContainer) nivelSemestreContainer.style.display = "none";
                
                // Quita 'required' de los campos ocultos
                nivelSemestreSelect.required = false; 
                document.querySelectorAll('input[name="beca_san_ignacio"]').forEach(r => r.required = false);

                // Selecciona automáticamente "No" en la beca (lógica de backend)
                if (document.getElementById('beca_no')) {
                    document.getElementById('beca_no').checked = true;
                }

                // Lógica de pago (Asumiendo que 'Especialización' cuesta igual que 'Posgrado')
                mensajePago.textContent = "Pago de $7.50 (Atención Psicológica Única - APSU)";
            
            } else {
                mensajePago.textContent = "";
            }

            mensajePago.classList.add("fs-5", "text-success", "mt-3");
        }



        // --- NUEVOS EVENT LISTENERS (AJAX) ---

        // 1. Cuando cambia "Nivel de Instrucción" (Grado/Posgrado)
        nivelRadios.forEach(radio => {
            radio.addEventListener('change', async (e) => {
                const nivelVal = e.target.value; // 'grado' o 'posgrado'

                // A) Ejecutar la lógica de visibilidad y pago
                actualizarPago(); 
                
                // B) Resetear y deshabilitar los selects dependientes
                resetSelect(facultadSelect, 'Cargando facultades...');
                resetSelect(carreraSelect, 'Seleccione primero una facultad...');

                // C) Buscar facultades vía AJAX
                try {
                    // Usamos la ruta que creamos en web.php
                    const response = await fetch(`{{ route('get.faculties') }}?nivel_instruccion=${nivelVal}`);
                    if (!response.ok) throw new Error('Error al cargar facultades');
                    const faculties = await response.json();
                    
                    // D) Poblar el select de facultades
                    populateSelect(facultadSelect, faculties, 'facultad', 'facultad', 'Seleccione una facultad...');
                } catch (error) {
                    console.error(error);
                    resetSelect(facultadSelect, 'Error al cargar facultades');
                }
            });
        });

        // 2. Cuando cambia "Facultad"
        facultadSelect.addEventListener('change', async (e) => {
            const facultadVal = e.target.value;
            const nivelVal = document.querySelector('input[name="nivel_instruccion"]:checked')?.value;

            if (!facultadVal || !nivelVal) return;

            // A) Resetear y deshabilitar el select de carrera
            resetSelect(carreraSelect, 'Cargando carreras...');

            // B) Buscar carreras vía AJAX
            try {
                // Usamos la ruta que creamos en web.php
                const response = await fetch(`{{ route('get.programs') }}?nivel_instruccion=${nivelVal}&facultad=${facultadVal}`);
                if (!response.ok) throw new Error('Error al cargar carreras');
                const programs = await response.json();

                // C) Poblar el select de carreras
                populateSelect(carreraSelect, programs, 'programa_desc', 'programa_desc', 'Seleccione una carrera...');
            } catch (error) {
                console.error(error);
                resetSelect(carreraSelect, 'Error al cargar carreras');
            }
        });

        becaRadios.forEach(r => r.addEventListener('change', actualizarPago));



        // === PAGO Y COMPROBANTE ===

        const tipoPagoSelect = document.getElementById('tipo-pago');

        const comprobanteContainer = document.getElementById('comprobante-container');

        const pagoEfectivoNote = document.getElementById('pago-efectivo-note');



        tipoPagoSelect.addEventListener('change', () => {

            const val = tipoPagoSelect.value;

            comprobanteContainer.style.display = (val === 'una_sola_vez' || val === 'transferencia') ? 'block' : 'none';

            pagoEfectivoNote.style.display = val === 'efectivo' ? 'block' : 'none';

        });



        // === AGENDAMIENTO ===

        const fechaInput = document.getElementById('fechaSeleccionada');

        const modalidadSelect = document.getElementById('modalidadSelect');

        const turnosContainer = document.getElementById('turnosContainer');

        const turnoIdInput = document.getElementById('turno_id');

        const dateShiftInput = document.getElementById('date_shift');

        const shiftTimeInput = document.getElementById('shift_time');

        const modalidadShiftInput = document.getElementById('modalidad_shift');



        async function cargarTurnos() {

            const fecha = fechaInput.value;

            const modalidad = modalidadSelect.value;



            if (!fecha || !modalidad) {

                turnosContainer.innerHTML = "<p class='text-muted'>Seleccione modalidad y fecha para ver los turnos...</p>";

                return;

            }



            turnosContainer.innerHTML = "<p class='text-muted'>Cargando turnos disponibles...</p>";

            turnoIdInput.value = '';

            dateShiftInput.value = '';

            shiftTimeInput.value = '';

            modalidadShiftInput.value = '';

            nextBtn.disabled = true;



            try {

                const res = await fetch(`/shifts/${fecha}?modalidad=${modalidad}`);

                if (!res.ok) throw new Error('No se pudo cargar los turnos');

                const data = await res.json();



                if (!Array.isArray(data) || data.length === 0) {

                    turnosContainer.innerHTML = "<p class='text-danger'>No hay turnos disponibles para esta fecha y modalidad.</p>";

                    return;

                }



                turnosContainer.innerHTML = '';

                data.forEach(t => {

                    const boton = document.createElement("div");

                    boton.className = "card turno-card m-2 p-3 text-center";

                    boton.style.cursor = "pointer";

                    boton.style.width = "180px";

                    boton.innerHTML = `

                        <h5 class="mb-2">${t.start_shift.substring(0,5)} - ${t.end_shift.substring(0,5)}</h5>

                        <p class="mb-1 fw-bold">${t.cubiculo}</p>

                        <small class="text-muted">${t.tipo_atencion}</small>

                    `;



                    boton.addEventListener('click', () => {

                        document.querySelectorAll('.turno-card').forEach(c => c.classList.remove('border-primary', 'bg-light'));

                        boton.classList.add('border-primary', 'bg-light');

                        turnoIdInput.value = t.id_shift;

                        dateShiftInput.value = fecha;

                        shiftTimeInput.value = `${t.start_shift.substring(0,5)} - ${t.end_shift.substring(0,5)}`;

                        modalidadShiftInput.value = modalidad;

                        nextBtn.disabled = false;

                    });



                    turnosContainer.appendChild(boton);

                });



            } catch (err) {

                console.error(err);

                turnosContainer.innerHTML = "<p class='text-danger'>Error al cargar los turnos.</p>";

            }

        }



        fechaInput.addEventListener('change', cargarTurnos);

        modalidadSelect.addEventListener('change', cargarTurnos);



        // === CONFIRMACIÓN FINAL ===

        function populateConfirmation() {

            document.getElementById('cedulaConfirm').textContent = document.querySelector('[name="cedula"]').value;

            document.getElementById('namesConfirm').textContent = document.querySelector('[name="names"]').value;

            document.getElementById('correoConfirm').textContent = document.querySelector('[name="correo_puce"]').value;

            document.getElementById('telefonoConfirm').textContent = document.querySelector('[name="telefono"]').value;

            document.getElementById('fechaConfirm').textContent = dateShiftInput.value || '-';

            document.getElementById('horarioConfirm').textContent = shiftTimeInput.value || '-';

        }



        showStep(currentStep);

    });

</script>





@stop