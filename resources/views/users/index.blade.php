@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <h1 class="text-center">Listado de Usuarios</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Gestión de Usuarios</h3>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Nuevo
            </a>
        </div>

        <div class="card-body">
<<<<<<< HEAD
            
=======

            {{-- Filtro por nombre de usuario --}}
           <!-- <div class="row mb-3">
                <div class="col-md-4">
                    <label for="filtro_nombre">Filtrar por Nombre de Usuario:</label>
                    <input type="text" id="filtro_nombre" class="form-control" placeholder="Escriba un nombre...">
                </div>
            </div>-->

>>>>>>> 770481b4c5da99829f5c325c95116d71cc39b8aa
            <div class="table-responsive">
                <table id="usuarios" class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>DNI</th>
                            <th>Cubículos Asignados</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $usuario)
                            <tr>
                                <td>{{ $usuario->id }}</td>
                                <td>{{ $usuario->name }}</td>
                                <td>{{ $usuario->email }}</td>
                                <td>{{ $usuario->DNI ?? 'N/A' }}</td>
                                <td>
                                    @if ($usuario->cubiculos->count() > 0)
                                        <ul class="mb-0">
                                            @foreach ($usuario->cubiculos as $cub)
                                                <li>{{ $cub->nombre }} ({{ ucfirst($cub->tipo_atencion) }})</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="badge bg-secondary">Sin cubículos</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('users.edit', $usuario) }}" class="btn btn-warning btn-sm me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('users.destroy', $usuario) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro de eliminar este usuario?')">
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
@stop

@section('css')
    {{-- Estilos DataTables y botones --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
@stop

@section('js')
    {{-- Librerías DataTables y exportación --}}
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(function () {
            var table = $('#usuarios').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                dom: '<"d-flex justify-content-between mb-3"Bf>rtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Exportar Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> Exportar PDF',
                        className: 'btn btn-danger btn-sm',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        title: 'Listado de Usuarios'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-secondary btn-sm'
                    }
                ]
            });

            // Filtro por nombre de usuario
            $('#filtro_nombre').on('keyup', function () {
                table.column(1).search(this.value).draw();
            });
        });
    </script>
@stop
