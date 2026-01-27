@extends('adminlte::page')

@section('title', 'Formularios')

@section('content_header')
    <h1 class="text-center">Lista de Formularios</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-end align-items-center">
            <a href="{{ route('forms.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus"></i> Nuevo Formulario
            </a>
        </div>

        <div class="card-body">
            {{-- Mensaje de éxito optimizado --}}
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {{ $message }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                {{-- Se añade datatable-export para el JS y data-page-title para los reportes --}}
                <table id="tabla-formularios" class="table datatable-export" data-page-title="Listado de Formularios">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Descripción</th>
                            <th>Término</th>
                            <th>Pregunta</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($forms as $form)
                            <tr>
                                <td>{{ $form->id }}</td>
                                <td class="font-weight-bold">{{ $form->title }}</td>
                                <td>{{ $form->description }}</td>
                                <td>{{ $form->term }}</td>
                                <td>{{ $form->question }}</td>
                                <td class="text-nowrap">
                                    {{-- Contenedor estandarizado para botones alineados --}}
                                    <div class="acciones-column">
                                        <a href="{{ route('forms.edit', $form->id) }}"
                                           class="btn btn-xs btn-default text-primary shadow-sm"
                                           title="Editar">
                                            <i class="fas fa-lg fa-edit"></i>
                                        </a>

                                        <form action="{{ route('forms.destroy', $form->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-xs btn-default text-danger shadow-sm"
                                                    onclick="return confirm('¿Seguro que quieres eliminar este formulario?')"
                                                    title="Eliminar">
                                                <i class="fas fa-lg fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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
    <link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}">
@stop

@section('js')
    <script src="{{ asset('js/admin-init.js') }}"></script>
@stop
