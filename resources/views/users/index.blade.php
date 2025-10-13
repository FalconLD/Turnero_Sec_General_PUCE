@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <h1 class="text-center">Listado de Usuarios</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Nuevo
            </a>
        </div>
        <div class="card-body">
            <table id="usuarios" class="table caption-top">
                <thead>
                    <tr>
                        <th scope="col" class="table-primary">ID</th>
                        <th scope="col" class="table-primary">Nombre</th>
                        <th scope="col" class="table-primary">Email</th>
                        <th scope="col" class="table-primary">DNI</th>
                        <th scope="col" class="table-primary">Cubículos Asignados</th>
                        <th scope="col" class="table-primary">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $users)
                        <tr>
                            <td>{{ $users->id }}</td>
                            <td>{{ $users->name }}</td>
                            <td>{{ $users->email }}</td>
                            <td>{{ $users->DNI ?? 'N/A' }}</td>
                            <td>
                                @if($users->cubiculos->count() > 0)
                                    <ul class="mb-0">
                                        @foreach ($users->cubiculos as $cubiculo)
                                            <li>{{ $cubiculo->nombre }} ({{ ucfirst($cubiculo->tipo_atencion) }})</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="badge bg-secondary">Sin cubículos</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('users.edit', $users) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('users.destroy', $users) }}" method="POST" style="display:inline;">
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
@stop

@section('js')
    <script>
        $(function () {
            $('#usuarios').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                }
            });
        });
    </script>
@stop
