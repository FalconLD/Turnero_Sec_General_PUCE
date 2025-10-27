@extends('adminlte::page')

@section('title', 'Registro de Estudiante')
@section('layout_topnav', true)

@section('content_header')
    <h1 class="text-center mb-4">Registro de Estudiante</h1>
@stop

@section('content')
<div class="container d-flex justify-content-center">
    <div class="card shadow-lg w-75 border-0">
        <div class="card-body">
            <h4 class="card-title text-center mb-4 text-primary">Formulario de Inscripción</h4>

            <form action="{{ route('student.finish') }}" method="POST" enctype="multipart/form-data" id="multiStepForm">
                @csrf

                {{-- Barra de progreso --}}
                <div class="progress mb-4" style="height: 8px;">
                    <div id="progressBar" class="progress-bar bg-primary" style="width: 0%;"></div>
                </div>

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
                    <div class="form-check mb-3 text-center">
                        <input type="checkbox" id="acepta_terminos" name="acepta_terminos" value="1">
                        <label for="acepta_terminos">Acepto los términos y condiciones</label>
                    </div>
                </div>

                {{-- Paso 2: Datos personales --}}
                <div class="form-step" style="display:none;">
                    <h5 class="text-secondary mb-3">Datos Personales y de Contacto</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Nombre completo</label>
                            <input type="text" class="form-control" name="names" placeholder="Ingrese su nombre" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Cédula</label>
                            <input type="text" class="form-control" name="cedula" placeholder="Ingrese su cédula" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Edad</label>
                            <input type="number" class="form-control" name="edad" placeholder="Ej. 20" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Correo electrónico</label>
                            <input type="email" class="form-control" name="correo_puce" placeholder="ejemplo@correo.com" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Teléfono</label>
                            <input type="text" class="form-control" name="telefono" placeholder="Ingrese su número" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Dirección</label>
                            <input type="text" class="form-control" name="direccion" placeholder="Ingrese su dirección" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Fecha de nacimiento</label>
                            <input type="date" class="form-control" name="fecha_nacimiento" required>
                        </div>
                    </div>
                </div>

                {{-- Paso 3: Datos académicos --}}
                <div class="form-step" style="display:none;">
                    <h5 class="text-secondary mb-3">Datos Académicos</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Facultad</label>
                            <select class="form-select" name="facultad" required>
                                <option value="" selected disabled>Seleccione...</option>
                                <option>Facultad de Ingeniería</option>
                                <option>Facultad de Ciencias Humanas</option>
                                <option>Facultad de Medicina</option>
                                <option>Facultad de Ciencias Administrativas</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Carrera</label>
                            <input type="text" class="form-control" name="carrera" placeholder="Ej. Ingeniería de Sistemas" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Nivel</label>
                            <select class="form-select" name="nivel" required>
                                <option value="" selected disabled>Seleccione...</option>
                                <option>Primero</option>
                                <option>Segundo</option>
                                <option>Tercero</option>
                                <option>Cuarto</option>
                                <option>Quinto</option>
                                <option>Sexto</option>
                            </select>
                        </div>

                        {{-- Nivel de instrucción --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nivel de instrucción</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="nivel_instruccion" value="grado" required>
                                    <label class="form-check-label">Grado</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="nivel_instruccion" value="posgrado" required>
                                    <label class="form-check-label">Posgrado</label>
                                </div>
                            </div>
                        </div>

                        {{-- Beca --}}
                        <div class="col-md-12 mb-3" id="beca-group">
                            <label class="form-label">¿Pertenece al grupo de beca San Ignacio?</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="beca_san_ignacio" value="si" required>
                                    <label class="form-check-label">Sí</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="beca_san_ignacio" value="no" required>
                                    <label class="form-check-label">No</label>
                                </div>
                            </div>
                        </div>

                        {{-- Mensaje de pago dinámico --}}
                        <div class="col-md-12 mb-3 text-center">
                            <p id="mensaje_pago" class="text-primary"></p>
                        </div>
                    </div>
                </div>

                {{-- Paso 4: Pago y motivo --}}
                <div class="form-step" style="display:none;">
                    <h5 class="text-secondary mb-3">Datos de Pago y Motivo</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Tipo de pago a realizar</label>
                            <select class="form-select" id="tipo-pago" name="forma_pago" required>
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="una_sola_vez">DE UNA</option>
                                <option value="transferencia">TRANSFERENCIA</option>
                                <option value="efectivo">Pago en Efectivo</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3" id="comprobante-container" style="display:none;">
                            <label>Subir comprobante (si aplica)</label>
                            <input type="file" class="form-control" accept=".pdf,.jpg,.png" name="comprobante">
                        </div>

                        <div class="alert alert-info" id="pago-efectivo-note" style="display:none;">
                            Una vez finalizada la inscripción, por favor acercarse 10 minutos antes en el día programado para su atención, al Centro Médico de Fundación PuceSalud, en la Pontificia Universidad Católica del Ecuador, diagonal a la biblioteca.
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Describa brevemente el motivo de su consulta</label>
                            <textarea class="form-control" name="motivo" rows="3" placeholder="Ingrese una breve descripción..." required></textarea>
                        </div>
                    </div>
                </div>

                {{-- Paso 5 y 6 se mantienen igual --}}
                <div class="form-step p-4 bg-light rounded shadow-sm" style="display:none;">
                 <h5 class="text-primary mb-4 text-center">📅 Seleccione una fecha y horario disponible</h5>
                
                <div class="mb-4">
                    <label for="fechaSeleccionada" class="form-label fw-bold">Fecha:</label>
                    <input type="date" id="fechaSeleccionada" class="form-control" min="{{ date('Y-m-d') }}">
                </div>
                
                        <div id="turnosContainer" class="d-flex flex-wrap justify-content-center gap-3 mt-3">
                            <div class="text-center p-3 border rounded text-muted" style="width:200px;">
                                Seleccione una fecha para ver los turnos disponibles...
                            </div>
                        </div>
                        
                        <input type="hidden" name="turno_id" id="turno_id">
                        <input type="hidden" name="date_shift" id="date_shift">
                        <input type="hidden" name="shift_time" id="shift_time">
                    </div>

                    <!-- Opcional: estilo para hover en turnos -->
                    <style>
                        .turno-card {
                            cursor: pointer;
                            transition: transform 0.2s, box-shadow 0.2s;
                        }
                        .turno-card:hover {
                            transform: translateY(-3px);
                            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                        }
                        .turno-card.selected {
                            border-color: #0d6efd;
                            background-color: #e7f1ff;
                        }
                    </style>


                <div class="form-step" style="display:none;">
                    <h5 class="text-secondary mb-3 text-center">Confirmación de Registro</h5>
                    <p><strong>Cédula:</strong> <span id="cedulaConfirm">-</span></p>
                    <p><strong>Nombres:</strong> <span id="namesConfirm">-</span></p>
                    <p><strong>Correo PUCE:</strong> <span id="correoConfirm">-</span></p>
                    <p><strong>Teléfono:</strong> <span id="telefonoConfirm">-</span></p>
                    <p><strong>Fecha seleccionada:</strong> <span id="fechaConfirm">-</span></p>
                    <p><strong>Horario:</strong> <span id="horarioConfirm">-</span></p>
                    <div class="text-muted small">Al presionar *Confirmar y Guardar* se registrará su turno y se enviará un correo.</div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" id="prevBtn" class="btn btn-outline-secondary">Anterior</button>
                    <button type="button" id="nextBtn" class="btn btn-primary" disabled>Siguiente</button>
                    <button type="submit" id="submitBtn" class="btn btn-success" style="display:none;">Confirmar y Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const steps = document.querySelectorAll('.form-step');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    const aceptaTerminos = document.getElementById('acepta_terminos');

    let currentStep = 0;

    function showStep(step) {
        steps.forEach((s, i) => s.style.display = (i === step) ? 'block' : 'none');
        prevBtn.style.display = step === 0 ? 'none' : 'inline-block';
        nextBtn.style.display = step === steps.length - 1 ? 'none' : 'inline-block';
        submitBtn.style.display = step === steps.length - 1 ? 'inline-block' : 'none';
        document.getElementById('progressBar').style.width = ((step + 1) / steps.length) * 100 + '%';
        if(step === steps.length - 1) populateConfirmation();
    }

    aceptaTerminos.addEventListener('change', () => nextBtn.disabled = !aceptaTerminos.checked);

    nextBtn.onclick = () => { if(!validateCurrentStep()) return; currentStep++; showStep(currentStep); };
    prevBtn.onclick = () => { currentStep--; showStep(currentStep); };

    function validateCurrentStep() {
        const step = steps[currentStep];
        const requireds = step.querySelectorAll('[required]');
        for(let el of requireds){
            if(el.type === 'radio'){
                const name = el.name;
                if(!step.querySelector(`input[name="${name}"]:checked`)) { el.focus(); return false; }
            } else if(!el.value || el.value.trim() === ''){ el.focus(); return false; }
        }
        return true;
    }

    // --- Mensajes dinámicos ---
    const nivelRadios = document.querySelectorAll('input[name="nivel_instruccion"]');
    const becaRadios = document.querySelectorAll('input[name="beca_san_ignacio"]');
    const mensajePago = document.getElementById('mensaje_pago');
    const tipoPagoSelect = document.getElementById('tipo-pago');
    const comprobanteContainer = document.getElementById('comprobante-container');
    const pagoEfectivoNote = document.getElementById('pago-efectivo-note');

    function actualizarMensajePago() {
        const nivel = document.querySelector('input[name="nivel_instruccion"]:checked')?.value;
        const beca = document.querySelector('input[name="beca_san_ignacio"]:checked')?.value;

        if(nivel === 'grado' && beca === 'si'){
            mensajePago.textContent = "Dentro de los lineamientos de la Atención Psicológica Única (APSU) se establece el pago de $ 0.50 (cero cincuenta centavos)";
        } else if(nivel === 'grado' && beca === 'no'){
            mensajePago.textContent = "Dentro de los lineamientos de la Atención Psicológica Única (APSU) se establece el pago de $ 2.50 (dos con cincuenta)";
        } else if(nivel === 'posgrado'){
            mensajePago.textContent = "Dentro de los lineamientos de la Atención Psicológica Única (APSU) se establece el pago de $ 7.50 (siete con cincuenta)";
        } else { mensajePago.textContent = ""; }
    }

    nivelRadios.forEach(r => r.addEventListener('change', actualizarMensajePago));
    becaRadios.forEach(r => r.addEventListener('change', actualizarMensajePago));

    tipoPagoSelect.addEventListener('change', () => {
        const val = tipoPagoSelect.value;
        if(val === 'una_sola_vez' || val === 'transferencia'){
            comprobanteContainer.style.display = 'block';
            pagoEfectivoNote.style.display = 'none';
        } else if(val === 'efectivo'){
            comprobanteContainer.style.display = 'none';
            pagoEfectivoNote.style.display = 'block';
        } else{
            comprobanteContainer.style.display = 'none';
            pagoEfectivoNote.style.display = 'none';
        }
    });

    // --- TURNOS ---
    const fechaInput = document.getElementById('fechaSeleccionada');
    const turnosContainer = document.getElementById('turnosContainer');
    const turnoIdInput = document.getElementById('turno_id');
    const dateShiftInput = document.getElementById('date_shift');
    const shiftTimeInput = document.getElementById('shift_time');

    fechaInput.addEventListener('change', () => {
        const fecha = fechaInput.value;
        if(!fecha) return;
        turnosContainer.innerHTML = "<p class='text-muted'>Cargando turnos disponibles...</p>";
        turnoIdInput.value = ''; dateShiftInput.value = ''; shiftTimeInput.value = '';
        nextBtn.disabled = true;

        fetch(`/shifts/${fecha}`)
            .then(res => res.json())
            .then(data => {
                if(!Array.isArray(data) || data.length === 0){
                    turnosContainer.innerHTML = "<p class='text-danger'>No hay turnos disponibles para esta fecha.</p>";
                    return;
                }
                turnosContainer.innerHTML = '';
                data.forEach(t => {
                    const boton = document.createElement("button");
                    boton.type = "button";
                    boton.className = "btn btn-outline-primary m-2 turno-btn";
                    const horaTxt = t.start_shift.substring(0,5) + " - " + t.end_shift.substring(0,5);
                    boton.innerHTML = `<strong>${horaTxt}</strong><br><small>${t.cubicle_shift}</small>`;
                    boton.dataset.id = t.id_shift;
                    boton.dataset.hora = horaTxt;
                    boton.addEventListener('click', function(){
                        document.querySelectorAll('.turno-btn').forEach(b => b.classList.remove('btn-success'));
                        this.classList.add('btn-success');
                        turnoIdInput.value = this.dataset.id;
                        dateShiftInput.value = fecha;
                        shiftTimeInput.value = this.dataset.hora;
                        nextBtn.disabled = false;
                    });
                    turnosContainer.appendChild(boton);
                });
            })
            .catch(err => {
                console.error(err);
                turnosContainer.innerHTML = "<p class='text-danger'>Error al cargar los turnos.</p>";
            });
    });

    function populateConfirmation(){
        document.getElementById('cedulaConfirm').textContent = document.querySelector('input[name="cedula"]').value || '-';
        document.getElementById('namesConfirm').textContent = document.querySelector('input[name="names"]').value || '-';
        document.getElementById('correoConfirm').textContent = document.querySelector('input[name="correo_puce"]').value || '-';
        document.getElementById('telefonoConfirm').textContent = document.querySelector('input[name="telefono"]').value || '-';
        document.getElementById('fechaConfirm').textContent = dateShiftInput.value || '-';
        document.getElementById('horarioConfirm').textContent = shiftTimeInput.value || '-';
    }

    showStep(currentStep);
    actualizarMensajePago();
});
</script>
@stop
