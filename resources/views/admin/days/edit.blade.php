@extends('adminlte::page')

@section('title', 'Editar Días de Atención')

@section('content_header')
<h1 class="text-center mb-4">
    Editar los días de atención del horario
</h1>
@stop

@section('content')
    {{-- 1. Contenedor centrado y más ancho (col-md-10) --}}
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">

                {{-- CAMBIO 1: Botón "Volver" movido a la parte superior izquierda --}}
                <div class="mb-3">
                    <a href="{{ route('schedules.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver a Horarios
                    </a>
                </div>
                
                {{-- Alerta de errores --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>¡Error!</strong> No se pudieron guardar los cambios:
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                {{-- Fin de Alerta --}}

                {{-- 2. Tarjeta con estilos redondeados (ver CSS) --}}
                <div class="card shadow-lg border-0 p-3 p-md-4">
                    <div class="card-body">
                        <form action="{{ route('days.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="schedule" value="{{ $schedule->id_hor }}">

                            <div class="mb-4">
                                {{-- 3. Calendario (sin .form-control para que los estilos CSS tomen control) --}}
                                <div id="datepicker" class="m-auto"></div>
                                
                                {{-- Contenedor para los inputs hidden que se enviarán --}}
                                <div id="hidden-dates">
                                    {{-- Lógica de 'edit' para cargar días existentes o de 'old' --}}
                                    @if(old('dates', $existingDays))
                                        @foreach(old('dates', $existingDays) as $d)
                                            <input type="hidden" name="dates[]" value="{{ $d }}">
                                        @endforeach
                                    @endif
                                </div>
                                @error('dates')
                                    <small class="text-danger text-center d-block mt-2">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Botón "Actualizar" centrado y con espacio --}}
                            <div class="text-center mt-5">
                                <button type="submit" class="btn btn-lg btn-primary rounded-pill px-5">
                                    <i class="fas fa-save"></i> Actualizar Días
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    {{-- 5. Estilos personalizados para el calendario y la tarjeta --}}
    <style>
        /* Tarjeta redondeada */
        .card {
            border-radius: 1rem !important;
        }

        /* Ocultar el input falso, el calendario 'inline' no lo necesita */
        #datepicker {
            border: none;
            box-shadow: none;
            background: transparent;
            padding: 0;
        }

        /* Hacer que el calendario ocupe todo el ancho del contenedor */
        .flatpickr-calendar {
            width: 100% !important;
            max-width: 100%;
            box-shadow: none !important;
        }

        /* CAMBIO 3: Centrar el nombre del mes */
        .flatpickr-month {
            text-align: center;
        }

        /* Estilos para que se vea "vistoso" con el tema de AdminLTE */
        .flatpickr-day.selected, 
        .flatpickr-day.startRange, 
        .flatpickr-day.endRange, 
        .flatpickr-day.selected.inRange, 
        .flatpickr-day.startRange.inRange, 
        .flatpickr-day.endRange.inRange, 
        .flatpickr-day.selected:focus, 
        .flatpickr-day.startRange:focus, 
        .flatpickr-day.endRange:focus, 
        .flatpickr-day.selected:hover, 
        .flatpickr-day.startRange:hover, 
        .flatpickr-day.endRange:hover, 
        .flatpickr-day.selected.prevMonthDay, 
        .flatpickr-day.startRange.prevMonthDay, 
        .flatpickr-day.endRange.prevMonthDay, 
        .flatpickr-day.selected.nextMonthDay, 
        .flatpickr-day.startRange.nextMonthDay, 
        .flatpickr-day.endRange.nextMonthDay {
            background: #007bff; /* Color primario de AdminLTE */
            border-color: #007bff;
        }

        .flatpickr-day.today {
            border-color: #007bff;
        }
        .flatpickr-day.today:hover {
            background: #e9ecef;
            border-color: #007bff;
        }
        
        /* Fuente más grande para los días */
        .flatpickr-day {
            font-size: 1rem;
            padding: 0.5rem 0;
            max-width: none;
        }
        .flatpickr-weekdays {
            font-size: 1.1rem;
        }
        span.flatpickr-weekday {
            font-weight: 600;
        }
    </style>
@stop

@section('js')
    {{-- 1. Cargar la LIBRERÍA PRINCIPAL de flatpickr --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- 2. Cargar la localización en español (después de la principal) --}}
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>

    <script>
    // 2. Envolvemos TODA la lógica en este evento para asegurar
    //    que el script 'es.js' se haya cargado primero.
    document.addEventListener("DOMContentLoaded", () => {

        const hiddenContainer = document.getElementById("hidden-dates");

        // Obtener las fechas que ya estaban guardadas (renderizadas por Blade)
        const existingDateInputs = Array.from(hiddenContainer.querySelectorAll('input'));
        const defaultDates = existingDateInputs.map(input => input.value);

        const datepicker = flatpickr("#datepicker", {
            locale: "es", // <-- Traduccion
            mode: "multiple",
            dateFormat: "Y-m-d",
            minDate: "today",
            inline: true,
            showMonths: 2, // <-- Mostrar 2 meses
            defaultDate: defaultDates, // <-- Cargar días guardados

            // Forzar inicio de semana en Lunes
            firstDayOfWeek: 1, 

            // Deshabilitar fines de semana (Sábado y Domingo)
            disable: [
                function(date) {
                    // 0 = Domingo, 6 = Sábado
                    return (date.getDay() === 0 || date.getDay() === 6);
                }
            ],
            
            onChange: function(selectedDates, dateStr, instance) {
                // Limpiar el contenedor
                hiddenContainer.innerHTML = "";
                
                // Volver a crear los inputs hidden con las fechas seleccionadas
                selectedDates.forEach(date => {
                    const input = document.createElement("input");
                    input.type = "hidden";
                    input.name = "dates[]";
                    input.value = instance.formatDate(date, "Y-m-d");
                    hiddenContainer.appendChild(input);
                });
            }
        });

    });
    </script>
@stop