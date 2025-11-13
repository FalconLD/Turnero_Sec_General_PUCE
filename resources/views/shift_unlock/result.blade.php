@extends('adminlte::page')

@section('title', 'Resultado')

@section('content_header')
    <h1 class="text-center">Resultado de la búsqueda</h1>
@stop

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Cédula</th>
                    <td>{{ $shift->person_shift }}</td>
                </tr>
                <tr>
                    <th>Nombre</th>
                    <td>{{ $student ? $student->names : 'No disponible' }}</td>
                </tr>
                <tr>
                    <th>Estado</th>
                    <td>
                        @if($student)
                            {{ $student->tomado == 1 ? 'Desbloqueado' : 'Bloqueado' }}
                        @else
                            No disponible
                        @endif
                    </td>
                </tr>
            </table>

            @if($student && $student->tomado == 0)
                <div class="text-center mt-3">
                    <a href="{{ route('shift_unlock.unlock', $shift->person_shift) }}" class="btn btn-success">
                        Desbloquear
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@stop
