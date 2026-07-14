@extends('layouts.app')
@section('content')

<style nonce="{{ request()->attributes->get('csp_nonce') }}">
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 0 !important;
        margin: 0 3px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        border-radius: 0 !important;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Disponibilidad — Bloqueos de Canchas</h4>
                <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
                    <i class="fa fa-plus me-1"></i> Nuevo Bloqueo
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaBloqueos" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Cancha</th>
                                <th>Fecha</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                                <th>Motivo</th>
                                <th>Descripción</th>
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

{{-- Modal Bloqueo --}}
<div class="modal fade" id="modalBloqueo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formBloqueo" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModal">Nuevo Bloqueo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="bloqueoId">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Cancha <span class="text-danger">*</span></label>
                        <select class="form-control" id="id_cancha" name="id_cancha" required>
                            <option value="">-- Selecciona --</option>
                            @foreach($canchas as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="fecha" name="fecha" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Hora inicio <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Hora fin <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Motivo <span class="text-danger">*</span></label>
                        <select class="form-control" id="motivo" name="motivo" required>
                            <option value="mantenimiento">Mantenimiento</option>
                            <option value="evento_especial">Evento especial</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="2" maxlength="255"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
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

    let modalBloqueo = new bootstrap.Modal(document.getElementById('modalBloqueo'));

    const motivoLabel = { mantenimiento: 'Mantenimiento', evento_especial: 'Evento especial', otro: 'Otro' };

    let TablaBloqueos = $('#tablaBloqueos').DataTable({
        processing: true,
        serverSide: false,
        responsive: true,
        destroy: true,
        pagingType: "simple_numbers",
        language: { url: "{{ asset('/vendor/datatables/js/Spanish.json') }}" },
        ajax: {
            url: "{{ route('admin.bloqueos.lista') }}",
            type: "GET",
            dataSrc: "data"
        },
        columns: [
            { data: "cancha" },
            { data: "fecha" },
            { data: "hora_inicio" },
            { data: "hora_fin" },
            { data: "motivo", render: d => motivoLabel[d] || d },
            { data: "descripcion" },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <div class="d-flex">
                            <button class="btn btn-primary shadow btn-xs sharp me-1 btnEdit" data-id="${row.id}">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button class="btn btn-danger shadow btn-xs sharp btnEliminar" data-id="${row.id}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>`;
                }
            }
        ]
    });

    // Nuevo
    $('#btnNuevo').on('click', function () {
        $('#formBloqueo')[0].reset();
        $('#bloqueoId').val('');
        $('#tituloModal').text('Nuevo Bloqueo');
        modalBloqueo.show();
    });

    // Editar
    $('#tablaBloqueos tbody').on('click', '.btnEdit', function () {
        let id = $(this).data('id');
        GS.inicioSolicitud();
        $.getJSON("{{ url('admin/bloqueos/obtener') }}/" + id, function (resp) {
            GS.finSolicitud();
            let b = resp.data;
            $('#bloqueoId').val(b.id);
            $('#id_cancha').val(b.id_cancha);
            $('#fecha').val(b.fecha);
            $('#hora_inicio').val(b.hora_inicio);
            $('#hora_fin').val(b.hora_fin);
            $('#motivo').val(b.motivo);
            $('#descripcion').val(b.descripcion !== '-' ? b.descripcion : '');
            $('#tituloModal').text('Editar Bloqueo');
            modalBloqueo.show();
        }).fail(function () {
            GS.finSolicitud();
            GS.toastError('No se pudo obtener el bloqueo.');
        });
    });

    // Guardar / Actualizar
    $('#formBloqueo').on('submit', function (e) {
        e.preventDefault();
        let id  = $('#bloqueoId').val();
        let url = id
            ? "{{ url('admin/bloqueos/actualizar') }}/" + id
            : "{{ route('admin.bloqueos.guardar') }}";
        let method = id ? 'PUT' : 'POST';

        GS.inicioSolicitud();
        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            dataType: "json"
        })
        .done(function (resp) {
            GS.finSolicitud();
            if (resp.status === 200) {
                modalBloqueo.hide();
                GS.toastSuccess(resp.message);
                TablaBloqueos.ajax.reload(null, false);
            } else {
                GS.toastError(resp.message);
            }
        })
        .fail(function (xhr) {
            GS.finSolicitud();
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                GS.toastError(Object.values(xhr.responseJSON.errors)[0][0]);
            } else {
                GS.toastError('Error al guardar el bloqueo.');
            }
        });
    });

    // Eliminar
    $('#tablaBloqueos tbody').on('click', '.btnEliminar', function () {
        let id = $(this).data('id');
        GS.modalConfirmacion('¿Eliminar bloqueo?', 'Esta acción no se puede revertir.', function () {
            GS.inicioSolicitud();
            $.ajax({
                url: "{{ url('admin/bloqueos/eliminar') }}/" + id,
                type: "DELETE",
                dataType: "json"
            })
            .done(function (resp) {
                GS.finSolicitud();
                if (resp.status === 200) {
                    GS.toastSuccess(resp.message);
                    TablaBloqueos.ajax.reload(null, false);
                } else {
                    GS.toastError(resp.message);
                }
            })
            .fail(function () {
                GS.finSolicitud();
                GS.toastError('Error al eliminar el bloqueo.');
            });
        });
    });

});
</script>
@endsection
