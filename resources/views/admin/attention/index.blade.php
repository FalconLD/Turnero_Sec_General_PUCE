@extends('adminlte::page')

@section('title', 'Gestión de Atención')

@section('content_header')
    <h1 class="m-0 text-dark text-center">Gestión Atención (Horarios - Turnos)</h1>
@stop

@section('content')
    <div class="row">
        {{-- COLUMNA DEL CALENDARIO (8 de 12 columnas) --}}
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>

        {{-- COLUMNA DE CUBÍCULOS ACTIVOS (4 de 12 columnas) --}}
        <div class="col-lg-4">
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
                                    <strong class="d-block text-dark" style="font-size: 0.9rem;">{{ $cubiculo->nombre }}</strong>
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
        
        /* ✅ ESTILOS MEJORADOS PARA EL CALENDARIO */
        #calendar {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .fc-event {
            border-radius: 4px;
            border: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .fc-event:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .fc-event-title {
            white-space: normal !important;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }
        
        .fc-daygrid-event {
            padding: 2px 4px;
        }
        
        /* ✅ RESPONSIVE */
        @media (max-width: 768px) {
            .fc-toolbar {
                flex-direction: column;
                gap: 10px;
            }
            
            .fc-toolbar-chunk {
                text-align: center;
                width: 100%;
            }
        }
        
        /* ✅ COLORES MEJORADOS */
        .fc-event.ocupado {
            background-color: #dc3545 !important;
            border-color: #bd2130 !important;
        }
        
        .fc-event.libre {
            background-color: #28a745 !important;
            border-color: #1e7e34 !important;
        }
    </style>
@stop

@section('js')
    {{-- 1. Importación de la librería FullCalendar --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>
    
    {{-- 2. Importación del IDIOMA ESPAÑOL --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/locales/es.global.min.js'></script>
    
    {{-- 3. Tippy.js para tooltips (OPCIONAL) --}}
    {{-- Si no quieres tooltips, comenta o elimina las siguientes 3 líneas --}}
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/themes/light.css" />

    {{-- 4. Script de inicialización --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                
                initialView: 'dayGridMonth', 
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                
                events: @json($calendarEvents ?? []),
                
                // ✅ CONFIGURACIONES MEJORADAS PARA MEJOR VISUALIZACIÓN
                eventTimeFormat: { // Formato de hora para los eventos
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false,
                    meridiem: false
                },
                
                // ✅ AJUSTAR ALTURA DE EVENTOS PARA MEJOR LECTURA
                eventMinHeight: 25, // Altura mínima de cada evento
                eventShortHeight: 30, // Altura para eventos cortos
                
                // ✅ MEJORAR LA VISUALIZACIÓN DE EVENTOS SUPERPUESTOS
                eventOverlap: false, // Evitar que se superpongan
                slotEventOverlap: false, // Evitar superposición en vista de tiempo
                
                // ✅ MEJORAR EL TÍTULO DE LOS EVENTOS (VERSIÓN SIMPLIFICADA)
                eventDisplay: 'block', // Mostrar como bloque
                
                // ✅ TOOLTIP PARA MOSTRAR MÁS INFORMACIÓN (OPCIONAL)
                // Si no quieres tooltips, elimina o comenta esta sección
                eventDidMount: function(info) {
                    // Verificar si Tippy está disponible
                    if (typeof tippy !== 'undefined') {
                        const horaInicio = info.event.extendedProps.hora_inicio || '';
                        const horaFin = info.event.extendedProps.hora_fin || '';
                        const estado = info.event.extendedProps.estado || '';
                        
                        const tooltipText = `${horaInicio} - ${horaFin} | ${estado}`;
                        
                        tippy(info.el, {
                            content: tooltipText,
                            placement: 'top',
                            theme: 'light',
                            arrow: true
                        });
                    }
                },
                
                // ✅ CONFIGURACIÓN DE VISTAS
                dayMaxEvents: 4, // Máximo de eventos por día en vista mensual
                dayMaxEventRows: true, // Limitar filas de eventos
                
                // ✅ CONFIGURACIÓN DE HORAS (solo aplica en vistas de tiempo)
                slotMinTime: '07:00:00', // Hora mínima a mostrar
                slotMaxTime: '22:00:00', // Hora máxima a mostrar
                slotDuration: '01:00:00', // Duración de cada slot
                
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
        });
    </script>
@stop