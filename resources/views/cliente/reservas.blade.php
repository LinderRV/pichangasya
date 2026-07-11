@extends('layouts.app')
@section('content')

<style>
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

@endsection

@section('script')
<script>
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
                                <i class="fa fa-whatsapp"></i>
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
            }
        ]
    });

});
</script>
@endsection
