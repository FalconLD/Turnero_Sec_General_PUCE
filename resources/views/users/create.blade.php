@extends('adminlte::page')

@section('title', 'Crear Usuario')

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
                <h1 class="text-dark font-weight-bold">Crear Nuevo Usuario</h1>
            </div>

            {{-- 3. Columna Derecha: Espacio vacío para balancear el centrado --}}
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
                        
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf

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
                                                   value="{{ old('name') }}" required placeholder="Ej: Juan Pérez">
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
                                                   value="{{ old('email') }}" required placeholder="Ej: juan.perez@puce.edu.ec">
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
                                                   value="{{ old('DNI') }}" placeholder="Ej: 1712345678">
                                        </div>
                                         @error('DNI') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Contraseña <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light"><i class="fas fa-lock text-secondary"></i></span>
                                            </div>
                                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                                   required placeholder="Mínimo 6 caracteres">
                                        </div>
                                        @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            {{-- Fila 3: ASIGNACIÓN DE ROL (Nuevo Bloque) --}}
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
                                                <label for="role">Seleccione el Rol para este Usuario <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-crown text-success"></i></span>
                                                    </div>
                                                    <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                                                        <option value="" disabled selected>-- Seleccione una opción --</option>
                                                        @foreach($roles as $role)
                                                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                                                {{ $role->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('role') <span class="text-danger small">{{ $message }}</span> @enderror
                                                <small class="form-text text-muted mt-2">
                                                    <i class="fas fa-info-circle"></i> El usuario heredará automáticamente todos los permisos configurados para el rol seleccionado.
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
                                        <i class="fas fa-save mr-2"></i> GUARDAR USUARIO
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