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
                                    @if($payment->comprobante_path)
                                        {{-- USAR asset('storage/..') suponiendo que en BD guardas: comprobantes/archivo.jpg --}}
                                        <button class="btn btn-xs btn-info"
                                                data-toggle="modal"
                                                data-target="#comprobanteModal"
                                                data-src="{{ asset('storage/' . $payment->comprobante_path) }}"
                                                data-student="{{ $payment->studentRegistration->names ?? 'Estudiante' }}">
                                            Ver Comprobante
                                        </button>

                                        @if(stripos($payment->comprobante_path, '.pdf') === false)
                                            <i class="fas fa-eye quick-view-trigger"
                                               data-src="{{ asset('storage/' . $payment->comprobante_path) }}">
                                            </i>
                                        @endif
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
                                                <span class="text-muted" title="{{ $payment->verified_at ? $payment->verified_at->format('d/m/Y H:i:s') : '' }}">
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

{{-- MODAL PARA VER COMPROBANTE --}}
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
                <div id="imageControls" class="mb-2" style="display:none;">
                    <button type="button" class="btn btn-secondary btn-sm" id="zoomOutBtn" title="Alejar"><i class="fas fa-search-minus"></i></button>
                    <button type="button" class="btn btn-secondary btn-sm" id="zoomInBtn" title="Acercar"><i class="fas fa-search-plus"></i></button>
                    <button type="button" class="btn btn-secondary btn-sm" id="rotateLeftBtn" title="Girar Izquierda"><i class="fas fa-undo-alt"></i></button>
                    <button type="button" class="btn btn-secondary btn-sm" id="rotateRightBtn" title="Girar Derecha"><i class="fas fa-redo-alt"></i></button>
                    <button type="button" class="btn btn-secondary btn-sm" id="resetImageBtn" title="Reiniciar"><i class="fas fa-sync-alt"></i> Reset</button>
                </div>

                <div id="imageViewerContainer" style="display:none;">
                    <img id="comprobanteImagen" src="" class="interactive" alt="Comprobante" />
                </div>

                <iframe id="comprobantePdf" src="" style="width: 100%; height: 75vh; display: none; border: none;" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PARA MOTIVO DE RECHAZO --}}
<div class="modal fade" id="rejectionModal" tabindex="-1" role="dialog" aria-labelledby="rejectionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Motivo del Rechazo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Por favor, especifique el motivo del rechazo:</label>
                        <textarea id="rejection_reason" name="rejection_reason" class="form-control" rows="3" required minlength="5" placeholder="Ej: El comprobante está borroso, el valor es incorrecto..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Rechazo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="quickViewBox" style="display:none; position:absolute;">
    <img src="" id="quickViewImage" style="max-width:300px; max-height:300px; display:block;" />
</div>
@stop

