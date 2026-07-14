@extends('layouts.app')
@section('content')

<style nonce="{{ request()->attributes->get('csp_nonce') }}">
    .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius:0!important; margin:0 3px; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { border-radius:0!important; }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Reservas</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaReservas" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Cliente</th>
                                <th>Cancha</th>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Total</th>
                                <th>Pago</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Ver Detalle --}}
<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cuerpoDetalle"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning" id="btnAbrirReprogramar" style="display:none;">
                    <i class="fa fa-calendar me-1"></i> Reprogramar Reserva
                </button>
                <button type="button" class="btn btn-danger" id="btnAbrirCancelar" style="display:none;">
                    <i class="fa fa-times me-1"></i> Cancelar Reserva
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Cancelar --}}
<div class="modal fade" id="modalCancelar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formCancelar" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title">Cancelar Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="cancelarId">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Motivo de cancelación <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="motivo_cancelacion" name="motivo_cancelacion" rows="2" maxlength="255" required></textarea>
                    </div>

                    <hr>
                    <p class="fw-bold mb-2">Datos del reembolso</p>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Método <span class="text-danger">*</span></label>
                            <select class="form-control" id="metodo_reembolso" name="metodo_reembolso" required>
                                <option value="yape">Yape</option>
                                <option value="plin">Plin</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="efectivo">Efectivo</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Monto reembolsado (S/) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="monto_reembolso" name="monto_reembolso" min="0" step="0.01" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Código de operación</label>
                        <input type="text" class="form-control" id="codigo_reembolso" name="codigo_reembolso" maxlength="100">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Observación</label>
                        <textarea class="form-control" id="observacion_reembolso" name="observacion_reembolso" rows="2" maxlength="255"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Volver</button>
                    <button type="submit" class="btn btn-danger">Confirmar cancelación</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Reprogramar --}}
<div class="modal fade" id="modalReprogramar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formReprogramar" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title">Reprogramar Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="reprogramarId">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nueva fecha <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="reprogramar_fecha" name="fecha" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Duración <span class="text-danger">*</span></label>
                            <select class="form-control" id="reprogramar_duracion" name="duracion" required>
                                @foreach($duraciones as $d)
                                    <option value="{{ $d }}">{{ $d }} min</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nuevo horario <span class="text-danger">*</span></label>
                        <select class="form-control" id="reprogramar_horario" name="horario" required>
                            <option value="">Selecciona una fecha primero</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Motivo de reprogramación <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="motivo_reprogramacion" name="motivo_reprogramacion" rows="2" maxlength="255" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Volver</button>
                    <button type="submit" class="btn btn-warning">Confirmar reprogramación</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script nonce="{{ request()->attributes->get('csp_nonce') }}">
