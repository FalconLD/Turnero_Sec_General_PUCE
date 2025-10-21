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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre">Nombre del Cubículo <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-person-booth"></i></span>
                                            </div>
                                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required placeholder="Ej: C-101">
                                        </div>
                                        @error('nombre')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo_atencion">Tipo de Atención <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-list-ul"></i></span>
                                            </div>
                                            <select name="tipo_atencion" id="tipo_atencion" class="form-control @error('tipo_atencion') is-invalid @enderror" required>
                                                <option value="">-- Seleccionar tipo --</option>
                                                <option value="virtual" {{ old('tipo_atencion') == 'virtual' ? 'selected' : '' }}>Virtual</option>
                                                <option value="presencial" {{ old('tipo_atencion') == 'presencial' ? 'selected' : '' }}>Presencial</option>
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


            // --- NUEVA VALIDACIÓN AL ENVIAR EL FORMULARIO ---
            createForm.addEventListener('submit', function (event) {
                const tipo = tipoSelect.value;
                const valorEnlace = input.value;
                
                // Expresión regular simple para verificar si empieza con http:// o https://
                const urlPattern = new RegExp('^(https://|http://)'); 

                if (tipo === 'virtual') {
                    if (valorEnlace.trim() === '' || !urlPattern.test(valorEnlace)) {
                        
                        // 1. Detiene el envío del formulario
                        event.preventDefault(); 
                        
                        // 2. Muestra un alerta al usuario
                        alert('Error: Para atención virtual, debe ingresar un enlace válido (ej: https://...).');
                        
                        // 3. Resalta el campo con error
                        input.classList.add('is-invalid');
                    }
                }
            });

            // Opcional: Quita la clase de error cuando el usuario corrija
            input.addEventListener('input', function() {
                if(input.classList.contains('is-invalid')) {
                    input.classList.remove('is-invalid');
                }
            });
        });
    </script>
@endpush