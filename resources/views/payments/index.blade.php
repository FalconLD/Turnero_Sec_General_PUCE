@extends('adminlte::page')

@section('title', 'Revisión de Pagos')

@section('content_header')
    <h1 class="m-0 text-dark text-center">Gestión Pagos</h1>
@stop
@section('plugins.Daterangepicker', true)


@section('content')

    <div class="row"> 
        <div class="col-12">
            {{-- Mensajes de Éxito o Advertencia --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
            @endif

            {{-- Fila de Tarjetas de Estadísticas (KPIs) (Idea 1) --}}
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>${{ number_format($totalVerificadoMes, 2) }}</h3>
                            <p>Verificado (Este Mes)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>${{ number_format($totalPendiente, 2) }}</h3>
                            <p>Pendiente (Filtro)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $pagosPendientes }}</h3>
                            <p>Pagos Pendientes (Filtro)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $totalRegistros }}</h3>
                            <p>Registros (Filtro)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-list-ol"></i>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Fin Fila de Tarjetas de Estadísticas --}}


            {{-- Tarjeta de Filtros (Con Idea de Búsqueda y Filtro de Fecha) --}}
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('payments.index') }}" method="GET">
                        <div class="row">
    
                            <div class="col-md-5">
                                <label>Buscar Estudiante:</label>
                                <input type="text" name="search" class="form-control" placeholder="Nombre o Cédula" value="{{ $search ?? '' }}">
                            </div>

                            {{-- NUEVO FILTRO DE FECHA --}}
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

            {{-- Tarjeta de Datos (Con Idea 7: Pestañas) --}}
            <div class="card">
                <div class="card-header p-0 pt-3">
                    {{-- PESTAÑAS (IDEA 7) --}}
                    <ul class="nav nav-tabs px-3">
                        <li class="nav-item">
                            <a class="nav-link @if($currentStatus == 'all') active @endif" 
                               href="{{ route('payments.index', array_merge(request()->query(), ['status' => 'all', 'page' => 1])) }}">
                                Todos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if($currentStatus == 'pending') active @endif" 
                               href="{{ route('payments.index', array_merge(request()->query(), ['status' => 'pending', 'page' => 1])) }}">
                                Pendientes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if($currentStatus == 'verified') active @endif" 
                               href="{{ route('payments.index', array_merge(request()->query(), ['status' => 'verified', 'page' => 1])) }}">
                                Verificados
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if($currentStatus == 'rejected') active @endif" 
                               href="{{ route('payments.index', array_merge(request()->query(), ['status' => 'rejected', 'page' => 1])) }}">
                                Rechazados
                            </a>
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
                                {{-- Resaltar Fila (Idea 2) --}}
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

                                    {{-- Comprobante (Con Idea 8: Vista Rápida) --}}
                                    <td>
                                        @if($payment->comprobante_path)
                                            <button class="btn btn-xs btn-info" 
                                                    data-toggle="modal" 
                                                    data-target="#comprobanteModal" 
                                                    data-src="{{ Storage::url($payment->comprobante_path) }}"
                                                    data-student="{{ $payment->studentRegistration->names ?? 'Estudiante' }}">
                                                Ver Comprobante
                                            </button>

                                            @if(stripos($payment->comprobante_path, '.pdf') === false)
                                                <i class="fas fa-eye quick-view-trigger"
                                                   data-src="{{ Storage::url($payment->comprobante_path) }}">
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

                                    {{-- Acciones (Con Idea 2, 3 y 4) --}}
                                    <td>
                                        @if($payment->status == 'pending')
                                            {{-- Botón Verificar --}}
                                            <form action="{{ route('payments.verify', $payment->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-success">
                                                    @if($payment->payment_method == 'efectivo')
                                                        Recibido
                                                    @else
                                                        Verificar
                                                    @endif
                                                </button>
                                            </form>
                                            
                                            {{-- Botón Rechazar (Idea 3) --}}
                                            <button type="button" 
                                                    class="btn btn-xs btn-danger" 
                                                    data-toggle="modal" 
                                                    data-target="#rejectionModal" 
                                                    data-reject-url="{{ route('payments.reject', $payment->id) }}">
                                                Rechazar
                                            </button>
                                            
                                        @else
                                            {{-- Lógica para Verificado vs Rechazado --}}
                                            @if($payment->status == 'rejected')
                                                {{-- Motivo de Rechazo (Idea 4) --}}
                                                <small class="text-danger" style="font-size: 0.85rem;">
                                                    <strong>Motivo del Rechazo:</strong><br>
                                                    {{ $payment->rejection_reason ?? 'No se especificó un motivo.' }}
                                                    <br>
                                                    <span class="text-muted" title="{{ $payment->verified_at ? $payment->verified_at->format('d/m/Y H:i:s') : '' }}">
                                                        (Revisado {{ $payment->verified_at ? $payment->verified_at->diffForHumans() : 'N/A' }} por {{ $payment->verifier->name ?? 'Admin' }})
                                                    </span>
                                                </small>
                                            
                                            @else 
                                                {{-- Info de Verificado (Con Idea 2: Fecha Humana) --}}
                                                <small class="text-muted">
                                                    Revisado por {{ $payment->verifier->name ?? 'Admin' }}
                                                    <br>
                                                    @if($payment->verified_at)
                                                        <span title="{{ $payment->verified_at->format('d/m/Y H:i:s') }}">
                                                            {{ $payment->verified_at->diffForHumans() }}
                                                        </span>
                                                    @endif
                                                </small>
                                            @endif
                                        @endif
                                    </td>

                                </tr> {{-- FIN de la fila --}}

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

    {{-- MODAL PARA VER EL COMPROBANTE (Con Idea 5: Zoom/Rotar) --}}
    <div class="modal fade" id="comprobanteModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Comprobante de Pago</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    
                    <div id="imageControls">
                        <button type="button" class="btn btn-secondary btn-sm" id="zoomOutBtn" title="Alejar">
                            <i class="fas fa-search-minus"></i>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" id="zoomInBtn" title="Acercar">
                            <i class="fas fa-search-plus"></i>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" id="rotateLeftBtn" title="Girar Izquierda">
                            <i class="fas fa-undo-alt"></i>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" id="rotateRightBtn" title="Girar Derecha">
                            <i class="fas fa-redo-alt"></i>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" id="resetImageBtn" title="Reiniciar">
                            <i class="fas fa-sync-alt"></i> Reset
                        </button>
                    </div>

                    <div id="imageViewerContainer">
                        <img id="comprobanteImagen" src="" class="interactive">
                    </div>

                    {{-- Contenedor para el PDF (sigue igual) --}}
                    <iframe id="comprobantePdf" src="" style="width: 100%; height: 75vh; display: none;" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PARA MOTIVO DE RECHAZO (Idea 3) --}}
    <div class="modal fade" id="rejectionModal" tabindex="-1" role="dialog" aria-labelledby="rejectionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectionModalLabel">Motivo del Rechazo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="rejection_reason">Por favor, especifique el motivo del rechazo:</label>
                            <textarea name="rejection_reason" 
                                      class="form-control" 
                                      rows="3" 
                                      required 
                                      minlength="5"
                                      placeholder="Ej: El comprobante está borroso, el valor es incorrecto..."></textarea>
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

    <div id="quickViewBox">
        <img src="" id="quickViewImage" />
    </div>