$(document).ready(function () {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    let modalDetalle     = new bootstrap.Modal(document.getElementById('modalDetalle'));
    let modalCancelar    = new bootstrap.Modal(document.getElementById('modalCancelar'));
    let modalReprogramar = new bootstrap.Modal(document.getElementById('modalReprogramar'));

    const estadoBadge = {
        'Confirmada': 'badge-success',
        'Completada': 'badge-primary',
        'Cancelada':  'badge-danger',
    };

    let TablaReservas = $('#tablaReservas').DataTable({
        processing: true,
        serverSide: false,
        responsive: true,
        destroy: true,
        pagingType: "simple_numbers",
        language: { url: "{{ asset('/vendor/datatables/js/Spanish.json') }}" },
        ajax: {
            url: "{{ route('admin.reservas.lista') }}",
            type: "GET",
            dataSrc: "data"
        },
        columns: [
            { data: "codigo" },
            { data: "cliente" },
            { data: "cancha" },
            { data: "fecha" },
            { data: "hora_inicio", render: (d, t, r) => `${r.hora_inicio} - ${r.hora_fin}` },
            { data: "total", render: d => `S/ ${d}` },
            { data: "metodo_pago" },
            { data: "estado", render: d => `<span class="badge ${estadoBadge[d] || 'badge-secondary'}">${d}</span>` },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: (data, type, row) => `
                    <button class="btn btn-info shadow btn-xs sharp btnVer" data-id="${row.id}">
                        <i class="fa fa-eye"></i>
                    </button>`
            }
        ]
    });

    // Ver detalle
    $('#tablaReservas tbody').on('click', '.btnVer', function () {
        let id = $(this).data('id');
        GS.inicioSolicitud();
        $.getJSON("{{ url('admin/reservas/obtener') }}/" + id, function (resp) {
            GS.finSolicitud();
            let r = resp.data;
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Código:</strong> ${r.codigo}</p>
                        <p><strong>Cliente:</strong> ${r.cliente}</p>
                        <p><strong>Email:</strong> ${r.email}</p>
                        <p><strong>Cancha:</strong> ${r.cancha}</p>
                        <p><strong>Complejo:</strong> ${r.complejo}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Fecha:</strong> ${r.fecha}</p>
                        <p><strong>Horario:</strong> ${r.hora_inicio} - ${r.hora_fin}</p>
                        <p><strong>Precio/hora:</strong> S/ ${r.precio_hora}</p>
                        <p><strong>Total:</strong> S/ ${r.total}</p>
                        <p><strong>Método pago:</strong> ${r.metodo_pago}</p>
                        <p><strong>Código op.:</strong> ${r.codigo_operacion || '-'}</p>
                        <p><strong>Estado:</strong> ${r.estado}</p>
                        <p><strong>Confirmado:</strong> ${r.confirmado_at}</p>
                        ${r.cancelado_at ? `<p><strong>Cancelado:</strong> ${r.cancelado_at}</p><p><strong>Motivo:</strong> ${r.motivo_cancelacion}</p>` : ''}
                    </div>
                </div>
                ${r.comprobante_url ? `<hr><p><strong>Comprobante:</strong><br><img src="/${r.comprobante_url}" class="img-fluid rounded border" style="max-height:200px;"></p>` : ''}
                ${r.telefono_complejo ? `<hr><a href="https://wa.me/51${r.telefono_complejo.replace(/\D/g,'')}" target="_blank" class="btn btn-success btn-sm"><i class="fab fa-whatsapp me-1"></i>WhatsApp del complejo</a>` : ''}
            `;
            $('#cuerpoDetalle').html(html);
            $('#cancelarId').val(r.id);
            $('#monto_reembolso').val(r.total);
            $('#reprogramarId').val(r.id);

            if (r.estado === 'Confirmada') {
                $('#btnAbrirCancelar').show();
                $('#btnAbrirReprogramar').show();
            } else {
                $('#btnAbrirCancelar').hide();
                $('#btnAbrirReprogramar').hide();
            }

            modalDetalle.show();
        }).fail(function () {
            GS.finSolicitud();
            GS.toastError('No se pudo obtener la reserva.');
        });
    });

    // Abrir modal cancelar desde detalle
    $('#btnAbrirCancelar').on('click', function () {
        modalDetalle.hide();
        $('#formCancelar')[0].reset();
        $('#monto_reembolso').val($(this).data('total') || '');
        modalCancelar.show();
    });

    // Confirmar cancelación
    $('#formCancelar').on('submit', function (e) {
        e.preventDefault();
        let id = $('#cancelarId').val();

        GS.inicioSolicitud();
        $.ajax({
            url: "{{ url('admin/reservas/cancelar') }}/" + id,
            type: "POST",
            data: $(this).serialize() + '&_method=PUT',
            dataType: "json"
        })
        .done(function (resp) {
            GS.finSolicitud();
            if (resp.status === 200) {
                modalCancelar.hide();
                GS.toastSuccess(resp.message);
                TablaReservas.ajax.reload(null, false);
            } else {
                GS.toastError(resp.message);
            }
        })
        .fail(function (xhr) {
            GS.finSolicitud();
            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                GS.toastError(Object.values(xhr.responseJSON.errors)[0][0]);
            } else {
                GS.toastError('Error al cancelar la reserva.');
            }
        });
    });

    // Abrir modal reprogramar desde detalle
    $('#btnAbrirReprogramar').on('click', function () {
        modalDetalle.hide();
        $('#formReprogramar')[0].reset();
        $('#reprogramar_fecha').attr('min', new Date().toISOString().slice(0, 10));
        $('#reprogramar_horario').html('<option value="">Selecciona una fecha primero</option>');
        modalReprogramar.show();
    });

    function cargarHorariosReprogramar() {
        let id = $('#reprogramarId').val();
        let fecha = $('#reprogramar_fecha').val();
        let duracion = $('#reprogramar_duracion').val();
        let $horario = $('#reprogramar_horario');

        if (!id || !fecha || !duracion) return;

        $horario.html('<option value="">Cargando...</option>');

        $.getJSON("{{ url('admin/reservas/slots-reprogramar') }}/" + id, { fecha: fecha, duracion: duracion }, function (resp) {
            if (resp.status === 200 && resp.data.length) {
                let options = resp.data.map(s => `<option value="${s.hora_inicio}|${s.hora_fin}">${s.hora_inicio} - ${s.hora_fin}</option>`).join('');
                $horario.html(options);
            } else {
                $horario.html('<option value="">Sin horarios disponibles para esa fecha</option>');
            }
        }).fail(function () {
            $horario.html('<option value="">Error al cargar horarios</option>');
        });
    }

    $('#reprogramar_fecha, #reprogramar_duracion').on('change', cargarHorariosReprogramar);

    // Confirmar reprogramación
    $('#formReprogramar').on('submit', function (e) {
        e.preventDefault();
        let id = $('#reprogramarId').val();
        let horario = $('#reprogramar_horario').val();

        if (!horario) {
            GS.toastError('Selecciona un horario disponible.');
            return;
        }

        let [hora_inicio, hora_fin] = horario.split('|');

        GS.inicioSolicitud();
        $.ajax({
            url: "{{ url('admin/reservas/reprogramar') }}/" + id,
            type: "POST",
            data: {
                fecha: $('#reprogramar_fecha').val(),
                hora_inicio: hora_inicio,
                hora_fin: hora_fin,
                motivo_reprogramacion: $('#motivo_reprogramacion').val(),
                _method: 'PUT'
            },
            dataType: "json"
        })
        .done(function (resp) {
            GS.finSolicitud();
            if (resp.status === 200) {
                modalReprogramar.hide();
                GS.toastSuccess(resp.message);
                TablaReservas.ajax.reload(null, false);
            } else {
                GS.toastError(resp.message);
            }
        })
        .fail(function (xhr) {
            GS.finSolicitud();
            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                GS.toastError(Object.values(xhr.responseJSON.errors)[0][0]);
            } else {
                GS.toastError('Error al reprogramar la reserva.');
            }
        });
    });

});
</script>
@endsection
