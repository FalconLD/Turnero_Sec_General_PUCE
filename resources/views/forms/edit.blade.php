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
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
    </form>
</div>
@endsection
