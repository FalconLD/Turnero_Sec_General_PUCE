@extends('adminlte::page')

@section('title', 'Registro de Estudiante')

@section('content_header')
<h1 class="text-center mb-4">Registro de Estudiante</h1>
@stop

@section('content')
<div class="container d-flex justify-content-center">
    <div class="card shadow-lg w-75 border-0">
        <div class="card-body">
            <h4 class="card-title text-center mb-4 text-primary">Formulario de Inscripción</h4>

            <form action="{{ route('student.store') }}" method="POST" enctype="multipart/form-data">
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
                        <input type="checkbox" id="acepta_terminos" name="acepta_terminos">
                        <label  for="acepta_terminos">Acepto los términos y condiciones</label>
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

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nivel de instrucción</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="nivel_instruccion" value="grado">
                                    <label class="form-check-label">Grado</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="nivel_instruccion" value="posgrado">
                                    <label class="form-check-label">Posgrado</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3" id="beca-group">
                            <label class="form-label">¿Pertenece al grupo de beca San Ignacio?</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="beca_san_ignacio" value="si">
                                    <label class="form-check-label">Sí</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="beca_san_ignacio" value="no">
                                    <label class="form-check-label">No</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3 text-center" id="valor-pagar-box" style="display:none; font-size: 1.2em; color: #0d6efd;"></div>
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
                                <option value="DeUna">DeUna</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Pago en Efectivo">Pago en Efectivo</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3" id="comprobante-container" style="display:none;">
                            <label>Subir comprobante</label>
                            <input type="file" class="form-control" accept=".pdf,.jpg,.png" name="comprobante">
                        </div>

                        <div class="alert alert-info" id="pago-efectivo-note" style="display:none;">
                            Pago en efectivo: Una vez finalizada la inscripción, por favor acercarse 10 minutos antes en el día programado para su atención, al Centro Médico de Fundación PuceSalud, en la Pontificia Universidad Católica del Ecuador, diagonal a la biblioteca.
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Describa brevemente el motivo de su consulta</label>
                            <textarea class="form-control" name="motivo" rows="3" placeholder="Ingrese una breve descripción..." required></textarea>
                        </div>
                    </div>
                </div>

                {{-- Paso 5: Confirmación --}}
                <div class="form-step" style="display:none;">
                    <h5 class="text-secondary mb-3">Confirmación</h5>
                    <p>Verifique que todos los datos sean correctos antes de enviar su información.</p>
                    <div class="alert alert-info">
                        <strong>Nota:</strong> Una vez enviado, no podrá modificar los datos ingresados.
                    </div>
                </div>

                {{-- Botones --}}
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" id="prevBtn" class="btn btn-outline-secondary">Anterior</button>
                    <button type="button" id="nextBtn" class="btn btn-primary" disabled>Siguiente</button>
                    <button type="submit" id="submitBtn" class="btn btn-success" style="display:none;">Enviar</button>
                </div>

                {{-- Línea de tiempo --}}
                <div class="mt-4 text-center">
                    <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                        <div id="stepIcon1" class="step-circle bg-secondary text-white">1</div>
                        <div class="line"></div>
                        <div id="stepIcon2" class="step-circle bg-secondary text-white">2</div>
                        <div class="line"></div>
                        <div id="stepIcon3" class="step-circle bg-secondary text-white">3</div>
                        <div class="line"></div>
                        <div id="stepIcon4" class="step-circle bg-secondary text-white">4</div>
                    </div>
                    <small class="text-muted d-block mt-2">Progreso del registro</small>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.step-circle { width: 35px; height: 35px; border-radius: 50%; display: flex; justify-content: center; align-items: center; transition: all 0.3s; }
.line { height: 3px; width: 50px; background-color: #ccc; }
.step-circle.active { background-color: #0d6efd !important; transform: scale(1.1); }
.step-circle.completed { background-color: #198754 !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const steps = document.querySelectorAll('.form-step');
    const icons = [
        document.getElementById('stepIcon1'),
        document.getElementById('stepIcon2'),
        document.getElementById('stepIcon3'),
        document.getElementById('stepIcon4')
    ];

    let currentStep = 0;

    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');

    function showStep(index) {
        steps.forEach((step, i) => step.style.display = i === index ? 'block' : 'none');
        prevBtn.style.display = index === 0 ? 'none' : 'inline-block';
        nextBtn.style.display = index === steps.length - 1 ? 'none' : 'inline-block';
        document.getElementById('submitBtn').style.display = index === steps.length - 1 ? 'inline-block' : 'none';
        document.getElementById('progressBar').style.width = ((index + 1) / steps.length) * 100 + '%';
        icons.forEach((icon, i) => {
            icon.classList.remove('active', 'completed');
            if(i < index) icon.classList.add('completed');
            if(i === index) icon.classList.add('active');
        });
    }

    nextBtn.addEventListener('click', () => {
        if(currentStep < steps.length - 1) currentStep++;
        showStep(currentStep);
    });

    prevBtn.addEventListener('click', () => {
        if(currentStep > 0) currentStep--;
        showStep(currentStep);
    });

    // Habilitar siguiente solo si se aceptan términos
    const aceptaTerminos = document.getElementById('acepta_terminos');
    aceptaTerminos.addEventListener('change', function() {
        nextBtn.disabled = !this.checked;
    });

    // Mostrar/ocultar grupo beca y valor a pagar
    document.addEventListener('change', function() {
        const nivel = document.querySelector('input[name="nivel_instruccion"]:checked');
        const becaGroup = document.getElementById('beca-group');
        const beca = document.querySelector('input[name="beca_san_ignacio"]:checked');
        const box = document.getElementById('valor-pagar-box');

        if(nivel) {
            if(nivel.value === 'posgrado') {
                becaGroup.style.display = 'none';
                becaGroup.querySelectorAll('input').forEach(r => r.checked = false);
                box.style.display = 'block';
                box.innerHTML = "<strong>VALOR A PAGAR</strong><br>$7.50";
            } else {
                becaGroup.style.display = 'block';
                if(beca) {
                    box.style.display = 'block';
                    box.innerHTML = beca.value === 'si' ? "<strong>VALOR A PAGAR</strong><br>$0.50" : "<strong>VALOR A PAGAR</strong><br>$2.50";
                } else {
                    box.style.display = 'none';
                }
            }
        }
    });

    // Mostrar campo de comprobante o nota según tipo de pago
    const tipoPagoSelect = document.getElementById('tipo-pago');
    const comprobanteContainer = document.getElementById('comprobante-container');
    const pagoEfectivoNote = document.getElementById('pago-efectivo-note');

    tipoPagoSelect.addEventListener('change', function() {
        const valor = this.value;
        if(valor === 'DeUna' || valor === 'Transferencia') {
            comprobanteContainer.style.display = 'block';
            pagoEfectivoNote.style.display = 'none';
        } else if(valor === 'Pago en Efectivo') {
            comprobanteContainer.style.display = 'none';
            pagoEfectivoNote.style.display = 'block';
        } else {
            comprobanteContainer.style.display = 'none';
            pagoEfectivoNote.style.display = 'none';
        }
    });

    showStep(currentStep);
});
</script>
@stop
