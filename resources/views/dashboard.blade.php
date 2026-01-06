@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1 class="text-center">Bienvenido</h1>

@stop

@section('content')
   <div class="card">
        <br>
        <h2 class="card-title mb-4">&ensp;&ensp; Sistema de Turnero para Secretaria General</h2>
            <div class="text-center">
                <br>
                <img src="{{ asset('vendor/adminlte/dist/img/puce.png') }}" alt="Logo Secretaria General" class="img-fluid" style="max-width">
            </div>
                <br>
                <br>
        </div>

    </div>

@stop


@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> </script>
@stop
