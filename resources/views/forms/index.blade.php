
@extends('adminlte::page')

@section('title', 'Formularios')

@section('content_header')
    <h1 class="text-center">Sección Formularios</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                {{ $message }}
            </div>
        @endif
    
        <a href="{{ route('forms.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo</a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table  class="table caption-top">
                <thead>
                    <tr>
                        <th scope="col" class="table-primary">ID</th>
                        <th scope="col" class="table-primary">Título</th>
                        <th scope="col" class="table-primary">Descripción</th>
                        <th scope="col" class="table-primary">Término</th>
                        <th scope="col" class="table-primary">Pregunta</th>
                        <th scope="col" class="table-primary" width="180px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($forms as $form)
                        <tr>
                            <td>{{ $form->id }}</td>
                            <td>{{ $form->title }}</td>
                            <td>{{ $form->description }}</td>
                            <td>{{ $form->term }}</td>
                            <td>{{ $form->question }}</td>
                            <td>
                                <a href="{{ route('forms.edit', $form->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i></a>
                                </a>

                                <form action="{{ route('forms.destroy', $form->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que quieres eliminar este formulario?')">
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
@endsection
