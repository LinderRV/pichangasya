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
                    <h4 class="card-title">Complejos Deportivos</h4>
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
                        <i class="fa fa-plus me-1"></i> Nuevo Complejo
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaComplejos" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Distrito</th>
                                    <th>Dueño</th>
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

    {{-- Modal Complejo --}}
    <div class="modal fade" id="modalComplejo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formComplejo" enctype="multipart/form-data" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloModal">Nuevo Complejo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="complejoId" name="id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Correo <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="correo" name="correo" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">RUC</label>
                                <input type="text" class="form-control" id="ruc" name="ruc">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" maxlength="20">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Departamento <span class="text-danger">*</span></label>
                                <select class="form-control" id="id_departamento" required>
                                    <option value="">-- Selecciona --</option>
                                    @foreach($departamentos as $d)
                                        <option value="{{ $d->id }}">{{ $d->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Provincia <span class="text-danger">*</span></label>
                                <select class="form-control" id="id_provincia" required disabled>
                                    <option value="">-- Selecciona --</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Distrito <span class="text-danger">*</span></label>
                                <select class="form-control" id="id_distrito" name="id_distrito" required disabled>
                                    <option value="">-- Selecciona --</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2" maxlength="255"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                                <select class="form-control" id="estado" name="estado" required>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Imagen</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                                <div class="mt-2">
                                    <img id="previewImagen" src="" alt="" style="max-height:90px; display:none;" class="rounded border">
                                </div>
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
    $(document).ready(function() {

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });

        let modalComplejo = new bootstrap.Modal(document.getElementById('modalComplejo'));

        let TablaComplejos = $('#tablaComplejos').DataTable({
            processing: true,
            serverSide: false,
            responsive: true,
            destroy: true,
            pagingType: "simple_numbers",
            language: {
                url: "{{ asset('/vendor/datatables/js/Spanish.json') }}"
            },
            ajax: {
                url: "{{ route('admin.complejos.lista') }}",
                type: "GET",
                dataSrc: "data"
            },
            columns: [
                { data: "nombre" },
                { data: "correo" },
                { data: "telefono", render: d => d || '-' },
                { data: "distrito" },
                { data: "dueno" },
                {
                    data: "estado",
                    render: function(d) {
                        let clase = d === 'activo' ? 'badge-success' : 'badge-danger';
                        return `<span class="badge ${clase}">${d}</span>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex">
                                <button type="button" class="btn btn-primary shadow btn-xs sharp me-1 btnEdit" data-id="${row.id}">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-danger shadow btn-xs sharp btnEliminar" data-id="${row.id}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });

        // ----- Cascada ubicación -----
        function cargarProvincias(idDep, seleccion = null, callback = null) {
            $('#id_provincia').prop('disabled', true).html('<option value="">-- Selecciona --</option>');
            $('#id_distrito').prop('disabled', true).html('<option value="">-- Selecciona --</option>');
            if (!idDep) return;
            $.getJSON("{{ url('admin/complejos/provincias') }}/" + idDep, function(resp) {
                resp.data.forEach(p => $('#id_provincia').append(`<option value="${p.id}">${p.nombre}</option>`));
                $('#id_provincia').prop('disabled', false);
                if (seleccion) $('#id_provincia').val(seleccion);
                if (callback) callback();
            });
        }

        function cargarDistritos(idProv, seleccion = null) {
            $('#id_distrito').prop('disabled', true).html('<option value="">-- Selecciona --</option>');
            if (!idProv) return;
            $.getJSON("{{ url('admin/complejos/distritos') }}/" + idProv, function(resp) {
                resp.data.forEach(d => $('#id_distrito').append(`<option value="${d.id}">${d.nombre}</option>`));
                $('#id_distrito').prop('disabled', false);
                if (seleccion) $('#id_distrito').val(seleccion);
            });
        }

        $('#id_departamento').on('change', function() {
            cargarProvincias($(this).val());
        });
        $('#id_provincia').on('change', function() {
            cargarDistritos($(this).val());
        });

        // Preview imagen
        $('#imagen').on('change', function() {
            let file = this.files[0];
            if (file) {
                $('#previewImagen').attr('src', URL.createObjectURL(file)).show();
            } else {
                $('#previewImagen').hide();
            }
        });

        // Nuevo
        $('#btnNuevo').on('click', function() {
            $('#formComplejo')[0].reset();
            $('#complejoId').val('');
            $('#id_provincia, #id_distrito').prop('disabled', true).html('<option value="">-- Selecciona --</option>');
            $('#previewImagen').hide();
            $('#tituloModal').text('Nuevo Complejo');
            modalComplejo.show();
        });

        // Editar
        $('#tablaComplejos tbody').on('click', '.btnEdit', function() {
            let id = $(this).data('id');
            GS.inicioSolicitud();
            $.getJSON("{{ url('admin/complejos/obtener') }}/" + id, function(resp) {
                GS.finSolicitud();
                let c = resp.data;
                $('#complejoId').val(c.id);
                $('#nombre').val(c.nombre);
                $('#correo').val(c.correo);
                $('#ruc').val(c.ruc);
                $('#telefono').val(c.telefono);
                $('#direccion').val(c.direccion);
                $('#descripcion').val(c.descripcion);
                $('#estado').val(c.estado);

                $('#id_departamento').val(c.id_departamento);
                cargarProvincias(c.id_departamento, c.id_provincia, function() {
                    cargarDistritos(c.id_provincia, c.id_distrito);
                });

                if (c.imagen) {
                    $('#previewImagen').attr('src', "{{ asset('') }}" + c.imagen).show();
                } else {
                    $('#previewImagen').hide();
                }

                $('#tituloModal').text('Editar Complejo');
                modalComplejo.show();
            }).fail(function() {
                GS.finSolicitud();
                GS.toastError('No se pudo obtener el complejo.');
            });
        });

        // Guardar (crear / actualizar) con FormData (imagen)
        $('#formComplejo').on('submit', function(e) {
            e.preventDefault();
            let id = $('#complejoId').val();
            let esEditar = id !== '';

            let url = esEditar
                ? "{{ url('admin/complejos/actualizar') }}/" + id
                : "{{ route('admin.complejos.guardar') }}";

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
            .done(function(resp) {
                GS.finSolicitud();
                if (resp.status === 200) {
                    modalComplejo.hide();
                    GS.toastSuccess(resp.message);
                    TablaComplejos.ajax.reload(null, false);
                } else {
                    GS.toastError(resp.message);
                }
            })
            .fail(function(xhr) {
                GS.finSolicitud();
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    GS.toastError(Object.values(xhr.responseJSON.errors)[0][0]);
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    GS.toastError(xhr.responseJSON.message);
                } else {
                    GS.toastError('Error al guardar el complejo.');
                }
            });
        });

        // Eliminar
        $('#tablaComplejos tbody').on('click', '.btnEliminar', function() {
            let id = $(this).data('id');
            GS.modalConfirmacion('¿Eliminar complejo?', 'Esta acción no se puede revertir.', function() {
                GS.inicioSolicitud();
                $.ajax({
                    url: "{{ url('admin/complejos/eliminar') }}/" + id,
                    type: "DELETE",
                    dataType: "json"
                })
                .done(function(resp) {
                    GS.finSolicitud();
                    if (resp.status === 200) {
                        GS.toastSuccess(resp.message);
                        TablaComplejos.ajax.reload(null, false);
                    } else {
                        GS.toastError(resp.message);
                    }
                })
                .fail(function() {
                    GS.finSolicitud();
                    GS.toastError('Error al eliminar el complejo.');
                });
            });
        });

    });
</script>

@endsection
