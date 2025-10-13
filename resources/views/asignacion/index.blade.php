@extends('adminlte::page')

@section('title', 'Asignación')

@section('content_header')
    <h1 class="text-center">Sección Asignación</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

                <a href="{{ route('asignacion.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Agregar               
                </a>
        </div>
        <div class="card-body">
                <table id="asignacion" class="table caption-top">
                    <thead>
                        <tr>
                            <th scope="col" class="table-primary">ID</th>
                            <th scope="col" class="table-primary">Cubículo</th>
                            <th scope="col" class="table-primary">Formulario</th>
                            <th scope="col" class="table-primary">Fecha de Actualización</th>
                            <th scope="col" class="table-primary">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asignaciones as $asignacion)
                            <tr>
                                <td>{{ $asignacion->id }}</td>
                                <td>{{ $asignacion->cubiculo->nombre ?? 'N/A' }}</td>
                                <td>{{ $asignacion->form->title ?? 'N/A' }}</td>
                                <td>
                                    @if($asignacion->fecha_actualizacion)
                                        {{ $asignacion->fecha_actualizacion->format('d/m/Y') }}
                                     @else
                                         <span class="text-muted">No asignada</span>
                                     @endif
                                </td>
                                <td>
                                    <a href="{{ route('asignacion.edit', $asignacion->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i></a>
                                    <form action="{{ route('asignacion.destroy', $asignacion->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar esta asignación?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay assignments registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
    </div>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script>
        $(function () {
            $('#asignacion').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                }
            });
        });
    </script>
@stop