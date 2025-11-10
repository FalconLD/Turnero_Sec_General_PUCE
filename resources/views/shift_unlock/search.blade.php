@extends('adminlte::page')

@section('title', 'Buscar Cédula')

@section('content_header')
    <h1 class="text-center">Desbloquear Usuario</h1>
@stop

@section('content')
<div class="row">
    {{-- 
      Hacemos la tarjeta más angosta (col-md-6) 
      y la centramos (mx-auto) 
    --}}
    <div class="col-md-6 mx-auto">

        {{-- Mostramos las alertas aquí para que estén centradas --}}
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- 
          Añadimos 'card-primary' y 'card-outline' 
          para darle el color principal de AdminLTE 
        --}}
        <div class="card card-primary card-outline shadow">
            <div class="card-header">
                <h3 class="card-title text-center d-block"></h3>
            </div>
            <div class="card-body">
                <p class="text-center text-muted">Ingrese la cédula o ID del usuario para buscar y desbloquear.</p>

                <form action="{{ route('shift_unlock.search.post') }}" method="POST">
                    @csrf
                    
                    {{-- 
                      Usamos 'input-group' para añadir un icono 
                      al campo de texto 
                    --}}
                    <div class="input-group mb-3">
                        <input type="text" name="cedula" id="cedula" class="form-control" placeholder="Cédula" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-id-card"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            {{-- 
                              Usamos 'btn-block' para que ocupe todo el ancho 
                              y añadimos un icono 
                            --}}
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search mr-2"></i>
                                Buscar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop