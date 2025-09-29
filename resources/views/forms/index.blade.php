
@extends('adminlte::page')

@section('title', 'Lista de Formularios')


@section('content')
<div class="container">
    <h2 class="mb-4">Lista de Formularios</h2>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif

    <div class="mb-3">
        <a href="{{ route('forms.create') }}" class="btn btn-primary">➕ Nuevo Formulario</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Término</th>
                    <th>Pregunta</th>
                    <th width="180px">Acciones</th>
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
                            <a href="{{ route('forms.edit', $form->id) }}" class="btn btn-warning btn-sm">✏ Editar</a>

                            <form action="{{ route('forms.destroy', $form->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que quieres eliminar este formulario?')">🗑 Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
