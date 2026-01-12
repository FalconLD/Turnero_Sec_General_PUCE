@extends('adminlte::page')

@section('title', 'Editar Carrera')

@section('content_header')
    <h1>Editar Carrera: {{ $career->name }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('careers.update', $career) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Selección de Área Operativa --}}
                <div class="form-group">
                    <label for="operating_area_id">Área Operativa / de Atención</label>
                    <select name="operating_area_id" id="operating_area_id" class="form-control @error('operating_area_id') is-invalid @enderror" required>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}"
                                {{ (old('operating_area_id', $career->operating_area_id) == $area->id) ? 'selected' : '' }}>
                                {{ $area->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('operating_area_id')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="row">
                    {{-- Código de Carrera --}}
                    <div class="col-md-4 form-group">
                        <label for="career_code">Código de Carrera</label>
                        <input type="text" name="career_code" id="career_code"
                               class="form-control @error('career_code') is-invalid @enderror"
                               value="{{ old('career_code', $career->career_code) }}"
                               placeholder="Ej: Q343 o 'No existe programa'">
                        @error('career_code')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    {{-- Nombre de la Carrera --}}
                    <div class="col-md-8 form-group">
                        <label for="name">Nombre de la Carrera</label>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $career->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sync-alt"></i> Actualizar Carrera
                    </button>
                    <a href="{{ route('careers.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('footer')
    <div class="text-right">
        <small>
            ID Interno: {{ $career->id }} |
            Creado: {{ $career->created_at ? $career->created_at->format('d/m/Y') : 'Fecha no disponible' }}
        </small>
    </div>
@stop
