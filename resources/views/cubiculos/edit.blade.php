@extends('adminlte::page')

@section('title', 'Editar Cubículo')

@section('content_header')
    <h1>Editar Cubículo</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('cubiculos.update', $cubiculo) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="nombre">Nombre del Cubículo</label>
                    <input type="text" name="nombre" class="form-control" value="{{ $cubiculo->nombre }}" required>
                </div>

                <div class="form-group">
                    <label for="tipo_atencion">Tipo de Atención</label>
                    <select name="tipo_atencion" id="tipo_atencion" class="form-control" required>
                        <option value="virtual" {{ $cubiculo->tipo_atencion == 'virtual' ? 'selected' : '' }}>Virtual</option>
                        <option value="presencial" {{ $cubiculo->tipo_atencion == 'presencial' ? 'selected' : '' }}>Presencial</option>
                    </select>
                </div>

                {{-- Campo dinámico según tipo_atencion --}}
                <div class="form-group" id="campo_extra">
                    <label id="campo_extra_label"></label>
                    <input 
                        type="text" 
                        name="enlace_o_ubicacion" 
                        id="campo_extra_input" 
                        class="form-control" 
                        value="{{ $cubiculo->enlace_o_ubicacion }}"
                    >
                </div>

                <div class="form-group">
                    <label for="user_id">Usuario Asignado</label>
                    <select name="user_id" class="form-control" required>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ $cubiculo->user_id == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Actualizar</button>
                <a href="{{ route('cubiculos.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>

    <script>
        function actualizarCampoExtra() {
            const tipo = document.getElementById('tipo_atencion').value;
            const campoExtra = document.getElementById('campo_extra');
            const label = document.getElementById('campo_extra_label');
            const input = document.getElementById('campo_extra_input');

            if (tipo === 'virtual') {
                campoExtra.style.display = 'block';
                label.textContent = 'Enlace de conexión (Zoom, Meet, etc.)';
                input.placeholder = 'https://...';
            } else if (tipo === 'presencial') {
                campoExtra.style.display = 'block';
                label.textContent = 'Ubicación del cubículo';
                input.placeholder = 'Ejemplo: Edificio Central, piso 2, aula 203';
            } else {
                campoExtra.style.display = 'none';
                input.value = '';
            }
        }

        // Ejecutar al cargar la página y al cambiar el tipo de atención
        document.addEventListener('DOMContentLoaded', actualizarCampoExtra);
        document.getElementById('tipo_atencion').addEventListener('change', actualizarCampoExtra);
    </script>
@stop
