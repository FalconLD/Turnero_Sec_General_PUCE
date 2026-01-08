@extends('adminlte::page')

@section('title', 'Nuevo Cubículo')

@section('content_header')
    <h1 class="text-center">Crear Cubículo</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('cubiculos.store') }}" method="POST" id="create-cubiculo-form">
                            @csrf

                            {{-- Fila para Nombre y Tipo de Atención --}}
                            <div class="row">
                                {{-- Columna para el GRUPO "Nombre del Cubículo" --}}
                                    <div class="col-md-6">
                                        <div class="cubiculo-name-container">
                                            {{-- Título del grupo (Centrado) --}}
                                            <h6 class="font-weight-bold text-center" style="margin-bottom: 0.9rem;">Nombre del Cubículo</h6>
                                            {{-- Fila interna para los dos campos (esta ya la tienes) --}}
                                            <div class="row">
                                                
                                                {{-- Columna para el Prefijo --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">

                                                        {{-- Mantenemos su label original para claridad --}}
                                                        <label for="prefijo">Prefijo <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                                            </div>
                                                            <select name="prefijo" id="prefijo" class="form-control @error('prefijo') is-invalid @enderror" required>
                                                                <option value="" disabled {{ old('prefijo') ? '' : 'selected' }}>-- P --</option>
                                                                <option value="C -" {{ old('prefijo') == 'C -' ? 'selected' : '' }}>C -</option>
                                                                <option value="P1 -" {{ old('prefijo') == 'P1 -' ? 'selected' : '' }}>P1 -</option>
                                                                <option value="P -" {{ old('prefijo') == 'P -' ? 'selected' : '' }}>P -</Hoption>
                                                                <option value="SALA-1 -" {{ old('prefijo') == 'SALA-1 -' ? 'selected' : '' }}>SALA-1 -</option>
                                                                <option value="SALA -" {{ old('prefijo') == 'SALA -' ? 'selected' : '' }}>SALA -</option>
                                                            </select>
                                                        </div>
                                                        @error('prefijo')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- Columna para el Número --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        {{-- Mantenemos su label original para claridad --}}
                                                        <label for="numero">Número <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                            </div>
                                                            <input type="text" 
                                                                name="numero" 
                                                                id="numero" 
                                                                class="form-control @error('numero') is-invalid @enderror" 
                                                                value="{{ old('numero') }}" 
                                                                placeholder="Ej: 001" 
                                                                pattern="[0-9]{3}" 
                                                                maxlength="3" 
                                                                inputmode="numeric" 
                                                                title="Debe ingresar un número de 3 dígitos (ej: 001, 101)." 
                                                                required>
                                                        </div>
                                                        @error('numero')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                <div class="col-md-6">
                                    <h6 class="font-weight-bold" style="margin-bottom: 0.9rem; visibility: hidden;">&nbsp;</h6>
                                    <div class="form-group">
                                        <label for="tipo_atencion">Tipo de Atención <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-list-ul"></i></span>
                                            </div>
                                            <select name="tipo_atencion" id="tipo_atencion" class="form-control @error('tipo_atencion') is-invalid @enderror" required>
                                                <option value="">-- Seleccionar tipo --</option>
                                                <option value="virtual" {{ old('tipo_atencion') == 'virtual' ? 'selected' : '' }}>Virtual</option>
                                                <!-- <option value="presencial" {{ old('tipo_atencion') == 'presencial' ? 'selected' : '' }}>Presencial</option> -->
                                            </select>
                                        </div>
                                        @error('tipo_atencion')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Fila para Usuario Asignado y Campo Dinámico --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="user_id">Usuario Asignado <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user-check"></i></span>
                                            </div>
                                            <select name="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                                <option value="">-- Seleccionar Usuario --</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('user_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                
                                {{-- Campo dinámico --}}
                                <div class="col-md-6" id="campo_extra_wrapper" style="display: none;">
                                    <div class="form-group">
                                        <label id="campo_extra_label"></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="campo_extra_icon"></span>
                                            </div>
                                            <input type="text" name="enlace_o_ubicacion" id="campo_extra_input" class="form-control @error('enlace_o_ubicacion') is-invalid @enderror" value="{{ old('enlace_o_ubicacion') }}">
                                        </div>
                                        @error('enlace_o_ubicacion')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Botones de Acción --}}
                            <div class="text-right">
                                <a href="{{ route('cubiculos.index') }}" class="btn btn-danger">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('css')
