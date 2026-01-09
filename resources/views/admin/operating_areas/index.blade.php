@extends('adminlte::page')

@section('title', 'Áreas Operativas')

@section('content_header')
    <h1 class="text-center">Gestión de Áreas Operativas</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="card">
                    <div class="card-header d-flex justify-content-end align-items-center">
                        <a href="{{ route('operating-areas.create') }}" class="btn btn-primary rounded-pill px-5">
                            <i class="fas fa-plus"></i> Nuevo
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="areas_table" class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre del Área</th>
                                        <th>Facultad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($areas as $area)
                                        <tr>
                                            <td>{{ $area->id }}</td>
                                            <td>{{ $area->name }}</td>
                                            <td>
                                                <span class="badge badge-info px-3 py-2 rounded-pill">
                                                    {{ $area->faculty->facultad }}
                                                </span>
                                            </td>
                                            <td class="text-nowrap">
                                                <a href="{{ route('operating-areas.edit', $area) }}" class="btn btn-link text-primary btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('operating-areas.destroy', $area) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-link text-danger btn-sm" onclick="return confirm('¿Eliminar esta área?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Estilos idénticos a tu módulo de usuarios */
        .card { border-radius: 1rem !important; border: none; box-shadow: 0 8px 24px rgba(0,0,0,0.07); }
        .card-header { border-top-left-radius: 1rem !important; border-top-right-radius: 1rem !important; background-color: #fff; border-bottom: 1px solid #f0f0f0; }
        #areas_table thead { background-color: #f8f9fa; }
        #areas_table thead th { color: #495057; font-weight: 600; border: none; padding-top: 1rem; padding-bottom: 1rem; }
        .dataTables_filter input[type="search"] { width: 350px !important; border-radius: 20px !important; }
    </style>
@stop

@section('js')
    <script>
        $(function () {
            $('#areas_table').DataTable({
                responsive: true,
                autoWidth: false,
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' }
            });
        });
    </script>
@stop
