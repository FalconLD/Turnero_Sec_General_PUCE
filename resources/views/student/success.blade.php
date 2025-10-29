@extends('adminlte::page')

@section('title', 'Registro Exitoso')

@section('content')
<div class="card w-75 mx-auto mt-5 text-center">
  <div class="card-body">
    <h4>Registro completado correctamente</h4>
    <a href="{{ route('student.personal') }}" class="btn btn-primary mt-3">Volver al inicio</a>

    {{--<a href="{{ route('student.terms') }}" class="btn btn-primary mt-3">Volver al inicio</a>--}}
   
  </div>
</div>
@stop
