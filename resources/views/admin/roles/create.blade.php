@extends('adminlte::page')

@section('title', 'Crear Rol')

@section('content_header')
    <div class="position-relative p-3">
        {{-- Botón Volver a la izquierda --}}
        <div class="position-absolute" style="left: 0; top: 50%; transform: translateY(-50%);">
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-sm px-4 rounded-pill">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
        {{-- Título Centrado --}}
        <h1 class="text-center font-weight-bold text-dark m-0">
            <i class="fas fa-plus-circle mr-2 text-primary"></i>Crear Nuevo Rol
        </h1>
    </div>
@stop

@section('content')

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf

        {{-- NOMBRE DEL ROL --}}
        <div class="card shadow-none border mb-4" style="background: #f8f9fa;">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <label class="col-auto col-form-label font-weight-bold text-muted text-uppercase small ls-1">Nombre del Rol:</label>
                    <div class="col">
                        <input type="text" name="name" class="form-control bg-white border-0 shadow-sm font-weight-bold text-dark"
                               placeholder="Ej: Auditor, Supervisor..." value="{{ old('name') }}" required style="font-size: 1.1rem;">
                        @error('name')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- GRILLA DE PERMISOS (Mismo diseño UX que en Editar) --}}
        <div class="row">
            @foreach($permissionGroups as $groupName => $permissions)
                @php
                    // Lógica de colores (Misma que en edit)
                    $bgHeader = 'bg-primary';
                    $icon = 'fas fa-cogs';
                    switch(strtolower($groupName)) {
                        case 'atencion': $bgHeader = 'bg-info'; $icon = 'far fa-calendar-alt'; break;
                        case 'usuarios': $bgHeader = 'bg-warning'; $icon = 'fas fa-users'; break;
                        case 'roles':    $bgHeader = 'bg-danger'; $icon = 'fas fa-user-shield'; break;
                        case 'horarios': $bgHeader = 'bg-teal'; $icon = 'far fa-clock'; break;
                        case 'cubiculos': $bgHeader = 'bg-orange'; $icon = 'fas fa-door-open'; break;
                        case 'reportes': $bgHeader = 'bg-purple'; $icon = 'fas fa-chart-pie'; break;
                        case 'desbloqueo': $bgHeader = 'bg-indigo'; $icon = 'fas fa-unlock-alt'; break;
                        case 'parametros': $bgHeader = 'bg-dark'; $icon = 'fas fa-sliders-h'; break;
                    }
                    $textClass = ($bgHeader == 'bg-warning' || $bgHeader == 'bg-orange') ? 'text-dark' : 'text-white';
                @endphp

                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card h-100 shadow-sm border-0 overflow-hidden hover-shadow-lg">
                        <div class="card-header {{ $bgHeader }} {{ $textClass }} py-2 d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0 font-weight-bold text-uppercase" style="font-size: 0.9rem; letter-spacing: 0.5px;">
                                <i class="{{ $icon }} mr-2 opacity-75"></i> {{ ucfirst($groupName) }}
                            </h6>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input select-all-group"
                                       id="all_{{ $groupName }}" data-group="{{ $groupName }}">
                                <label class="custom-control-label font-weight-normal" for="all_{{ $groupName }}" style="font-size: 0.8rem">Todo</label>
                            </div>
                        </div>

                        <div class="card-body p-0 group-container-{{ $groupName }}">
                            <table class="table table-sm table-hover mb-0">
                                <tbody>
                                    @foreach($permissions as $permission)
                                        @php
                                            $cleanName = ucfirst(explode('.', $permission->name)[1] ?? $permission->name);
                                            $cleanName = str_replace('_', ' ', $cleanName);
                                        @endphp
                                        <tr>
                                            <td class="pl-3 align-middle border-top-0">
                                                <label class="m-0 font-weight-normal text-secondary cursor-pointer"
                                                       for="perm_{{ $permission->id }}" style="cursor: pointer; font-size: 0.9rem;">
                                                    {{ $cleanName }}
                                                </label>
                                            </td>
                                            <td class="text-right pr-3 align-middle border-top-0" style="width: 60px;">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="perm_{{ $permission->id }}"
                                                        name="permissions[]"
                                                        value="{{ $permission->name }}">
                                                    <label class="custom-control-label" for="perm_{{ $permission->id }}"></label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- BARRA INFERIOR (Sticky Footer) --}}
        <div class="card shadow mt-4 border-top-primary">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col-md-6 text-left">
                        <p class="text-muted small mb-0">
                            <i class="fas fa-info-circle mr-1"></i>
                            <span class="d-none d-md-inline">Defina los permisos iniciales para este nuevo perfil.</span>
                        </p>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary mr-2 px-4">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-success px-5 font-weight-bold shadow-sm">
                            <i class="fas fa-check mr-2"></i> CREAR ROL
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-4"></div>

    </form>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('.select-all-group').change(function() {
            var group = $(this).data('group');
            var isChecked = $(this).is(':checked');
            $('.group-container-' + group + ' input[type="checkbox"]').prop('checked', isChecked);
        });
    });
</script>
@stop

@section('css')
<style>
    .custom-control-input:checked ~ .custom-control-label::before {
        background-color: #28a745;
        border-color: #28a745;
    }
    .hover-shadow-lg { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-shadow-lg:hover { transform: translateY(-2px); box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important; }
    .cursor-pointer { cursor: pointer; }
</style>
@stop
