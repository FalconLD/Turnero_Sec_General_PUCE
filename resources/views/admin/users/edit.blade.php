@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
    <div class="container-fluid">
        <div class="row align-items-center">
            {{-- 1. Columna Izquierda: Botón de Volver --}}
            <div class="col-sm-3 col-md-2 text-left">
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fas fa-arrow-left mr-1"></i> Volver
                </a>
            </div>
            
            {{-- 2. Columna Central: Título Centrado --}}
            <div class="col-sm-6 col-md-8 text-center">
                <h1 class="text-dark font-weight-bold">Editar Usuario</h1>
            </div>

            {{-- 3. Columna Derecha: Espacio vacío para mantener el balance --}}
            <div class="col-sm-3 col-md-2"></div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        
                        {{-- Formulario apuntando a UPDATE --}}
                        <form action="{{ route('users.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <h5 class="mb-3 text-muted font-weight-bold text-uppercase small ls-1">Información Personal</h5>

                            {{-- Fila 1: Nombre y Correo --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Nombre Completo <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light"><i class="fas fa-user text-secondary"></i></span>
                                            </div>
                                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                                   value="{{ old('name', $user->name) }}" required>
                                        </div>
                                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Correo Electrónico <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light"><i class="fas fa-envelope text-secondary"></i></span>
                                            </div>
                                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                                   value="{{ old('email', $user->email) }}" required>
                                        </div>
                                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Fila 2: DNI y Contraseña --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="DNI">DNI / Cédula</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light"><i class="fas fa-id-card text-secondary"></i></span>
                                            </div>
                                            <input type="text" name="DNI" class="form-control @error('DNI') is-invalid @enderror" 
                                                   value="{{ old('DNI', $user->DNI) }}">
                                        </div>
                                         @error('DNI') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Contraseña</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light"><i class="fas fa-lock text-secondary"></i></span>
                                            </div>
                                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                                   placeholder="Dejar en blanco para mantener la actual">
                                        </div>
                                        <small class="form-text text-muted">Solo llene este campo si desea cambiar la contraseña.</small>
                                        @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            {{-- Fila 3: ASIGNACIÓN DE ROL (Bloque Idéntico al Crear pero con Preselección) --}}
                            <div class="row">
                                <div class="col-12">
                                    <div class="card card-outline card-success bg-light">
                                        <div class="card-header">
                                            <h3 class="card-title text-success font-weight-bold">
                                                <i class="fas fa-user-tag mr-2"></i>Asignar Rol y Permisos
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-0">
                                                <label for="role">Rol del Usuario <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-crown text-success"></i></span>
                                                    </div>
                                                    <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                                                        <option value="" disabled>-- Seleccione una opción --</option>
                                                        @foreach($roles as $role)
                                                            <option value="{{ $role->name }}" 
                                                                {{-- LÓGICA DE PRESELECCIÓN --}}
                                                                {{-- 1. Si hubo un error de validación (old) usamos ese valor --}}
                                                                {{-- 2. Si no, verificamos si el usuario ya tiene ese rol en la BD --}}
                                                                {{ (old('role') == $role->name) || ($user->hasRole($role->name) && empty(old('role'))) ? 'selected' : '' }}>
                                                                {{ $role->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('role') <span class="text-danger small">{{ $message }}</span> @enderror
                                                <small class="form-text text-muted mt-2">
                                                    <i class="fas fa-info-circle"></i> Al cambiar el rol, los permisos del usuario se actualizarán inmediatamente.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Botones de Acción --}}
                            <div class="row mt-4">
                                <div class="col-12 text-right">
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary mr-2">
                                        <i class="fas fa-times mr-1"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-sm">
                                        <i class="fas fa-sync-alt mr-2"></i> ACTUALIZAR USUARIO
                                    </button>
                                </div>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop