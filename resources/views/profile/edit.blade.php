@extends('adminlte::page')

{{-- Título de la página --}}
@section('title', 'Editar Perfil')

{{-- Contenido del encabezado --}}
@section('content_header')
    <h1 class="m-0 text-dark text-center">Editar mi Perfil</h1>
@stop

{{-- Contenido principal de la página --}}
@section('content')
    <div class="row justify-content-center">
        
        <div class="col-md-6"> {{-- La tarjeta principal se mantiene a la mitad --}}

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i>
                        Información de tu Cuenta
                    </h3>
                </div>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        {{-- Mensaje de éxito --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        {{-- Fila de Nombre y Correo (se mantiene igual) --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nombre:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" name="name" id="name" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    @error('name')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Correo Electrónico:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input type="email" name="email" id="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               value="{{ old('email', $user->email) }}" required readonly>
                                    </div>
                                    @error('email')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>
                        <p class="text-muted text-center">Dejar en blanco si no deseas cambiar la contraseña:</p>

                        <div class="row justify-content-center">
                            <div class="col-md-8"> {{-- Este 8/12 hace que sea angosto --}}
                                {{-- Nueva Contraseña --}}
                                <div class="form-group">
                                    <label for="password">Nueva Contraseña:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        
                                        <input type="password" name="password" id="password" 
                                            class="form-control @error('password') is-invalid @enderror"
                                            autocomplete="new-password"> {{-- Buena práctica --}}
                                        
                                        {{-- INICIO DE LA MODIFICACIÓN --}}
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                            </button>
                                        </div>
                                        {{-- FIN DE LA MODIFICACIÓN --}}

                                    </div>
                                    @error('password')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row justify-content-center">
                            <div class="col-md-8"> {{-- Mismo ancho que el de arriba --}}
                                {{-- Confirmar Contraseña --}}
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmar Nueva Contraseña:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>

                                        <input type="password" name="password_confirmation" 
                                            id="password_confirmation" class="form-control"
                                            autocomplete="new-password"> {{-- Buena práctica --}}

                                        {{-- INICIO DE LA MODIFICACIÓN --}}
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                                <i class="fas fa-eye" id="togglePasswordConfirmationIcon"></i>
                                            </button>
                                        </div>
                                        {{-- FIN DE LA MODIFICACIÓN --}}

                                    </div>
                                </div>
                            </div>
                        </div>
                        

                    </div> {{-- Fin .card-body --}}

                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Perfil
                        </button>
                    </div> {{-- Fin .card-footer --}}

                </form>
            </div> {{-- Fin .card --}}
        </div> {{-- Fin .col-md-6 --}}
    </div> {{-- Fin .row --}}
@stop

@section('js')
    <script>
        $(document).ready(function() {

            // --- Lógica para el campo 'Nueva Contraseña' ---
            $('#togglePassword').on('click', function() {
                // Obtener el input de contraseña
                var passwordInput = $('#password');
                var passwordIcon = $('#togglePasswordIcon');

                // Comprobar el tipo de input
                if (passwordInput.attr('type') === 'password') {
                    // Mostrar contraseña
                    passwordInput.attr('type', 'text');
                    // Cambiar icono a 'ojo tachado'
                    passwordIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    // Ocultar contraseña
                    passwordInput.attr('type', 'password');
                    // Cambiar icono a 'ojo'
                    passwordIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // --- Lógica para el campo 'Confirmar Nueva Contraseña' ---
            $('#togglePasswordConfirmation').on('click', function() {
                // Obtener el input de contraseña
                var passwordInput = $('#password_confirmation');
                var passwordIcon = $('#togglePasswordConfirmationIcon');

                // Comprobar el tipo de input
                if (passwordInput.attr('type') === 'password') {
                    // Mostrar contraseña
                    passwordInput.attr('type', 'text');
                    // Cambiar icono a 'ojo tachado'
                    passwordIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    // Ocultar contraseña
                    passwordInput.attr('type', 'password');
                    // Cambiar icono a 'ojo'
                    passwordIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

        });
    </script>
@stop