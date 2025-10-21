@extends('adminlte::page')

@section('title', 'Crear Usuario')

@section('content_header')
    <h1 class="text-center">Crear Usuario</h1>
@stop

@section('content')
    <div class="container-fluid"> {{-- Agregamos container-fluid para mayor amplitud --}}
        <div class="row justify-content-center"> {{-- Usamos row y justify-content-center para centrar --}}
            <div class="col-md-8"> {{-- Definimos un ancho para el card, por ejemplo 8 columnas de 12 --}}
                <div class="card">
                    <div class="card-body">
                        {{-- Todo el contenido de tu formulario va aquí --}}
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf

                            {{-- Fila para Nombre y Correo Electrónico --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Nombre <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            </div>
                                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Ej: Juan Pérez">
                                        </div>
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Correo Electrónico <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            </div>
                                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="Ej: juan.perez@puce.edu.ec">
                                        </div>
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Fila para DNI y Contraseña --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="DNI">DNI</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                            </div>
                                            <input type="text" name="DNI" class="form-control @error('DNI') is-invalid @enderror" value="{{ old('DNI') }}" placeholder="Ej: 1712345678">
                                        </div>
                                         @error('DNI')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Contraseña <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            </div>
                                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Mínimo 8 caracteres">
                                        </div>
                                        @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Botones de Acción --}}
                            <div class="text-right">
                                <a href="{{ route('users.index') }}" class="btn btn-danger">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div> {{-- Cierra col-md-8 --}}
        </div> {{-- Cierra row --}}
    </div> {{-- Cierra container-fluid --}}
@stop