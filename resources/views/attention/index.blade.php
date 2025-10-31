@extends('adminlte::page')

@section('title', 'Gestión de Atención')

@section('content_header')
    <h1 class="m-0 text-dark text-center">Gestión de Atención de Turnos</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <body>
                        <div id='calendar'></div>

                    </body>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const calendarEl = document.getElementById('calendar')
                const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth'
                })
                calendar.render()
                })
    </script>

    
@stop

@section('js')

@stop