@push('css')
<style>
    #imageViewerContainer {
        overflow: auto;
        max-height: 70vh;
        background-color: #f3f3f3;
        border-radius: 4px;
    }
    #comprobanteImagen.interactive {
        transition: transform 0.2s ease;
        transform-origin: center center;
        cursor: grab;
        max-width: 100%;
    }
    #comprobanteImagen.interactive:active { cursor: grabbing; }
    #imageControls { margin-bottom:10px; }
    #quickViewBox {
        border: 2px solid #aaa;
        background: #fff;
        padding: 5px;
        z-index: 1060;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        border-radius: 4px;
        pointer-events: none;
    }
    .quick-view-trigger { cursor: help; margin-left:8px; color:#007bff; font-size:1.1rem; }
</style>
@endpush

@push('js')
<script>
$(function() {
    // Variables para zoom/rotación
    let currentZoom = 1.0;
    let currentRotation = 0;
    const zoomStep = 0.2;

    function applyTransform() {
        $('#comprobanteImagen').css('transform', `scale(${currentZoom}) rotate(${currentRotation}deg)`);
    }

    // Abrir modal de comprobante
    $('#comprobanteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var src = button.data('src') || '';
        var student = button.data('student') || '';
        var modal = $(this);
        modal.find('.modal-title').text('Comprobante de ' + student);

        var imgContainer = modal.find('#imageViewerContainer');
        var imgControls = modal.find('#imageControls');
        var imgTag = modal.find('#comprobanteImagen');
        var iframeTag = modal.find('#comprobantePdf');

        // reset
        imgContainer.hide();
        imgControls.hide();
        iframeTag.hide();
        imgTag.attr('src', '');
        iframeTag.attr('src', '');
        currentZoom = 1.0;
        currentRotation = 0;
        applyTransform();

        if (!src) return;

        if (src.toLowerCase().endsWith('.pdf')) {
            iframeTag.attr('src', src).show();
        } else {
            imgTag.attr('src', src);
            imgControls.show();
            imgContainer.show();
        }
    });

    $('#comprobanteModal').on('hide.bs.modal', function () {
        $(this).find('#comprobanteImagen').attr('src', '');
        $(this).find('#comprobantePdf').attr('src', '');
        currentZoom = 1.0; currentRotation = 0; applyTransform();
    });

    // Controles
    $('#zoomInBtn').on('click', function(){ currentZoom += zoomStep; applyTransform(); });
    $('#zoomOutBtn').on('click', function(){ if (currentZoom > zoomStep * 2) { currentZoom -= zoomStep; applyTransform(); } });
    $('#rotateLeftBtn').on('click', function(){ currentRotation -= 90; applyTransform(); });
    $('#rotateRightBtn').on('click', function(){ currentRotation += 90; applyTransform(); });
    $('#resetImageBtn').on('click', function(){ currentZoom = 1.0; currentRotation = 0; applyTransform(); });

    // Rejection modal: set action
    $('#rejectionModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var rejectUrl = button.data('reject-url') || '';
        $(this).find('form').attr('action', rejectUrl);
    });

    // Quick view (hover)
    var $quickViewBox = $('#quickViewBox');
    var $quickViewImage = $('#quickViewImage');

    $('.table').on('mouseenter', '.quick-view-trigger', function(event) {
        var src = $(this).data('src') || '';
        if (!src) return;
        $quickViewImage.attr('src', src);
        $quickViewBox.css({ top: (event.pageY + 20) + 'px', left: (event.pageX + 20) + 'px' }).show();
    });
    $('.table').on('mouseleave', '.quick-view-trigger', function() {
        $quickViewBox.hide(); $quickViewImage.attr('src','');
    });
    $('.table').on('mousemove', '.quick-view-trigger', function(event) {
        $quickViewBox.css({ top: (event.pageY + 20) + 'px', left: (event.pageX + 20) + 'px' });
    });

    // Date range picker init
    $('#dateRangePicker').daterangepicker({
        opens: 'left',
        autoUpdateInput: false,
        locale: {
            format: 'MM/DD/YYYY',
            separator: ' - ',
            applyLabel: 'Aplicar',
            cancelLabel: 'Limpiar',
            fromLabel: 'Desde',
            toLabel: 'Hasta',
            customRangeLabel: 'Personalizado',
            weekLabel: 'S',
            daysOfWeek: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            firstDay: 1
        },
        ranges: {
            'Hoy': [moment(), moment()],
            'Ayer': [moment().subtract(1,'days'), moment().subtract(1,'days')],
            'Últimos 7 Días': [moment().subtract(6,'days'), moment()],
            'Últimos 30 Días': [moment().subtract(29,'days'), moment()],
            'Este Mes': [moment().startOf('month'), moment().endOf('month')],
            'Mes Pasado': [moment().subtract(1,'month').startOf('month'), moment().subtract(1,'month').endOf('month')]
        }
    });

    // Si viene valor por defecto lo mostramos
    var initialRange = "{{ $dateRange ?? '' }}";


    if (initialRange) {
        $('#dateRangePicker').val(initialRange);
    }

    $('#dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });
    $('#dateRangePicker').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

}); // ready
</script>
@endpush
