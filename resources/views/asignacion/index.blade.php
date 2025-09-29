@extends('adminlte::page')

@section('title', 'pagina_asignación')

@section('content_header')
    <h1>Sección asignación</h1>
@stop

@section('content')
    <p>Esta es la pagina de las asiganciones.</p>
    <button type="button" class="btn btn-primary">Primary</button>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop