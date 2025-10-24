@extends('adminlte::page')

@section('title', 'Seleccionar Días')

@section('content_header')
<h1 class="text-center mb-4">
    Selecciona los días de atención del horario
</h1>
@stop

@section('content')
<div class="container d-flex justify-content-center">
    <div class="card shadow-lg w-75 border-0 p-4">
        <form action="{{ route('days.store') }}" method="POST">
            @csrf
            <input type="hidden" name="schedule" value="{{ $schedule->id_hor }}">

            <div class="mb-3">
                <label for="datepicker" class="form-label">Seleccione los días:</label>
                <div id="datepicker" class="form-control"></div>
                <div id="hidden-dates">
                    @if(old('dates'))
                        @foreach(old('dates') as $d)
                            <input type="hidden" name="dates[]" value="{{ $d }}">
                        @endforeach
                    @endif
                </div>
                @error('dates')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100">Guardar</button>
        </form>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
const hiddenContainer = document.getElementById("hidden-dates");

const datepicker = flatpickr("#datepicker", {
    mode: "multiple",
    dateFormat: "Y-m-d",
    minDate: "today",
    inline: true,
    showMonths: 1,
    onChange: function(selectedDates) {
        hiddenContainer.innerHTML = "";
        selectedDates.forEach(date => {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "dates[]";
            input.value = datepicker.formatDate(date, "Y-m-d");
            hiddenContainer.appendChild(input);
        });
    }
});

// Inicializar con valores previos
document.addEventListener("DOMContentLoaded", () => {
    const oldInputs = Array.from(hiddenContainer.querySelectorAll('input')).map(i => i.value);
    oldInputs.forEach(d => datepicker.addDate(d));
});
</script>
@stop
