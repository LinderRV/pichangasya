@extends('layouts.app')
@section('content')

<style>
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
                <h4 class="card-title">Canchas</h4>
                <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
                    <i class="fa fa-plus me-1"></i> Nueva Cancha
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaCanchas" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Complejo</th>
                                <th>Precio/hora</th>
                                <th>Capacidad</th>
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

{{-- Modal Cancha --}}
<div class="modal fade" id="modalCancha" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formCancha" enctype="multipart/form-data" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModal">Nueva Cancha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="canchaId" name="id">

                    @if(Auth::user()->esSuperAdmin())
                    <div class="mb-3">
                        <label class="form-label fw-bold">Complejo <span class="text-danger">*</span></label>
                        <select class="form-control" id="id_complejo" name="id_complejo" required>
                            <option value="">-- Selecciona --</option>
                            @foreach($complejos as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tipo de Cancha <span class="text-danger">*</span></label>
                            <select class="form-control" id="id_tipo_cancha" name="id_tipo_cancha" required>
                                <option value="">-- Selecciona --</option>
                                @foreach($tipoCanchas as $t)
                                    <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Precio por hora (S/) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="precio_hora" name="precio_hora" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Capacidad</label>
                            <input type="number" class="form-control" id="capacidad" name="capacidad" min="1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                            <select class="form-control" id="estado" name="estado" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                                <option value="mantenimiento">Mantenimiento</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="2" maxlength="255"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Foto</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                        <div class="mt-2">
                            <img id="previewFoto" src="" alt="" style="max-height:90px; display:none;" class="rounded border">
                        </div>
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
<script>
$(document).ready(function () {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    let modalCancha = new bootstrap.Modal(document.getElementById('modalCancha'));

    let TablaCanchas = $('#tablaCanchas').DataTable({
        processing: true,
        serverSide: false,
        responsive: true,
        destroy: true,
        pagingType: "simple_numbers",
        language: { url: "{{ asset('/vendor/datatables/js/Spanish.json') }}" },
        ajax: {
            url: "{{ route('admin.canchas.lista') }}",
            type: "GET",
            dataSrc: "data"
        },
        columns: [
            { data: "nombre" },
            { data: "tipo" },
            { data: "complejo" },
            { data: "precio_hora", render: d => `S/ ${d}` },
            { data: "capacidad" },
            {
                data: "estado",
                render: function (d) {
                    const clases = { activo: 'badge-success', inactivo: 'badge-danger', mantenimiento: 'badge-warning' };
                    return `<span class="badge ${clases[d] || 'badge-secondary'}">${d}</span>`;
                }
            },
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

    // Preview foto
    $('#foto').on('change', function () {
        let file = this.files[0];
        if (file) {
            $('#previewFoto').attr('src', URL.createObjectURL(file)).show();
        } else {
            $('#previewFoto').hide();
        }
    });

    // Nuevo
    $('#btnNuevo').on('click', function () {
        $('#formCancha')[0].reset();
        $('#canchaId').val('');
        $('#previewFoto').hide();
        $('#tituloModal').text('Nueva Cancha');
        modalCancha.show();
    });

    // Editar
    $('#tablaCanchas tbody').on('click', '.btnEdit', function () {
        let id = $(this).data('id');
        GS.inicioSolicitud();
        $.getJSON("{{ url('admin/canchas/obtener') }}/" + id, function (resp) {
            GS.finSolicitud();
            let c = resp.data;
            $('#canchaId').val(c.id);
            $('#nombre').val(c.nombre);
            $('#id_tipo_cancha').val(c.id_tipo_cancha);
            $('#precio_hora').val(c.precio_hora);
            $('#capacidad').val(c.capacidad);
            $('#descripcion').val(c.descripcion);
            $('#estado').val(c.estado);
            @if(Auth::user()->esSuperAdmin())
            $('#id_complejo').val(c.id_complejo);
            @endif
            if (c.foto) {
                $('#previewFoto').attr('src', "{{ asset('') }}" + c.foto).show();
            } else {
                $('#previewFoto').hide();
            }
            $('#tituloModal').text('Editar Cancha');
            modalCancha.show();
        }).fail(function () {
            GS.finSolicitud();
            GS.toastError('No se pudo obtener la cancha.');
        });
    });

    // Guardar / Actualizar
    $('#formCancha').on('submit', function (e) {
        e.preventDefault();
        let id  = $('#canchaId').val();
        let url = id
            ? "{{ url('admin/canchas/actualizar') }}/" + id
            : "{{ route('admin.canchas.guardar') }}";

        let formData = new FormData(this);

        GS.inicioSolicitud();
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json"
        })
        .done(function (resp) {
            GS.finSolicitud();
            if (resp.status === 200) {
                modalCancha.hide();
                GS.toastSuccess(resp.message);
                TablaCanchas.ajax.reload(null, false);
            } else {
                GS.toastError(resp.message);
            }
        })
        .fail(function (xhr) {
            GS.finSolicitud();
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                GS.toastError(Object.values(xhr.responseJSON.errors)[0][0]);
            } else {
                GS.toastError('Error al guardar la cancha.');
            }
        });
    });

    // Eliminar
    $('#tablaCanchas tbody').on('click', '.btnEliminar', function () {
        let id = $(this).data('id');
        GS.modalConfirmacion('¿Eliminar cancha?', 'Esta acción no se puede revertir.', function () {
            GS.inicioSolicitud();
            $.ajax({
                url: "{{ url('admin/canchas/eliminar') }}/" + id,
                type: "DELETE",
                dataType: "json"
            })
            .done(function (resp) {
                GS.finSolicitud();
                if (resp.status === 200) {
                    GS.toastSuccess(resp.message);
                    TablaCanchas.ajax.reload(null, false);
                } else {
                    GS.toastError(resp.message);
                }
            })
            .fail(function () {
                GS.finSolicitud();
                GS.toastError('Error al eliminar la cancha.');
            });
        });
    });

});
</script>
@endsection
