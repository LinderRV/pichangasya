@extends('layouts.app')
@section('content')

<style nonce="{{ request()->attributes->get('csp_nonce') }}">
    .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius:0!important; margin:0 3px; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { border-radius:0!important; }
</style>

@if(session('success'))
<div class="alert alert-success alert-dismissible mb-3" role="alert">
    <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Mis Reservas</h4>
                <a href="{{ route('web.paginas.canchas') }}" class="btn btn-success btn-sm">
                    <i class="fa fa-plus me-1"></i> Nueva Reserva
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaMisReservas" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Cancha</th>
                                <th>Complejo</th>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Total</th>
                                <th>Pago</th>
                                <th>Estado</th>
                                <th>Contacto</th>
                                <th>Comprobante</th>
                                <th>Detalle</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="alert alert-warning mt-3 mb-0">
                    <i class="fa fa-info-circle me-2"></i>
                    Para cancelar o reprogramar una reserva, contacta al dueño del complejo directamente por WhatsApp.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalleReserva" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Detalle de reserva</h5>
                    <div class="text-muted small" id="detalleCodigo"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6"><strong>Cancha</strong><div id="detalleCancha"></div></div>
                    <div class="col-12 col-md-6"><strong>Complejo</strong><div id="detalleComplejo"></div></div>
                    <div class="col-12 col-md-6"><strong>Fecha y horario</strong><div id="detalleFecha"></div></div>
                    <div class="col-12 col-md-3"><strong>Total</strong><div id="detalleTotal"></div></div>
                    <div class="col-12 col-md-3"><strong>Estado</strong><div id="detalleEstado"></div></div>
                    <div class="col-12"><strong>Dirección</strong><div id="detalleDireccion"></div></div>
                </div>

                <div class="card bg-light border-0 mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold">Pago</h6>
                        <div id="detallePago" class="text-muted">Sin pago registrado.</div>
                    </div>
                </div>

                <div class="card bg-warning-subtle border-0 mb-3 d-none" id="detalleReembolsoCard">
                    <div class="card-body">
                        <h6 class="fw-bold">Reembolso registrado</h6>
                        <div id="detalleReembolso"></div>
                    </div>
                </div>

                <div class="alert alert-danger d-none" id="detalleCancelacion"></div>

                <h6 class="fw-bold mt-4">Historial de estados</h6>
                <div id="detalleHistorial" class="list-group list-group-flush"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script nonce="{{ request()->attributes->get('csp_nonce') }}">
$(document).ready(function () {

    const estadoBadge = {
        'Confirmada': 'badge-success',
        'Completada': 'badge-primary',
        'Cancelada':  'badge-danger',
    };

    $('#tablaMisReservas').DataTable({
        processing: true,
        serverSide: false,
        responsive: true,
        destroy: true,
        pagingType: "simple_numbers",
        language: { url: "{{ asset('/vendor/datatables/js/Spanish.json') }}" },
        ajax: {
            url: "{{ route('cliente.reservas.lista') }}",
            type: "GET",
            dataSrc: "data"
        },
        columns: [
            { data: "codigo" },
            { data: "cancha" },
            { data: "complejo" },
            { data: "fecha" },
            { data: "hora_inicio", render: (d, t, r) => `${r.hora_inicio} – ${r.hora_fin}` },
            { data: "total" },
            { data: "metodo_pago" },
            { data: "estado", render: d => `<span class="badge ${estadoBadge[d] || 'badge-secondary'}">${d}</span>` },
            {
                data: "telefono_complejo",
                orderable: false,
                searchable: false,
                render: function (d) {
                    if (!d) return '-';
                    let num = d.replace(/\D/g,'');
                    return `<a href="https://wa.me/51${num}" target="_blank" class="btn btn-success btn-xs shadow">
                                <i class="fab fa-whatsapp"></i>
                            </a>`;
                }
            },
            {
                data: "id",
                orderable: false,
                searchable: false,
                render: function (id, type, row) {
                    if (!row.tiene_pago) return '-';
                    return `<a href="{{ url('cliente/reservas') }}/${id}/comprobante" target="_blank" class="btn btn-success btn-xs shadow" title="Comprobante de pago">
                                <i class="fa fa-file-pdf"></i>
                            </a>`;
                }
            },
            {
                data: "id",
                orderable: false,
                searchable: false,
                render: id => `<button type="button" class="btn btn-primary btn-xs shadow btnDetalle" data-id="${id}" title="Ver detalle"><i class="fa fa-eye"></i></button>`
            }
        ]
    });

    $('#tablaMisReservas tbody').on('click', '.btnDetalle', function () {
        const id = $(this).data('id');

        fetch(`{{ url('cliente/reservas') }}/${id}`, { headers: { 'Accept': 'application/json' } })
            .then(response => {
                if (!response.ok) throw new Error('No se pudo consultar la reserva.');
                return response.json();
            })
            .then(response => {
                const reserva = response.data;
                $('#detalleCodigo').text(reserva.codigo);
                $('#detalleCancha').text(reserva.cancha);
                $('#detalleComplejo').text(reserva.complejo);
                $('#detalleFecha').text(`${reserva.fecha} · ${reserva.horario}`);
                $('#detalleTotal').text(reserva.total);
                $('#detalleEstado').text(reserva.estado);
                $('#detalleDireccion').text(reserva.direccion || '-');

                $('#detallePago').text(reserva.pago
                    ? `${reserva.pago.metodo} · ${reserva.pago.estado} · ${reserva.pago.monto} · ${reserva.pago.fecha || ''}`
                    : 'Sin pago registrado.');

                $('#detalleCancelacion')
                    .toggleClass('d-none', !reserva.motivo_cancelacion)
                    .text(reserva.motivo_cancelacion ? `Motivo de cancelación: ${reserva.motivo_cancelacion}` : '');

                $('#detalleReembolsoCard').toggleClass('d-none', !reserva.reembolso);
                $('#detalleReembolso').text(reserva.reembolso
                    ? `${reserva.reembolso.metodo} · ${reserva.reembolso.monto} · ${reserva.reembolso.fecha || ''}${reserva.reembolso.codigo ? ' · Ref. ' + reserva.reembolso.codigo : ''}`
                    : '');

                const historial = $('#detalleHistorial').empty();
                if (!reserva.historial.length) {
                    historial.append($('<div class="text-muted small py-2"></div>').text('Sin cambios registrados.'));
                } else {
                    reserva.historial.forEach(item => {
                        const row = $('<div class="list-group-item px-0 d-flex justify-content-between gap-3"></div>');
                        row.append($('<span class="fw-semibold"></span>').text(item.estado));
                        row.append($('<span class="text-muted small"></span>').text(item.fecha || ''));
                        historial.append(row);
                    });
                }

                new bootstrap.Modal(document.getElementById('modalDetalleReserva')).show();
            })
            .catch(() => toastr.error('No se pudo cargar el detalle de la reserva.'));
    });

});
</script>
@endsection
