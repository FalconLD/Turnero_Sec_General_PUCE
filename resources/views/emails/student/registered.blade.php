@component('mail::message')
# Estimado {{ $student->names }},

**Los datos de su cita son:**

* **Fecha y Hora:** {{ $shift->date_shift }} a las {{ $shift->start_shift }}
* **Cubículo:** {{ $shift->cubicle->nombre }}
* **Modalidad:** {{ $shift->cubicle->tipo_atencion }}
* **Ubicación:** {{ $shift->cubicle->enlace_o_ubicacion }}

Gracias por su atención,  
{{ config('app.name') }}
@endcomponent
