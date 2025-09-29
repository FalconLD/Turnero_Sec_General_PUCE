@extends('adminlte::page')

@section('title', 'pagina_usuarios')

@section('content_header')
    <h1>Sección usuarios</h1>
@stop

@section('content')
    <p>Esta es la pagina de los usuarios.</p>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop