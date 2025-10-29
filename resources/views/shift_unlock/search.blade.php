@extends('adminlte::page')

@section('title', 'Buscar Cédula')

@section('content_header')
    <h1 class="text-center">Buscar Cédula</h1>
@stop

@section('content')
<div class="container mt-4">
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('shift_unlock.search.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="cedula">Cédula:</label>
                    <input type="text" name="cedula" id="cedula" class="form-control" placeholder="Ingrese la cédula" required>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Buscar</button>
            </form>
        </div>
    </div>
</div>
@stop
