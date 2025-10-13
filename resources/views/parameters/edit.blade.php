@extends('adminlte::page')

@section('content')
<div class="container">
    <h2>Editar Parámetro</h2>

    <form action="{{ route('parameters.update', $parameter) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Clave</label>
            <input type="text" class="form-control" name="clave" value="{{ $parameter->clave }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" required>{{ $parameter->descripcion }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Parámetro</label>
            <input type="text" class="form-control" name="parametro" value="{{ $parameter->parametro }}" required>
        </div>

        <button type="submit" class="btn btn-success">Actualizar</button>
    </form>
</div>
@endsection
