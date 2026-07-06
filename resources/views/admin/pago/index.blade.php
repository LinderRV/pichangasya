@extends('layouts.app')
@section('content')

<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius:0!important; margin:0 3px; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { border-radius:0!important; }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Historial de Pagos</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaPagos" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Reserva</th>
                                <th>Cliente</th>
                                <th>Complejo</th>
                                <th>Cancha</th>
                                <th>Monto</th>
                                <th>Método</th>
                                <th>Estado</th>
                                <th>Fecha de pago</th>
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
<div class="modal fade" id="modalDetallePago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cuerpoDetallePago"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
$(document).ready(function () {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    let modalDetalle = new bootstrap.Modal(document.getElementById('modalDetallePago'));

    const estadoBadge = {
        'confirmado':   'badge-success',
        'reembolsado':  'badge-warning',
        'anulado':      'badge-danger',
    };

    let TablaPagos = $('#tablaPagos').DataTable({
        processing: true,
        serverSide: false,
        responsive: true,
        destroy: true,
        pagingType: "simple_numbers",
        language: { url: "{{ asset('/vendor/datatables/js/Spanish.json') }}" },
        ajax: {
            url: "{{ route('admin.pagos.lista') }}",
            type: "GET",
            dataSrc: "data"
        },
        columns: [
            { data: "codigo_reserva" },
            { data: "cliente" },
            { data: "complejo" },
            { data: "cancha" },
            { data: "monto", render: d => `S/ ${d}` },
            { data: "metodo_pago" },
            { data: "estado", render: d => `<span class="badge ${estadoBadge[d] || 'badge-secondary'}">${d}</span>` },
            { data: "fecha_pago" },
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

    $('#tablaPagos tbody').on('click', '.btnVer', function () {
        let id = $(this).data('id');
        GS.inicioSolicitud();
        $.getJSON("{{ url('admin/pagos/obtener') }}/" + id, function (resp) {
            GS.finSolicitud();
            let p = resp.data;
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Reserva:</strong> ${p.codigo_reserva ?? '-'}</p>
                        <p><strong>Cliente:</strong> ${p.cliente}</p>
                        <p><strong>Email:</strong> ${p.email ?? '-'}</p>
                        <p><strong>Complejo:</strong> ${p.complejo ?? '-'}</p>
                        <p><strong>Cancha:</strong> ${p.cancha ?? '-'}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Código operación:</strong> ${p.codigo_operacion}</p>
                        <p><strong>Monto:</strong> S/ ${p.monto}</p>
                        <p><strong>Método de pago:</strong> ${p.metodo_pago}</p>
                        <p><strong>Estado:</strong> ${p.estado}</p>
                        <p><strong>Fecha de pago:</strong> ${p.fecha_pago}</p>
                    </div>
                </div>
                ${p.comprobante_url ? `<hr><p><strong>Comprobante:</strong><br><img src="/${p.comprobante_url}" class="img-fluid rounded border" style="max-height:200px;"></p>` : ''}
            `;
            $('#cuerpoDetallePago').html(html);
            modalDetalle.show();
        }).fail(function () {
            GS.finSolicitud();
            GS.toastError('No se pudo obtener el pago.');
        });
    });

});
</script>
@endsection