@stop

@push('css')
    <style>
        /* Contenedor para la imagen, permite "panear" la imagen zoomeada */
        #imageViewerContainer {
            overflow: auto; /* Habilita el scroll (pan) si la imagen es muy grande */
            max-height: 70vh; /* Deja espacio para los botones */
            background-color: #f3f3f3;
            border-radius: 4px;
            display: none; /* Oculto por defecto */
        }
        /* Estilos de la imagen interactiva */
        #comprobanteImagen.interactive {
            transition: transform 0.2s ease;
            transform-origin: center center;
            cursor: grab;
        }
        #comprobanteImagen.interactive:active {
            cursor: grabbing;
        }
        /* Contenedor de botones (oculto por defecto) */
        #imageControls {
            display: none;
            margin-bottom: 10px;
        }

        /* --- Estilos para la Vista Rápida (Idea 8) --- */
        #quickViewBox {
            position: absolute; /* Sigue al cursor */
            display: none;
            border: 2px solid #aaa;
            background: #fff;
            padding: 5px;
            z-index: 1060; /* Encima de todo */
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            border-radius: 4px;
            pointer-events: none; /* Evita que el mouse interactúe con la caja y cause parpadeo */
        }
        #quickViewBox img {
            max-width: 300px; /* Tamaño de la previsualización */
            max-height: 300px;
            display: block;
        }
        .quick-view-trigger {
            cursor: help; /* Un cursor que indica "hay más info" */
            margin-left: 8px; /* Espacio después del botón "Ver Comprobante" */
            color: #007bff;
            font-size: 1.1rem;
        }
    </style>
