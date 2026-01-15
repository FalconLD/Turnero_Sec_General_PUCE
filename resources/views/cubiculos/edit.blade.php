@extends('adminlte::page')

@section('title', 'Editar Cubículo')

@section('content_header')
    {{-- Título de la página --}}
    <h1 class="text-center">Editar Cubículo</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        {{-- 
                          CAMBIOS EN EL FORMULARIO:
                          1. action: apunta a 'cubiculos.update' pasándole el $cubiculo.
                          2. id: cambiado a 'cubiculo-form' (para JS).
                          3. @method('PUT'): requerido para la actualización.
                        --}}
                        <form action="{{ route('cubiculos.update', ['modulo' => $cubiculo->id]) }}" method="POST" id="cubiculo-form">
                            @csrf
                            @method('PUT')

                            {{-- Fila Principal: Columna Izquierda (Usuario y Área) y Columna Derecha (Nombre del Cubículo) --}}
                            <div class="row">
                                {{-- Columna Izquierda: Usuario Asignado y Área Operativa --}}
                                <div class="col-md-6">
                                    {{-- Usuario Asignado con filtro inteligente --}}
                                    <div class="form-group">
                                        <label for="user_id">Usuario Asignado <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user-check"></i></span>
                                            </div>
                                            <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                                <option value="">-- Seleccionar Usuario --</option>
                                                @foreach ($users as $user)
                                                    {{-- CAMBIO: Comparamos con $cubiculo->user_id y agregamos data-areas para el filtro --}}
                                                    <option value="{{ $user->id }}" 
                                                        {{ old('user_id', $cubiculo->user_id) == $user->id ? 'selected' : '' }}
                                                        data-areas='{{ $user->operatingAreas->map(function($a){ return ["id" => $a->id, "name" => $a->name]; })->toJson() }}'>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('user_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Área de Atención / Facultad con filtro inteligente --}}
                                    <div class="form-group">
                                        <label for="operating_area_id">Área de Atención / Facultad <span class="text-danger">*</span></label>
                                        <select name="operating_area_id" id="operating_area_id" class="form-control @error('operating_area_id') is-invalid @enderror" required>
                                            <option value="">-- Seleccionar Área --</option>
                                            {{-- Se llenará dinámicamente con JS según el usuario seleccionado --}}
                                        </select>
                                        @error('operating_area_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Columna Derecha: GRUPO "Nombre del Cubículo" --}}
                                <div class="col-md-6">
                                    <div class="cubiculo-name-container">
                                        <h6 class="font-weight-bold text-center" style="margin-bottom: 0.9rem;">Nombre del Cubículo</h6>
                                        <div class="row">
                                            
                                            {{-- Columna para el Prefijo --}}
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="prefijo">Prefijo <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                                        </div>
                                                        <select name="prefijo" id="prefijo" class="form-control @error('prefijo') is-invalid @enderror" required>
                                                            {{-- 
                                                              CAMBIO: 
                                                              Usamos old('prefijo', $prefijo) para comparar.
                                                              Se da prioridad a 'old()' (si falló la validación)
                                                              y si no, usa '$prefijo' (el valor de la DB).
                                                            --}}
                                                            <option value="" disabled>-- P --</option>
                                                            <option value="C -" {{ old('prefijo', $prefijo) == 'C -' ? 'selected' : '' }}>C -</option>
                                                            <option value="P1 -" {{ old('prefijo', $prefijo) == 'P1 -' ? 'selected' : '' }}>P1 -</option>
                                                            <option value="P -" {{ old('prefijo', $prefijo) == 'P -' ? 'selected' : '' }}>P -</option>
                                                            <option value="SALA-1 -" {{ old('prefijo', $prefijo) == 'SALA-1 -' ? 'selected' : '' }}>SALA-1 -</option>
                                                            <option value="SALA -" {{ old('prefijo', $prefijo) == 'SALA -' ? 'selected' : '' }}>SALA -</option>
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
                                                    <label for="numero">Número <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                        </div>
                                                        <input type="text" 
                                                            name="numero" 
                                                            id="numero" 
                                                            class="form-control @error('numero') is-invalid @enderror" 
                                                            {{-- CAMBIO: Usamos old('numero', $numero) --}}
                                                            value="{{ old('numero', $numero) }}" 
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
                            </div>

                            {{-- Fila para Tipo de Atención y Campo Dinámico --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo_atencion">Tipo de Atención <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-list-ul"></i></span>
                                            </div>
                                            <select name="tipo_atencion" id="tipo_atencion" class="form-control @error('tipo_atencion') is-invalid @enderror" required>
                                                <option value="">-- Seleccionar tipo --</option>
                                                {{-- CAMBIO: Comparamos con $cubiculo->tipo_atencion --}}
                                                <option value="virtual" {{ old('tipo_atencion', $cubiculo->tipo_atencion) == 'virtual' ? 'selected' : '' }}>Virtual</option>                                            </select>
                                        </div>
                                        @error('tipo_atencion')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                
                                {{-- Campo dinámico (Enlace o Ubicación) --}}
                                <div class="col-md-6" id="campo_extra_wrapper" style="display: none;">
                                    <div class="form-group">
                                        <label id="campo_extra_label"></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="campo_extra_icon"></span>
                                            </div>
                                            {{-- CAMBIO: Usamos $cubiculo->enlace_o_ubicacion --}}
                                            <input type="text" name="enlace_o_ubicacion" id="campo_extra_input" class="form-control @error('enlace_o_ubicacion') is-invalid @enderror" value="{{ old('enlace_o_ubicacion', $cubiculo->enlace_o_ubicacion) }}">
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
                                {{-- CAMBIO: Texto del botón --}}
                                <button type="submit" class="btn btn-primary">Actualizar</button>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

{{-- 
  CSS y JS con la lógica del filtro inteligente y validaciones
--}}

@push('css')
    <style>
    /* Este es el estilo para el contenedor que agrupa "Prefijo" y "Número" */
    .cubiculo-name-container {
        background-color: #f8f9fa; 
        border: 1px solid #e9ecef;   
        border-radius: 0.35rem;     
        padding: 1rem;             
    }
    </style>
@endpush

@push('js')
    <script>
        $(document).ready(function() {
            const userSelect = $('#user_id');
            const areaSelect = $('#operating_area_id');
            const areaActual = "{{ $cubiculo->operating_area_id }}"; // ID del área que ya tiene el cubículo

            function cargarAreas() {
                // 1. Obtener la opción seleccionada
                const selectedOption = userSelect.find('option:selected');
                
                // 2. Extraer el JSON de áreas del usuario
                const areasData = selectedOption.data('areas');

                // 3. Limpiar el selector de áreas
                areaSelect.empty().append('<option value="">-- Seleccionar Área --</option>');

                // 4. Si hay áreas, llenar el selector
                if (areasData && areasData.length > 0) {
                    areasData.forEach(function(area) {
                        // Marcamos como 'selected' si coincide con el área actual del cubículo
                        const isSelected = (area.id == areaActual) ? 'selected' : '';
                        areaSelect.append(`<option value="${area.id}" ${isSelected}>${area.name}</option>`);
                    });
                } else if (userSelect.val() !== "") {
                    areaSelect.append('<option value="" disabled>Este usuario no tiene áreas asignadas en la DB</option>');
                }
            }

            // 1. Ejecutar al cargar la página (para que aparezca el área actual)
            cargarAreas();

            // 2. Ejecutar cada vez que cambien el usuario
            userSelect.on('change', cargarAreas);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            const cubiculoForm = document.getElementById('cubiculo-form'); 
            const tipoSelect = document.getElementById('tipo_atencion');
            const campoWrapper = document.getElementById('campo_extra_wrapper');
            const label = document.getElementById('campo_extra_label');
            const input = document.getElementById('campo_extra_input');
            const icon = document.getElementById('campo_extra_icon');

            // --- FUNCIÓN MODIFICADA PARA EDICIÓN ---
            function actualizarCampoDinamico() {
                const tipo = tipoSelect.value;
                const valorActual = input.value.trim();
                const urlPattern = new RegExp('^(https://|http://)'); // Patrón de URL

                if (tipo === 'virtual') {
                    campoWrapper.style.display = 'block';
                    label.textContent = 'Enlace de conexión (Zoom, Meet, etc.)';
                    input.placeholder = 'https://...';
                    icon.innerHTML = '<i class="fas fa-link"></i>';

                    // 👇 NUEVA LÓGICA 👇
                    // Si el valor que hay NO es una URL (ej. "Piso 2"), lo borramos.
                    if (valorActual !== '' && !urlPattern.test(valorActual)) {
                        input.value = ''; 
                    }

                } else if (tipo === 'presencial') {
                    campoWrapper.style.display = 'block';
                    label.textContent = 'Ubicación del cubículo';
                    input.placeholder = 'Ejemplo: Edificio Central, piso 2...';
                    icon.innerHTML = '<i class="fas fa-map-marker-alt"></i>';

                    // 👇 NUEVA LÓGICA 👇
                    // Si el valor que hay SÍ es una URL (ej. "http://zoom.com"), lo borramos.
                    if (valorActual !== '' && urlPattern.test(valorActual)) {
                        input.value = ''; 
                    }

                } else {
                    campoWrapper.style.display = 'none';
                    input.value = ''; // Limpiamos si seleccionan "Seleccionar tipo"
                }
            }
            // --- FIN DE LA FUNCIÓN MODIFICADA ---
            
            tipoSelect.addEventListener('change', actualizarCampoDinamico);
            // Esta línea es clave: ejecuta la nueva lógica de limpieza en cuanto la página carga
            actualizarCampoDinamico(); 


            // --- VALIDACIÓN AL ENVIAR EL FORMULARIO ---
            cubiculoForm.addEventListener('submit', function (event) {
                const tipo = tipoSelect.value;
                const valorCampo = input.value.trim();
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

            // Quita la clase de error cuando el usuario corrija
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