<style>
/* Este es el estilo para el contenedor 
  que agrupará "Prefijo" y "Número"
*/
.cubiculo-name-container {
    background-color: #f8f9fa; /* Un fondo gris muy claro (opaco) */
    border: 1px solid #e9ecef;   /* Un borde sutil */
    border-radius: 0.35rem;     /* Bordes redondeados */
    padding: 1rem;             /* Espaciado interno */
}
</style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // Referencias a los elementos del DOM
            const createForm = document.getElementById('create-cubiculo-form'); // El formulario
            const tipoSelect = document.getElementById('tipo_atencion');
            const campoWrapper = document.getElementById('campo_extra_wrapper');
            const label = document.getElementById('campo_extra_label');
            const input = document.getElementById('campo_extra_input');
            const icon = document.getElementById('campo_extra_icon');

            // --- Función para mostrar/ocultar el campo dinámico ---
            function actualizarCampoDinamico() {
                const tipo = tipoSelect.value;
                if (tipo === 'virtual') {
                    campoWrapper.style.display = 'block';
                    label.textContent = 'Enlace de conexión (Zoom, Meet, etc.)';
                    input.placeholder = 'https://...';
                    icon.innerHTML = '<i class="fas fa-link"></i>';
                } else if (tipo === 'presencial') {
                    campoWrapper.style.display = 'block';
                    label.textContent = 'Ubicación del cubículo';
                    input.placeholder = 'Ejemplo: Edificio Central, piso 2...';
                    icon.innerHTML = '<i class="fas fa-map-marker-alt"></i>';
                } else {
                    campoWrapper.style.display = 'none';
                    input.value = '';
                }
            }
            
            tipoSelect.addEventListener('change', actualizarCampoDinamico);
            actualizarCampoDinamico();


            // --- VALIDACIÓN AL ENVIAR EL FORMULARIO ---
            createForm.addEventListener('submit', function (event) {
                const tipo = tipoSelect.value;
                const valorCampo = input.value.trim(); // Usamos .trim()
                
                // Expresión regular para verificar si empieza con http:// o https://
                const urlPattern = new RegExp('^(https://|http://)'); 

                if (tipo === 'virtual') {
                    // Si es virtual, DEBE ser un enlace válido
                    if (valorCampo === '' || !urlPattern.test(valorCampo)) {
                        event.preventDefault(); 
                        alert('Error: Para atención virtual, debe ingresar un enlace válido (ej: https://...).');
                        input.classList.add('is-invalid');
                    }
                
                } else if (tipo === 'presencial') {
                    // Si es presencial, NO DEBE ser un enlace
                    if (valorCampo !== '' && urlPattern.test(valorCampo)) {
                        event.preventDefault(); 
                        alert('Error: Para atención presencial, la ubicación no puede ser un enlace. Ingrese solo texto (ej: Piso 2, Oficina 10).');
                        input.classList.add('is-invalid');
                    }
                }
            });


            //Quita la clase de error cuando el usuario corrija
            input.addEventListener('input', function() {
                if(input.classList.contains('is-invalid')) {
                    input.classList.remove('is-invalid');
                }
            });

            // Seleccionamos el campo de número
            const inputNumero = document.getElementById('numero');

            if (inputNumero) {

                /**
                 * 1. Bloqueo Proactivo (keydown)
                 * Bloquea teclas antes de que se escriban.
                 */
                inputNumero.addEventListener('keydown', function (event) {
                    
                    // Permite teclas de control: Backspace, Tab, Delete, Flechas, Home, End
                    if ([
                        'Backspace', 'Tab', 'Delete', 'ArrowLeft', 'ArrowRight', 'Home', 'End'
                    ].includes(event.key)) {
                        return; // Deja que la tecla funcione
                    }

                    // Permite atajos comunes (Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X / Cmd+A...)
                    if ((event.ctrlKey || event.metaKey) && ['a', 'c', 'v', 'x'].includes(event.key.toLowerCase())) {
                        return;
                    }

                    // Si la tecla presionada NO es un dígito (0-9)
                    if (!/^[0-9]$/.test(event.key)) {
                        event.preventDefault(); // Detiene la acción (la tecla no se escribe)
                    }
                });

                /**
                 * 2. Limpieza Reactiva (input) - La "Red de Seguridad"
                 * Limpia el valor si algo se filtra (ej. al pegar con el mouse).
                 */
                inputNumero.addEventListener('input', function (event) {
                    // Reemplaza todo lo que NO sea un dígito por una cadena vacía
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }


        });
    </script>


@endpush