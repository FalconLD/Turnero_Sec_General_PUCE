@extends('adminlte::page')

@section('title', 'Gestión de Roles')

@section('content')

    {{-- 1. TÍTULO CENTRADO CON ESTILO --}}
    <div class="row mb-4 mt-4">
        <div class="col-12 text-center">
            <h1 class="font-weight-bold text-dark">
                <i class="fas fa-users-cog text-primary mr-2"></i>Roles del Sistema
            </h1>
            <p class="text-muted">Administra los niveles de acceso y permisos de los usuarios</p>
            
            <div class="mt-3">
                <a href="{{ route('roles.create') }}" class="btn btn-primary btn-lg shadow-sm rounded-pill px-4">
                    <i class="fas fa-plus mr-1"></i> Crear Nuevo Rol
                </a>
            </div>
        </div>
    </div>

    {{-- 2. GRILLA DE TARJETAS MEJORADA --}}
    <div class="row justify-content-center"> @foreach ($roles as $role)
            @php
                // Lógica de Colores e Iconos
                $color = 'secondary';
                $icon = 'fas fa-user-tag';
                $btnClass = 'btn-outline-secondary';

                // Personalización según el nombre del rol
                switch(strtolower($role->name)) {
                    case 'super admin':
                        $color = 'purple'; // AdminLTE soporta colores como purple, indigo, etc.
                        $icon = 'fas fa-crown';
                        $btnClass = 'btn-outline-purple'; // Clase personalizada o usa btn-outline-dark
                        break;
                    case 'recepcion':
                        $color = 'orange';
                        $icon = 'fas fa-concierge-bell';
                        $btnClass = 'btn-outline-warning';
                        break;
                    case 'psicologo':
                        $color = 'teal';
                        $icon = 'fas fa-user-md';
                        $btnClass = 'btn-outline-info';
                        break;
                }
                
                // Truco para colores hexadecimales si AdminLTE no reconoce 'purple' en bordes
                $borderClass = ($role->name == 'Super Admin') ? 'border-danger' : 'border-'.$color; 
            @endphp

            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                {{-- CARD: Usamos h-100 para que todas tengan la misma altura --}}
                <div class="card card-outline card-{{ $color == 'purple' ? 'primary' : $color }} h-100 shadow-sm hover-effect">
                    <div class="card-body box-profile d-flex flex-column">
                        
                        {{-- Icono Centrado y Grande --}}
                        <div class="text-center mb-3">
                            <div class="d-inline-flex justify-content-center align-items-center rounded-circle bg-light" 
                                 style="width: 80px; height: 80px; border: 3px solid #f4f6f9;">
                                <i class="{{ $icon }} fa-3x text-{{ $color == 'purple' ? 'indigo' : $color }}"></i>
                            </div>
                        </div>

                        {{-- Nombre del Rol --}}
                        <h3 class="profile-username text-center font-weight-bold text-dark mb-1">
                            {{ $role->name }}
                        </h3>

                        {{-- Cantidad de Usuarios --}}
                        <p class="text-muted text-center mb-4">
                            @if(($role->users_count ?? 0) > 0)
                                <span class="badge badge-light border">
                                    {{ $role->users_count }} Usuarios activos
                                </span>
                            @else
                                <span class="badge badge-light border">Sin usuarios</span>
                            @endif
                        </p>

                        {{-- Botones de Acción --}}
                        <div class="mt-auto row">
                            {{-- Botón Editar (Permisos) --}}
                            <div class="col-{{ $role->name !== 'Super Admin' ? '6' : '12' }}">
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary btn-block btn-sm shadow-sm">
                                    <i class="fas fa-edit"></i> Permisos
                                </a>
                            </div>
                            
                            {{-- Botón Borrar (Solo si NO es Super Admin) --}}
                            @if($role->name !== 'Super Admin')
                                <div class="col-6"> {{-- FALTABA ESTA COLUMNA ENVOLVENTE --}}
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        
                                        {{-- Corrección: Quitamos 'mt-2' y usamos 'btn-danger' sólido --}}
                                        <button type="submit" class="btn btn-danger btn-block btn-sm shadow-sm btn-delete">
                                            <i class="fas fa-trash mr-1"></i> Borrar
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Estilos Extra para efecto Hover --}}
    <style>
        .hover-effect { transition: transform 0.3s ease; }
        .hover-effect:hover { transform: translateY(-5px); }
    </style>
@stop


@section('js')
    {{-- Importamos SweetAlert2 (si no lo tienes ya en tu layout base) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Mensajes de éxito/error que vienen del controlador
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
            });
        @endif

        // Lógica para la confirmación de borrado
        $('.delete-form').submit(function(e) {
            e.preventDefault(); // Detiene el envío automático del formulario

            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esta acción! Se eliminará el rol permanentemente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminarlo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit(); // Si el usuario dice sí, envía el formulario
                }
            })
        });
    </script>
@stop