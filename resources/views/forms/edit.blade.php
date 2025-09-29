@extends('adminlte::page')

@section('title', 'Editar Formulario')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Formulario</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ups!</strong> Hubo algunos problemas.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('forms.update', $form->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $form->title) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Descripción</label>
            <textarea name="description" class="form-control">{{ old('description', $form->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="term" class="form-label">Término</label>
            <input type="text" name="term" class="form-control" value="{{ old('term', $form->term) }}" required>
        </div>

        <div class="mb-3">
            <label for="question" class="form-label">Pregunta</label>
            <textarea name="question" class="form-control" required>{{ old('question', $form->question) }}</textarea>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('forms.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
    </form>
</div>
@endsection
