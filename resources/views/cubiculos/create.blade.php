@extends('adminlte::page')

@section('title', 'Nuevo Cubículo')

@section('content_header')
    <h1>Crear Cubículo</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('cubiculos.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="nombre">Nombre del Cubículo</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="tipo_atencion">Tipo de Atención</label>
                    <select name="tipo_atencion" id="tipo_atencion" class="form-control" required>
                        <option value="">-- Seleccionar tipo --</option>
                        <option value="virtual">Virtual</option>
                        <option value="presencial">Presencial</option>
                    </select>
                </div>

                {{-- Campo dinámico --}}
                <div class="form-group" id="campo_extra" style="display: none;">
                    <label id="campo_extra_label"></label>
                    <input type="text" name="enlace_o_ubicacion" id="campo_extra_input" class="form-control">
                </div>

                <div class="form-group">
                    <label for="user_id">Usuario Asignado</label>
                    <select name="user_id" class="form-control" required>
                        <option value="">-- Seleccionar Usuario --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="{{ route('cubiculos.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('tipo_atencion').addEventListener('change', function () {
            const tipo = this.value;
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
        });
    </script>
    
@stop
