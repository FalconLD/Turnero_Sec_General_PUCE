@extends('adminlte::page')

@section('title', 'Revisión de Pagos')

@section('content_header')
    <h1 class="m-0 text-dark text-center">Gestión Pagos</h1>
@stop

@section('plugins.Daterangepicker', true)

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Mensajes --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif

        {{-- KPIs --}}
        <div class="row mb-3">
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>${{ number_format($totalVerificadoMes, 2) }}</h3>
                        <p>Verificado (Este Mes)</p>
                    </div>
                    <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>${{ number_format($totalPendiente, 2) }}</h3>
                        <p>Pendiente (Filtro)</p>
                    </div>
                    <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $pagosPendientes }}</h3>
                        <p>Pagos Pendientes (Filtro)</p>
                    </div>
                    <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $totalRegistros }}</h3>
                        <p>Registros (Filtro)</p>
                    </div>
                    <div class="icon"><i class="fas fa-list-ol"></i></div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ route('payments.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-5">
                            <label>Buscar Estudiante:</label>
                            <input type="text" name="search" class="form-control" placeholder="Nombre o Cédula" value="{{ $search ?? '' }}">
                        </div>

                        <div class="col-md-4">
                            <label>Rango de Fechas (Creación):</label>
                            <input type="text" name="date_range" id="dateRangePicker" class="form-control" value="{{ $dateRange ?? '' }}" placeholder="Seleccione un rango...">
                        </div>

                        <div class="col-md-1">
                            <label>Por Pág:</label>
                            <select name="per_page" class="form-control">
                                <option value="10" @if($perPage == 10) selected @endif>10</option>
                                <option value="25" @if($perPage == 25) selected @endif>25</option>
                                <option value="50" @if($perPage == 50) selected @endif>50</option>
                                <option value="100" @if($perPage == 100) selected @endif>100</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-block">Aplicar</button>
                            <a href="{{ route('payments.index') }}" class="btn btn-secondary ml-2" title="Limpiar filtros">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla y pestañas --}}
        <div class="card">
            <div class="card-header p-0 pt-3">
                <ul class="nav nav-tabs px-3">
                    <li class="nav-item">
                        <a class="nav-link @if($currentStatus == 'all') active @endif" href="{{ route('payments.index', array_merge(request()->query(), ['status' => 'all', 'page' => 1])) }}">Todos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if($currentStatus == 'pending') active @endif" href="{{ route('payments.index', array_merge(request()->query(), ['status' => 'pending', 'page' => 1])) }}">Pendientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if($currentStatus == 'verified') active @endif" href="{{ route('payments.index', array_merge(request()->query(), ['status' => 'verified', 'page' => 1])) }}">Verificados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if($currentStatus == 'rejected') active @endif" href="{{ route('payments.index', array_merge(request()->query(), ['status' => 'rejected', 'page' => 1])) }}">Rechazados</a>
                    </li>
                </ul>
            </div>

            <div class="card-body p-0">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Cédula</th>
                            <th>Forma de Pago</th>
                            <th>Valor</th>
                            <th>Comprobante</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr @if($payment->status == 'pending') class="table-warning" @endif>
                                <td>{{ $payment->studentRegistration->names ?? 'Estudiante no encontrado' }}</td>
                                <td>{{ $payment->studentRegistration->cedula ?? 'N/A' }}</td>
                                <td>
                                    @if($payment->payment_method == 'efectivo')
                                        <span class="badge badge-secondary">Efectivo</span>
                                    @elseif($payment->payment_method == 'transferencia')
                                        <span class="badge badge-primary">Transferencia</span>
                                    @else
                                        <span class="badge badge-light">{{ $payment->payment_method }}</span>
                                    @endif
                                </td>
                                <td>${{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    @if($payment->comprobante_base64)
                                        {{-- ✅ Muestra base64 --}}
                                        <button class="btn btn-xs btn-info"
                                                data-toggle="modal"
                                                data-target="#comprobanteModal"
                                                data-src="data:{{ $payment->comprobante_mime ?? 'image/jpeg' }};base64,{{ $payment->comprobante_base64 }}"
                                                data-student="{{ $payment->studentRegistration->names ?? 'Estudiante' }}">
                                            Ver Comprobante
                                        </button>
                                    @elseif($payment->comprobante_path)
                                        {{-- ✅ Muestra archivo físico --}}
                                        <button class="btn btn-xs btn-info"
                                                data-toggle="modal"
                                                data-target="#comprobanteModal"
                                                data-src="{{ asset('storage/' . $payment->comprobante_path) }}"
                                                data-student="{{ $payment->studentRegistration->names ?? 'Estudiante' }}">
                                            Ver Comprobante
                                        </button>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($payment->status == 'pending')
                                        <span class="badge badge-warning">Pendiente</span>
                                    @elseif($payment->status == 'verified')
                                        <span class="badge badge-success">Verificado</span>
                                    @elseif($payment->status == 'rejected')
                                        <span class="badge badge-danger">Rechazado</span>
                                    @endif
                                </td>
                                <td>
                                    @if($payment->status == 'pending')
                                        <form action="{{ route('payments.verify', $payment->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-success">
                                                @if($payment->payment_method == 'efectivo') Recibido @else Verificar @endif
                                            </button>
                                        </form>

                                        <button type="button" class="btn btn-xs btn-danger"
                                                data-toggle="modal"
                                                data-target="#rejectionModal"
                                                data-reject-url="{{ route('payments.reject', $payment->id) }}">
                                            Rechazar
                                        </button>
                                    @else
                                        @if($payment->status == 'rejected')
                                            <small class="text-danger" style="font-size: 0.85rem;">
                                                <strong>Motivo del Rechazo:</strong><br>
                                                {{ $payment->rejection_reason ?? 'No se especificó un motivo.' }}<br>
                                                <span class="text-muted">
                                                    (Revisado {{ $payment->verified_at ? $payment->verified_at->diffForHumans() : 'N/A' }} por {{ $payment->verifier->name ?? 'Admin' }})
                                                </span>
                                            </small>
                                        @else
                                            <small class="text-muted">
                                                Revisado por {{ $payment->verifier->name ?? 'Admin' }}<br>
                                                @if($payment->verified_at)
                                                    <span title="{{ $payment->verified_at->format('d/m/Y H:i:s') }}">
                                                        {{ $payment->verified_at->diffForHumans() }}
                                                    </span>
                                                @endif
                                            </small>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay registros que coincidan con los filtros.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer clearfix">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>

{{-- ✅ Modal comprobante con soporte base64 --}}
<div class="modal fade" id="comprobanteModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Comprobante de Pago</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="comprobanteImagen" src="" alt="Comprobante" class="img-fluid rounded shadow-sm">
            </div>
        </div>
    </div>
</div>
@stop

@push('js')
<script>
$('#comprobanteModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const src = button.data('src');
    const student = button.data('student');
    const modal = $(this);

    modal.find('.modal-title').text('Comprobante de ' + student);
    modal.find('#comprobanteImagen').attr('src', src);
});
</script>
@endpush
