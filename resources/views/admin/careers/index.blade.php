    @extends('adminlte::page')

@section('title', 'Carreras')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Gestión de Carreras</h1>
        @can('carreras.crear')
            <a href="{{ route('careers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Carrera
            </a>
        @endcan
    </div>
@stop

@section('content')
    @if (session('info'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('info') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-hover table-sm">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>Código</th>
                        <th>Nombre de la Carrera</th>
                        <th>Área de Atención</th>
                        <th width="120px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($careers as $career)
                        <tr>
                            <td>
                                <span class="badge badge-secondary">{{ $career->career_code ?? 'S/N' }}</span>
                            </td>
                            <td>{{ $career->name }}</td>
                            <td>{{ $career->operatingArea->name ?? 'Sin área' }}</td>
                            <td>
                                @can('carreras.editar')
                                    <a href="{{ route('careers.edit', $career) }}" class="btn btn-xs btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan
                                @can('carreras.eliminar')
                                    <form action="{{ route('careers.destroy', $career) }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('¿Eliminar carrera?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
