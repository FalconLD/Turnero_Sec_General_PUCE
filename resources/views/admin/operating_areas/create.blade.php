@extends('adminlte::page')

@section('title', 'Crear Área de Atención')

@section('content_header')
    <h1>Registrar Nueva Área de Atención</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('operating-areas.store') }}" method="POST">
                @csrf

                {{-- Selección de Facultad --}}
                <div class="form-group">
                    <label for="faculty_id">Facultad / Unidad Académica</label>
                    <select name="faculty_id" id="faculty_id" class="form-control @error('faculty_id') is-invalid @enderror" required>
                        <option value="" disabled selected>-- Seleccione una Facultad --</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                {{ $faculty->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('faculty_id')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                {{-- Nombre del Área --}}
                <div class="form-group">
                    <label for="name">Nombre del Área de Atención</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                           placeholder="Ej: Secretaría General, Bienestar, Cómputo..." value="{{ old('name') }}" required>
                    @error('name')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                {{-- Descripción --}}
                <div class="form-group">
                    <label for="description">Descripción (Opcional)</label>
                    <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                              placeholder="Breve descripción de las funciones del área...">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Área
                    </button>
                    <a href="{{ route('operating-areas.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    {{-- Aquí podrías añadir Select2 si quieres buscadores en los select más adelante --}}
@stop

@section('js')
    <script> console.log('Formulario de áreas cargado.'); </script>
@stop
