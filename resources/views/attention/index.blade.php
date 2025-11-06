@extends('adminlte::page')

@section('title', 'Gestión de Atención')

@section('content_header')
    <h1 class="m-0 text-dark text-center">Gestión Atención (Horarios - Turnos)</h1>
@stop

@section('content')
    <div class="row">

        {{-- 
          --- COLUMNA DEL CALENDARIO (8 de 12 columnas) ---
        --}}
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>

        {{-- 
          --- COLUMNA DE CUBÍCULOS ACTIVOS (4 de 12 columnas) ---
        --}}
        <div class="col-lg-4">
            {{-- 
              --- ¡AQUÍ ESTÁ EL CAMBIO! ---
              Cambiamos <h2> por <h3> y añadimos 'text-muted' 
              para hacerlo más pequeño y menos prominente.
            --}}
            <h4 class="text-center text-muted">Equipo</h4>
            
            @forelse ($cubiculos as $cubiculo)
                <div class="mb-3"> 
                    <div class="card shadow-sm card-outline {{ $cubiculo->tipo_atencion == 'virtual' ? 'card-primary' : 'card-success' }}">
                        
                        <div class="card-body p-2">
                            
                            <div class="d-flex align-items-center mb-2">
                                @php
                                    $name = $cubiculo->users->name ?? 'No Asignado';
                                    $initials = 'NA';
                                    if ($name != 'No Asignado') {
                                        $parts = explode(' ', $name);
                                        $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
                                    }
                                @endphp
                                <img src="https://ui-avatars.com/api/?name={{ $initials }}&background=random&color=fff&size=30" 
                                     class="rounded-circle mr-2" 
                                     width="30" height="30" 
                                     alt="{{ $name }}">
                                <div>
                                    {{-- 1. Nombre del Cubículo --}}
                                    <strong class="d-block text-dark" style="font-size: 0.9rem;">{{ $cubiculo->nombre }}</strong>
                                    
                                    {{-- 2. Asignado a + Nombre --}}
                                    <span class="text-muted" style="font-size: 0.75rem;">
                                        Asignado a: <strong class="text-dark" style="font-size: 0.85rem;">{{ $name }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div>
                                <span class="d-block text-muted" style="font-size: 0.75rem;">Ubicación / Enlace:</span>
                                @php
                                    $location = $cubiculo->enlace_o_ubicacion;
                                    $isLink = filter_var($location, FILTER_VALIDATE_URL);
                                @endphp

                                @if ($isLink)
                                    <a href="{{ $location }}" target="_blank" rel="noopener noreferrer" style="font-size: 0.9rem;">
                                        <i class="fas fa-link mr-2 text-primary"></i>
                                        Enlace de la reunión
                                    </a>
                                @else
                                    <i class="fas fa-map-marker-alt mr-2 text-success"></i>
                                    <span class="text-dark" style="font-size: 0.9rem;">{{ $location }}</span>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted">No hay cubículos activos registrados.</p>
                </div>
            @endforelse 
        </div>

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