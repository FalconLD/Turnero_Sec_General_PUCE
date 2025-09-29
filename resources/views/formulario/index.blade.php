@extends('adminlte::page')

@section('title', 'pagina_formulario')

@section('content_header')
    <h1>Sección formulario</h1>
@stop

@section('content')
    <p>Esta es la pagina de los formularios</p>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop