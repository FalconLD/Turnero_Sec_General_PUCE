@extends('adminlte::page')

@section('title', 'Crear Facultad')

@section('content_header')
    <h1>Registrar Nueva Facultad</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('faculties.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="facultad">Nombre de la Facultad</label>
                        <input type="text" name="facultad" id="facultad" class="form-control @error('facultad') is-invalid @enderror" value="{{ old('facultad') }}" required>
                        @error('facultad') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3 form-group">
                        <label for="nivel">Nivel</label>
                        <select name="nivel" id="nivel" class="form-control" required>
                            <option value="GRADO">GRADO</option>
                            <option value="POSGRADO">POSGRADO</option>
                            <option value="TECNOLOGÍA">TECNOLOGÍA</option>
                        </select>
                    </div>

                    <div class="col-md-12 form-group">
                        <label for="programa_desc">Descripción del Programa</label>
                        <input type="text" name="programa_desc" id="programa_desc" class="form-control @error('programa_desc') is-invalid @enderror" value="{{ old('programa_desc') }}" required>
                        @error('programa_desc') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
                    <a href="{{ route('faculties.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@stop
