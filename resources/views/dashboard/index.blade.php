@extends('adminlte::page')
 
@section('title', 'Dashboard Principal')
 
@section('content')
<div class="container-fluid">
 
    {{-- ======= FILTROS ======= --}}
    {{-- ======= FILTROS + BOTÓN EXPORTAR ======= --}}
<form method="GET" action="{{ route('dashboard.index') }}" class="mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label for="anio" class="form-label">Año</label>
            <select name="anio" id="anio" class="form-control">
                <option value="">Todos</option>
                @foreach ($aniosDisponibles as $a)
                    <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
        </div>
 
        <div class="col-md-3">
            <label for="mes" class="form-label">Mes</label>
            <select name="mes" id="mes" class="form-control">
                <option value="">Todos</option>
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
 
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-filter"></i> Filtrar
            </button>
        </div>
 
        <div class="col-md-2">
            <button id="exportPdfBtn" class="btn btn-danger w-100">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </button>
        </div>
    </div>
</form>
 
 
    {{-- ======= TARJETAS KPI ======= --}}
 
    <div class="row justify-content-center">
        <div class="col-md-3 mb-3">
            <div class="small-box bg-info">
                <div class="inner text-center">
                    <h3>{{ $totalEstudiantes }}</h3>
                    <p>Estudiantes registrados</p>
                </div>
                <div class="icon"><i class="fas fa-user-graduate"></i></div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="small-box bg-success">
                <div class="inner text-center">
                    <h3>{{ $turnosAtendidos }}</h3>
                    <p>Turnos atendidos</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="small-box bg-warning">
                <div class="inner text-center">
                    <h3>{{ $turnosPendientes }}</h3>
                    <p>Turnos pendientes</p>
                </div>
                <div class="icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>
 
 
    {{-- ======= FILA 1: Turnos + Pagos ======= --}}
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <canvas id="turnosDiaChart" style="height:260px;"></canvas>
                </div>
            </div>
        </div>
 
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <canvas id="pagosFormaChart" style="height:260px;"></canvas>
                </div>
            </div>
        </div>
    </div>
 
    {{-- ======= FILA 2: Turnos Atendidos vs Libres + Cubículos ======= --}}
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <canvas id="turnosStatusChart" style="height:260px;"></canvas>
                </div>
            </div>
        </div>
 
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <canvas id="cubiculosChart" style="height:260px;"></canvas>
                </div>
            </div>
        </div>
    </div>
 
</div>



@endsection

 
@section('js')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- jsPDF y html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
 
<script>
// --- Gráfico: Turnos por Día ---
// --- Gráfico: Turnos por Día ---
const turnosLabels = @json($turnosPorDia->pluck('fecha'));
const turnosData   = @json($turnosPorDia->pluck('total'));
 
// Generar tonos de azul
const blueShades = turnosData.map((_, i) => `rgba(54, 162, 235, ${0.3 + 0.7 * (i / turnosData.length)})`);
 
new Chart(document.getElementById('turnosDiaChart'), {
    type: 'bar',
    data: {
        labels: turnosLabels,
        datasets: [{
            label: 'Turnos por día',
            data: turnosData,
            backgroundColor: blueShades,
            borderColor: blueShades.map(c => c.replace('0.', '1.')), // borde más fuerte
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, precision: 0 } }
    }
});
 
 
// --- Gráfico: Pagos por Forma ---
const pagosLabels = @json($pagosPorForma->pluck('forma_pago'));
const pagosData   = @json($pagosPorForma->pluck('total'));
const blueShadesPagos = pagosData.map((_, i) => `rgba(54, 162, 235, ${0.3 + 0.7 * (i / pagosData.length)})`);
 
new Chart(document.getElementById('pagosFormaChart'), {
    type: 'bar',
    data: {
        labels: pagosLabels,
        datasets: [{
            label: 'Estudiantes por forma de pago',
            data: pagosData,
            backgroundColor: blueShadesPagos,
            borderColor: blueShadesPagos.map(c => c.replace('0.', '1.')),
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true, precision: 0 } },
        plugins: { legend: { display: false }, title: { display: true, text: 'Pagos por forma de pago' } }
    }
});
 
 
// --- Gráfico: Turnos Atendidos vs Libres ---
const blueShadesStatus = ['rgba(54, 162, 235, 0.6)', 'rgba(54, 162, 235, 0.3)'];
new Chart(document.getElementById('turnosStatusChart'), {
    type: 'bar',
    data: {
        labels: ['Atendidos', 'Libres'],
        datasets: [{
            label: 'Turnos',
            data: [{{ $turnosAtendidos }}, {{ $turnosPendientes }}],
            backgroundColor: blueShadesStatus,
            borderColor: blueShadesStatus.map(c => c.replace('0.', '1.')),
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false }, title: { display: true, text: 'Turnos atendidos vs libres' } },
        scales: { y: { beginAtZero: true, precision: 0 } }
    }
});
 
 
// --- Gráfico: Registros por Cubículo y Tipo de Atención ---
const cubiculosData = @json($cubiculosQuery);
const cubiculosLabels = [...new Set(cubiculosData.map(c => c.nombre))];
const tiposAtencion = [...new Set(cubiculosData.map(c => c.tipo_atencion))];
 
// Dataset por tipo de atención con tonos de azul
const datasetsCubiculos = tiposAtencion.map((tipo, i) => ({
    label: tipo,
    data: cubiculosLabels.map(cub => {
        const registro = cubiculosData.find(d => d.nombre === cub && d.tipo_atencion === tipo);
        return registro ? registro.total : 0;
    }),
    backgroundColor: `rgba(54, 162, 235, ${0.3 + 0.7 * (i / tiposAtencion.length)})`
}));
 
new Chart(document.getElementById('cubiculosChart'), {
    type: 'bar',
    data: {
        labels: cubiculosLabels,
        datasets: datasetsCubiculos
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true, precision: 0 }, x: { stacked: true } },
        plugins: { title: { display: true, text: 'Registros por Cubículo y Tipo de Atención' }, tooltip: { mode: 'index', intersect: false } }
    }
});
 
</script>
<script>
document.getElementById('exportPdfBtn').addEventListener('click', async (e) => {
    e.preventDefault(); // Evita que el formulario se envíe
 
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('p', 'mm', 'a4');
    const charts = ['turnosDiaChart', 'pagosFormaChart', 'turnosStatusChart', 'cubiculosChart'];
   
    let yOffset = 10; // margen superior
 
    for (let i = 0; i < charts.length; i++) {
        const canvas = document.getElementById(charts[i]);
 
        // html2canvas espera un elemento DOM
        const imgData = await html2canvas(canvas, { scale: 2 }).then(c => c.toDataURL('image/png'));
 
        const imgProps = pdf.getImageProperties(imgData);
        const pdfWidth = pdf.internal.pageSize.getWidth() - 20; // margen horizontal
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
 
        // Si se pasa del alto, agregar página
        if (yOffset + pdfHeight > pdf.internal.pageSize.getHeight()) {
            pdf.addPage();
            yOffset = 10;
        }
 
        pdf.addImage(imgData, 'PNG', 10, yOffset, pdfWidth, pdfHeight);
        yOffset += pdfHeight + 10; // espacio entre gráficos
    }
 
    pdf.save('dashboard.pdf'); // Fuerza la descarga
});
</script>
 
@endsection