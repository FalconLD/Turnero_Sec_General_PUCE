@extends('adminlte::page')

@section('title', 'Nuevo Formulario')


@section('content')
<div class="container">
    <h2 class="mb-4">Crear Formulario</h2>

    <!-- Mostrar errores de validación -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ups!</strong> Hubo algunos problemas con tu entrada.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario -->
    <form action="{{ route('forms.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" name="title" class="form-control" placeholder="Escribe el título" value="{{ old('title') }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Descripción</label>
            <textarea name="description" class="form-control" placeholder="Escribe una descripción">{{ old('description') }}</textarea>
        </div>

        <div class="custom-control custom-switch mb-3">
            <input type="checkbox" class="custom-control-input" id="term" name="term" value="1" {{ old('term') ? 'checked' : '' }}>
            <label class="custom-control-label" for="term">Término</label>
        </div>

        <div class="custom-control custom-switch mb-3">
            <input type="checkbox" class="custom-control-input" id="question" name="question" value="1" {{ old('question') ? 'checked' : '' }}>
            <label class="custom-control-label" for="question">Pregunta</label>
        </div>


        <div class="d-flex justify-content-between">
            <a href="{{ route('forms.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</div>
@endsection
