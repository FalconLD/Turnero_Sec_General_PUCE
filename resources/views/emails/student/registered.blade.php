@component('mail::message')
# Estimado {{ $student->names }},

Usted ha tomado una cita para Psicología Aplicada APsU.

**Su cita es:** {{ $shift->date }} a las {{ $shift->time }}  
**Cubículo:** {{ $shift->cubicle_name }}  
**Modalidad:** {{ $shift->modalidad }}  
**Enlace o ubicación:** {{ $shift->location }}

Gracias por su atención,  
{{ config('app.name') }}
@endcomponent
