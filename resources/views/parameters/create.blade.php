@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Crear Parámetro</h2>

    <form action="{{ route('parameters.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="clave" class="form-label">Clave</label>
            <input type="text" class="form-control" name="clave" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" required></textarea>
        </div>

        <div class="mb-3">
            <label for="parametro" class="form-label">Parámetro</label>
            <input type="text" class="form-control" name="parametro" required>
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
