@extends('adminlte::page')

@section('title', 'Gestión de Atención')

@section('content_header')
    <h1 class="text-center">Gestión Atención (Horarios - Turnos)</h1>
@stop

@section('content')
    <div class="row">
        {{-- Contenedor del Calendario --}}
        <div class="col-lg-10 mx-auto">
            <div class="card shadow">
                <div class="card-body">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- 
      --- SECCIÓN DE CUBÍCULOS ( TARJETAS TIPO WIDGET) ---
    --}}
    <div class="mt-4">
       <h2 class="text-center">Cubículos Activos</h2>
    </div>
    
    <div class="row mt-3">
        {{-- 
          --- ¡AQUÍ ESTÁ LA CORRECCIÓN! ---
          Iniciamos el bucle @forelse ANTES de crear la columna/tarjeta.
        --}}
        @forelse ($cubiculos as $cubiculo)
            <div class="col-lg-4 col-md-6 mb-4">
                {{-- 
                  Tarjeta con el borde de color superior.
                  ¡CORRECCIÓN! Usamos $cubiculo (singular) en lugar de $cubiculos (plural)
                --}}
                <div class="card shadow-sm card-outline {{ $cubiculo->tipo_atencion == 'virtual' ? 'card-primary' : 'card-success' }}">
                    
                    {{-- 
                    Este es el código que elimina la separación.
                    No usamos 'list-group', solo un 'card-body'.
                    --}}
                    <div class="card-body">
                        
                        {{-- 1. Persona Asignada (con Avatar) --}}
                        {{-- Este bloque tiene un margen inferior (mb-3) para separarlo del bloque de abajo --}}
                        <div class="d-flex align-items-center mb-3">
                            @php
                                $name = $cubiculo->users->name ?? 'No Asignado';
                                $initials = 'NA';
                                if ($name != 'No Asignado') {
                                    $parts = explode(' ', $name);
                                    $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
                                }
                            @endphp
                            <img src="https://ui-avatars.com/api/?name={{ $initials }}&background=random&color=fff&size=40" 
                                 class="rounded-circle mr-3" 
                                 width="40" height="40" 
                                 alt="{{ $name }}">
                            <div>
                                <span class="d-block text-muted" style="font-size: 0.8rem;">Asignado a:</span>
                                <strong class="text-dark">{{ $name }}</strong>
                            </div>
                        </div>

                        {{-- 2. Ubicación / Enlace --}}
                        <div>
                            <span class="d-block text-muted" style="font-size: 0.8rem;">Ubicación / Enlace:</span>
                            @php
                                $location = $cubiculo->enlace_o_ubicacion;
                                $isLink = filter_var($location, FILTER_VALIDATE_URL);
                            @endphp

                            @if ($isLink)
                                <a href="{{ $location }}" target="_blank" rel="noopener noreferrer">
                                    <i class="fas fa-link mr-2 text-primary"></i>
                                    Enlace de la reunión
                                </a>
                            @else
                                <i class="fas fa-map-marker-alt mr-2 text-success"></i>
                                <span class="text-dark">{{ $location }}</span>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        {{-- 
          Sección @empty por si no hay cubículos 
        --}}
        @empty
            <div class="col-12">
                <p class="text-center text-muted">No hay cubículos activos registrados.</p>
            </div>
        {{-- 
          --- ¡AQUÍ ESTÁ LA CORRECCIÓN! ---
          Cerramos el bucle @forelse
        --}}
        @endforelse 
    </div>
@stop

@section('css')
    <style>
        .fc-day-sat,
        .fc-day-sun {
            background-color: #f0f0f0 !important;
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Ajuste para que el texto en la cabecera 
           de la tarjeta sea siempre blanco 
        */
        .card-header .card-title {
            color: white !important;
        }
    </style>
@stop

@section('js')
    {{-- 1. Importación de la librería FullCalendar --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>
    
    {{-- 2. Importación del IDIOMA ESPAÑOL --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/locales/es.global.min.js'></script>

    {{-- 3. Script de inicialización --}}
    <script>
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            
            initialView: 'dayGridMonth', 
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            
            events: @json($calendarEvents ?? []),

            locale: 'es',     
            weekends: true,     
            firstDay: 1,
            
            
            buttonText: {
                today:    'Hoy',
                month:    'Mes',
                week:     'Semana',
                day:      'Día'
            }
        });
        
        calendar.render();
    </script>
@stop