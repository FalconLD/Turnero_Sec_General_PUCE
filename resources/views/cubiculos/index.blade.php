@extends('adminlte::page')

@section('title', 'Cubículos')

@section('content_header')
    <h1 class="text-center">Listado de Cubículos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('cubiculos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo
            </a>
        </div>
        <div class="card-body">
            <table id="cubiculos" class="table caption-top">
                <thead>
                    <tr>
                        <th scope="col" class="table-primary" >ID</th>
                        <th scope="col" class="table-primary">Nombre</th>
                        <th scope="col" class="table-primary">Tipo de Atención</th>
                        <th scope="col" class="table-primary">Usuario Asignado</th>
                        <th scope="col" class="table-primary">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cubiculos as $cubiculo)
                        <tr>
                            <td>{{ $cubiculo->id }}</td>
                            <td>{{ $cubiculo->nombre }}</td>
                            <td>
                                <span class="badge {{ $cubiculo->tipo_atencion == 'virtual' ? 'bg-info' : 'bg-success' }}">
                                    {{ ucfirst($cubiculo->tipo_atencion) }}
                                </span>
                            </td>
                            <td>{{ $cubiculo->users->name ?? 'No asignado' }}</td>
                            <td>
                                <a href="{{ route('cubiculos.edit', $cubiculo) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('cubiculos.destroy', $cubiculo) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro de eliminar este cubículo?')">
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
            $('#cubiculos').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                }
            });
        });
    </script>
@stop