@endpush

{{-- PEGA ESTE NUEVO BLOQUE AL FINAL DE TU ARCHIVO --}}
{{-- 3. CÓDIGO JAVASCRIPT --}}
@push('js')
    <script>
        $(document).ready(function() {

            {{-- --- LÓGICA DEL MODAL DE IMAGEN (IDEA 5) --- --}}
            let currentZoom = 1.0;
            let currentRotation = 0;
            const zoomStep = 0.2;

            function applyTransform() {
                $('#comprobanteImagen').css('transform', `scale(${currentZoom}) rotate(${currentRotation}deg)`);
            }

            $('#comprobanteModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var src = button.data('src');
                var student = button.data('student');
                var modal = $(this);
                modal.find('#modalLabel').text('Comprobante de ' + student);
                var imgContainer = modal.find('#imageViewerContainer');
                var imgControls = modal.find('#imageControls');
                var imgTag = modal.find('#comprobanteImagen');
                var iframeTag = modal.find('#comprobantePdf');
                imgContainer.hide();
                imgControls.hide();
                iframeTag.hide();
                currentZoom = 1.0;
                currentRotation = 0;
                applyTransform(); 

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
                currentZoom = 1.0;
                currentRotation = 0;
                applyTransform();
            });

            
            $('#zoomInBtn').on('click', function() {
                currentZoom += zoomStep;
                applyTransform();
            });
            $('#zoomOutBtn').on('click', function() {
                if (currentZoom > zoomStep * 2) { 
                    currentZoom -= zoomStep;
                    applyTransform();
                }
            });
            $('#rotateLeftBtn').on('click', function() {
                currentRotation -= 90;
                applyTransform();
            });
            $('#rotateRightBtn').on('click', function() {
                currentRotation += 90;
                applyTransform();
            });
            $('#resetImageBtn').on('click', function() {
                currentZoom = 1.0;
                currentRotation = 0;
                applyTransform();
            });

            {{-- --- Oyente para el Modal de RECHAZO (IDEA 3) --- --}}
            $('#rejectionModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var rejectUrl = button.data('reject-url');
                var modal = $(this);
                modal.find('form').attr('action', rejectUrl);
            });

            {{-- --- Lógica para la Vista Rápida (Idea 8) --- --}}
            var $quickViewBox = $('#quickViewBox');
            var $quickViewImage = $('#quickViewImage');

            $('.table').on('mouseenter', '.quick-view-trigger', function(event) {
                var src = $(this).data('src');
                $quickViewImage.attr('src', src);
                $quickViewBox.css({
                    'top': (event.pageY + 20) + 'px',
                    'left': (event.pageX + 20) + 'px'
                }).show();
            });
            $('.table').on('mouseleave', '.quick-view-trigger', function() {
                $quickViewBox.hide();
                $quickViewImage.attr('src', '');
            });
            $('.table').on('mousemove', '.quick-view-trigger', function(event) {
                $quickViewBox.css({
                    'top': (event.pageY + 20) + 'px',
                    'left': (event.pageX + 20) + 'px'
                });
            });

            {{-- --- Inicializador para el Filtro de Rango de Fechas --- --}}
            $('#dateRangePicker').daterangepicker({
                opens: 'left',
                locale: {
                    "format": "MM/DD/YYYY",
                    "separator": " - ",
                    "applyLabel": "Aplicar",
                    "cancelLabel": "Limpiar",
                    "fromLabel": "Desde",
                    "toLabel": "Hasta",
                    "customRangeLabel": "Personalizado",
                    "weekLabel": "S",
                    "daysOfWeek": ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sá"],
                    "monthNames": [
                        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", 
                        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
                    ],
                    "firstDay": 1
                },
                ranges: {
                'Hoy': [moment(), moment()],
                'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Últimos 7 Días': [moment().subtract(6, 'days'), moment()],
                'Últimos 30 Días': [moment().subtract(29, 'days'), moment()],
                'Este Mes': [moment().startOf('month'), moment().endOf('month')],
                'Mes Pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            if( ! '{{ $dateRange ?? '' }}' ) {
                $('#dateRangePicker').val('');
            }
            $('#dateRangePicker').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

        }); // Fin de document.ready
    </script>
@endpush