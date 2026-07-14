@extends('layouts.app')
@section('content')

<style nonce="{{ request()->attributes->get('csp_nonce') }}">
    .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius:0!important; margin:0 3px; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { border-radius:0!important; }
</style>

<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Reporte de Reservas</h4>
            </div>
            <div class="card-body">
                <form id="formFiltro" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Desde</label>
                        <input type="date" class="form-control" id="fecha_desde" name="fecha_desde">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Hasta</label>
                        <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta">
                    </div>
                    @if($complejos->isNotEmpty())
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Complejo</label>
                        <select class="form-control" id="id_complejo" name="id_complejo">
                            <option value="">Todos</option>
                            @foreach($complejos as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fa fa-filter me-1"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-success flex-fill" id="btnExportar">
                            <i class="fa fa-file-excel-o me-1"></i> Exportar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaReservasReporte" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Cliente</th>
                                <th>Complejo</th>
                                <th>Cancha</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script nonce="{{ request()->attributes->get('csp_nonce') }}">
$(document).ready(function () {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    const hoy = new Date().toISOString().slice(0, 10);
    const inicioMes = hoy.slice(0, 8) + '01';
    $('#fecha_desde').val(inicioMes);
    $('#fecha_hasta').val(hoy);

    const estadoBadge = {
        'Confirmada': 'badge-success',
        'Completada': 'badge-primary',
        'Cancelada':  'badge-danger',
    };

    function filtros() {
        return {
            fecha_desde: $('#fecha_desde').val(),
            fecha_hasta: $('#fecha_hasta').val(),
            id_complejo: $('#id_complejo').val() || ''
        };
    }

    let TablaReservas = $('#tablaReservasReporte').DataTable({
        processing: true,
        serverSide: false,
        responsive: true,
        destroy: true,
        pagingType: "simple_numbers",
        language: { url: "{{ asset('/vendor/datatables/js/Spanish.json') }}" },
        ajax: {
            url: "{{ route('admin.reportes.reservas.lista') }}",
            type: "GET",
            data: filtros,
            dataSrc: "data"
        },
        columns: [
            { data: "codigo" },
            { data: "cliente" },
            { data: "complejo" },
            { data: "cancha" },
            { data: "fecha" },
            { data: "total", render: d => `S/ ${d}` },
            { data: "estado", render: d => `<span class="badge ${estadoBadge[d] || 'badge-secondary'}">${d}</span>` },
        ]
    });

    $('#formFiltro').on('submit', function (e) {
        e.preventDefault();
        TablaReservas.ajax.reload();
    });

    $('#btnExportar').on('click', function () {
        window.location.href = "{{ route('admin.reportes.reservas.exportar') }}?" + $.param(filtros());
    });

});
</script>
@endsection
