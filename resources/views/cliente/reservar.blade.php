@extends('layouts.app')
@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- Paso 1: Seleccionar cancha y fecha --}}
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Reservar Cancha</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Complejo <span class="text-danger">*</span></label>
                        <select class="form-control" id="id_complejo">
                            <option value="">-- Selecciona --</option>
                            @foreach($complejos as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Cancha <span class="text-danger">*</span></label>
                        <select class="form-control" id="id_cancha" disabled>
                            <option value="">-- Selecciona --</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Fecha <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="fecha_reserva" min="{{ date('Y-m-d') }}" disabled>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" id="btnVerSlots" disabled>
                    <i class="fa fa-search me-1"></i> Ver disponibilidad
                </button>
            </div>
        </div>

        {{-- Slots disponibles --}}
        <div id="seccionSlots" style="display:none;">
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Horarios disponibles</h5>
                </div>
                <div class="card-body">
                    <div id="contenedorSlots" class="row g-2"></div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal Confirmar Reserva --}}
<div class="modal fade" id="modalReservar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formReservar" enctype="multipart/form-data" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <input type="hidden" name="id_cancha" id="r_id_cancha">
                    <input type="hidden" name="fecha_reserva" id="r_fecha">
                    <input type="hidden" name="hora_inicio" id="r_hora_inicio">
                    <input type="hidden" name="hora_fin" id="r_hora_fin">

                    <div class="alert alert-info py-2 mb-3" id="resumenReserva"></div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Método de pago <span class="text-danger">*</span></label>
                        <select class="form-control" name="id_metodo_pago" id="r_metodo_pago" required>
                            <option value="">-- Selecciona --</option>
                            @foreach($metodosPago as $m)
                                <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Código de operación <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="codigo_operacion" maxlength="100" required placeholder="Ej: 123456789">
                        <small class="text-muted">Número de operación Yape, Plin o código de transacción</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Captura del pago (opcional)</label>
                        <input type="file" class="form-control" name="comprobante" accept="image/*">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-check me-1"></i> Confirmar Reserva
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
$(document).ready(function () {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    let modalReservar = new bootstrap.Modal(document.getElementById('modalReservar'));
    let precioHora = 0, totalSlot = 0;

    // Cargar canchas al seleccionar complejo
    $('#id_complejo').on('change', function () {
        let idComplejo = $(this).val();
        $('#id_cancha').prop('disabled', true).html('<option value="">-- Selecciona --</option>');
        $('#fecha_reserva').prop('disabled', true).val('');
        $('#btnVerSlots').prop('disabled', true);
        $('#seccionSlots').hide();

        if (!idComplejo) return;

        $.getJSON("{{ url('cliente/canchas-por-complejo') }}/" + idComplejo, function (resp) {
            if (resp.data && resp.data.length) {
                resp.data.forEach(c => $('#id_cancha').append(`<option value="${c.id}" data-precio="${c.precio_hora}">${c.nombre} — S/ ${c.precio_hora}/hr</option>`));
                $('#id_cancha').prop('disabled', false);
            } else {
                GS.toastError('Este complejo no tiene canchas disponibles.');
            }
        });
    });

    $('#id_cancha').on('change', function () {
        $('#fecha_reserva').prop('disabled', !$(this).val()).val('');
        $('#btnVerSlots').prop('disabled', true);
        $('#seccionSlots').hide();
    });

    $('#fecha_reserva').on('change', function () {
        $('#btnVerSlots').prop('disabled', !$(this).val());
        $('#seccionSlots').hide();
    });

    // Ver disponibilidad
    $('#btnVerSlots').on('click', function () {
        let idCancha = $('#id_cancha').val();
        let fecha    = $('#fecha_reserva').val();

        GS.inicioSolicitud();
        $.getJSON("{{ url('cliente/slots') }}/" + idCancha + '/' + fecha, function (resp) {
            GS.finSolicitud();
            let slots = resp.data;
            let html  = '';

            if (!slots || slots.length === 0) {
                html = '<div class="col-12"><p class="text-muted text-center py-3">No hay horarios disponibles para esta fecha.</p></div>';
            } else {
                slots.forEach(s => {
                    html += `
                        <div class="col-md-4 col-sm-6">
                            <div class="card border text-center slot-card" style="cursor:pointer;"
                                data-inicio="${s.hora_inicio}" data-fin="${s.hora_fin}"
                                data-precio="${s.precio_hora}" data-total="${s.total}">
                                <div class="card-body py-3">
                                    <p class="fw-bold mb-1 fs-5">${s.hora_inicio} – ${s.hora_fin}</p>
                                    <p class="text-success mb-0 fw-bold">S/ ${parseFloat(s.total).toFixed(2)}</p>
                                </div>
                            </div>
                        </div>`;
                });
            }

            $('#contenedorSlots').html(html);
            $('#seccionSlots').show();
        }).fail(function () {
            GS.finSolicitud();
            GS.toastError('Error al consultar disponibilidad.');
        });
    });

    // Seleccionar slot → abrir modal
    $('#contenedorSlots').on('click', '.slot-card', function () {
        let inicio = $(this).data('inicio');
        let fin    = $(this).data('fin');
        precioHora = $(this).data('precio');
        totalSlot  = $(this).data('total');

        let cancha = $('#id_cancha option:selected').text();
        let fecha  = $('#fecha_reserva').val();

        $('#r_id_cancha').val($('#id_cancha').val());
        $('#r_fecha').val(fecha);
        $('#r_hora_inicio').val(inicio);
        $('#r_hora_fin').val(fin);

        $('#resumenReserva').html(`
            <strong>${cancha}</strong><br>
            Fecha: <strong>${fecha}</strong> &nbsp;|&nbsp;
            Horario: <strong>${inicio} – ${fin}</strong><br>
            Total a pagar: <strong class="text-success">S/ ${parseFloat(totalSlot).toFixed(2)}</strong>
        `);

        $('#formReservar')[0].reset();
        $('#r_id_cancha').val($('#id_cancha').val());
        $('#r_fecha').val(fecha);
        $('#r_hora_inicio').val(inicio);
        $('#r_hora_fin').val(fin);

        modalReservar.show();
    });

    // Confirmar reserva
    $('#formReservar').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        GS.inicioSolicitud();
        $.ajax({
            url: "{{ route('cliente.confirmar') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json"
        })
        .done(function (resp) {
            GS.finSolicitud();
            if (resp.status === 200) {
                modalReservar.hide();
                GS.toastSuccess(resp.message);
                // Recargar slots para reflejar la reserva
                $('#btnVerSlots').trigger('click');
            } else {
                GS.toastError(resp.message);
            }
        })
        .fail(function (xhr) {
            GS.finSolicitud();
            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                GS.toastError(Object.values(xhr.responseJSON.errors)[0][0]);
            } else {
                GS.toastError('Error al confirmar la reserva.');
            }
        });
    });

});
</script>
@endsection
