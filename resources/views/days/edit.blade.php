@extends('adminlte::page')

@section('title', 'Editar Días de Atención')

@section('content_header')
<h1 class="text-center mb-4">
    Editar los días de atención del horario
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
                    @if(old('dates', $existingDays))
                        @foreach(old('dates', $existingDays) as $d)
                            <input type="hidden" name="dates[]" value="{{ $d }}">
                        @endforeach
                    @endif
                </div>
                @error('dates')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100">Actualizar Días</button>
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
const existingDays = @json($existingDays ?? []);

const datepicker = flatpickr("#datepicker", {
    mode: "multiple",
    dateFormat: "Y-m-d",
    minDate: "today",
    inline: true,
    showMonths: 1,
    defaultDate: existingDays,
    onChange: function(selectedDates) {
        hiddenContainer.innerHTML = "";
        selectedDates.forEach(date => {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "dates[]";
            input.value = flatpickr.formatDate(date, "Y-m-d");
            hiddenContainer.appendChild(input);
        });
    }
});

// To pre-fill the hidden inputs on initial load
document.addEventListener("DOMContentLoaded", () => {
    const initialDates = @json(old('dates', $existingDays));
    hiddenContainer.innerHTML = "";
    initialDates.forEach(d => {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "dates[]";
        input.value = d;
        hiddenContainer.appendChild(input);
    });
    datepicker.setDate(initialDates, false); // Update flatpickr instance
});
</script>
@stop
