<x-mail::message>
# ✅ Prueba de Email Exitosa

**Sistema de Turnos PUCE**

Hola,

Este es un correo de prueba para verificar que el sistema de envío de emails está funcionando correctamente.

## 📊 Datos de Prueba:
- **Fecha:** {{ now()->format('d/m/Y H:i:s') }}
- **Entorno:** {{ app()->environment() }}
- **Destinatario:** prueba@gmail.com
- **Configuración:** {{ config('mail.default') }}

## 🔧 Configuración SMTP:
- Host: {{ config('mail.mailers.smtp.host') }}
- Puerto: {{ config('mail.mailers.smtp.port') }}
- Encriptación: {{ config('mail.mailers.smtp.encryption') }}

<x-mail::panel>
**Estado:** ✅ Sistema de email operativo
</x-mail::panel>

<x-mail::button :url="config('app.url')">
Acceder al Sistema
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>