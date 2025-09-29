@extends('adminlte::page')

@section('title', 'pagina_asignación')

@section('content_header')
    <h1>Sección asignación</h1>
@stop

@section('content')

<div class="container mt-5">
    <div class="card card-custom p-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="input-group" style="max-width: 400px;">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-0" placeholder="Buscar">
                </div>
                <div>
                    <button class="btn btn-primary">Nuevo</button>
                    <button class="btn btn-refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">CUBÍCULO</th>
                            <th scope="col">FORMULARIO</th>
                            <th scope="col">ACTUALIZADO</th>
                            <th scope="col" colspan="2">ACCIONES</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        {{-- Aquí comenzaría tu bucle en Laravel: @foreach($asignaciones as $asignacion) --}}
                        
                        <tr>
                            <td><span class="badge-custom">Cubículo 2 Demo Tania</span></td>
                            <td><span class="badge-custom">Matrículas carrera1</span></td>
                            <td>hace 6 meses</td>
                            <td>
                                <a href="#" class="text-danger">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td><span class="badge-custom">Cubículo 1 Demo SAdmin</span></td>
                            <td><span class="badge-custom">Matrículas carrera1</span></td>
                            <td>hace 6 meses</td>
                            <td>
                                <a href="#" class="text-danger">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td><span class="badge-custom">Cubículos6</span></td>
                            <td><span class="badge-custom">Matrículas carrera6</span></td>
                            <td>hace un mes</td>
                            <td>
                                <a href="#" class="text-danger">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td><span class="badge-custom">Cubículos7</span></td>
                            <td><span class="badge-custom">Matrículas carrera7</span></td>
                            <td>hace un mes</td>
                            <td>
                                <a href="#" class="text-danger">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>

                        {{-- Aquí terminaría tu bucle: @endforeach --}}
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>


@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop