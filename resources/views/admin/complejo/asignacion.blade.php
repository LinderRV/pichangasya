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
                    <h4 class="card-title">Asignar Dueño a Complejo</h4>
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
                        <i class="fa fa-plus me-1"></i> Nueva Asignación
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaAsignaciones" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Complejo</th>
                                    <th>Dueño (Usuario Interno)</th>
                                    <th>Cargo</th>
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

    {{-- Modal Asignación --}}
    <div class="modal fade" id="modalAsignacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formAsignacion" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloModal">Nueva Asignación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="asignacionId" name="id">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Complejo <span class="text-danger">*</span></label>
                            <select class="form-control" id="id_complejo" name="id_complejo" required>
                                <option value="">-- Selecciona --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Dueño (Usuario Interno) <span class="text-danger">*</span></label>
                            <select class="form-control" id="id_usuario" name="id_usuario" required>
                                <option value="">-- Selecciona --</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Cargo <span class="text-danger">*</span></label>
                                <select class="form-control" id="cargo" name="cargo" required>
                                    <option value="Dueño">Dueño</option>
                                    <option value="Empleado">Empleado</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                                <select class="form-control" id="estado" name="estado" required>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
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
<script nonce="{{ request()->attributes->get('csp_nonce') }}">
    $(document).ready(function() {

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });

        let modalAsignacion = new bootstrap.Modal(document.getElementById('modalAsignacion'));

        let TablaAsignaciones = $('#tablaAsignaciones').DataTable({
            processing: true,
            serverSide: false,
            responsive: true,
            destroy: true,
            pagingType: "simple_numbers",
            language: {
                url: "{{ asset('/vendor/datatables/js/Spanish.json') }}"
            },
            ajax: {
                url: "{{ route('admin.complejos.asignacion.lista') }}",
                type: "GET",
                dataSrc: "data"
            },
            columns: [
                { data: "complejo" },
                { data: "dueno" },
                { data: "cargo" },
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

        // Cargar selects (complejos y usuarios disponibles)
        function cargarSelects(idAsignacion = null, selComplejo = null, selUsuario = null) {
            let urlC = "{{ route('admin.complejos.asignacion.complejosDisponibles') }}";
            let urlU = "{{ route('admin.complejos.asignacion.usuariosDisponibles') }}";
            if (idAsignacion) {
                urlU += "?id=" + idAsignacion;
            }

            $.getJSON(urlC, function(resp) {
                $('#id_complejo').html('<option value="">-- Selecciona --</option>');
                resp.data.forEach(c => $('#id_complejo').append(`<option value="${c.id}">${c.nombre}</option>`));
                if (selComplejo) $('#id_complejo').val(selComplejo);
            });

            $.getJSON(urlU, function(resp) {
                $('#id_usuario').html('<option value="">-- Selecciona --</option>');
                resp.data.forEach(u => $('#id_usuario').append(`<option value="${u.id}">${u.nombre}</option>`));
                if (selUsuario) $('#id_usuario').val(selUsuario);
            });
        }

        // Nuevo
        $('#btnNuevo').on('click', function() {
            $('#formAsignacion')[0].reset();
            $('#asignacionId').val('');
            $('#tituloModal').text('Nueva Asignación');
            cargarSelects();
            modalAsignacion.show();
        });

        // Editar
        $('#tablaAsignaciones tbody').on('click', '.btnEdit', function() {
            let id = $(this).data('id');
            GS.inicioSolicitud();
            $.getJSON("{{ url('admin/complejos/asignacion/obtener') }}/" + id, function(resp) {
                GS.finSolicitud();
                let a = resp.data;
                $('#asignacionId').val(a.id);
                $('#cargo').val(a.cargo);
                $('#estado').val(a.estado);
                cargarSelects(a.id, a.id_complejo, a.id_usuario);
                $('#tituloModal').text('Editar Asignación');
                modalAsignacion.show();
            }).fail(function() {
                GS.finSolicitud();
                GS.toastError('No se pudo obtener la asignación.');
            });
        });

        // Guardar (crear / actualizar)
        $('#formAsignacion').on('submit', function(e) {
            e.preventDefault();
            let id = $('#asignacionId').val();
            let esEditar = id !== '';

            let url = esEditar
                ? "{{ url('admin/complejos/asignacion/actualizar') }}/" + id
                : "{{ route('admin.complejos.asignacion.guardar') }}";
            let metodo = esEditar ? "PUT" : "POST";

            GS.inicioSolicitud();
            $.ajax({
                url: url,
                type: metodo,
                data: $('#formAsignacion').serialize(),
                dataType: "json"
            })
            .done(function(resp) {
                GS.finSolicitud();
                if (resp.status === 200) {
                    modalAsignacion.hide();
                    GS.toastSuccess(resp.message);
                    TablaAsignaciones.ajax.reload(null, false);
                } else {
                    GS.toastError(resp.message);
                }
            })
            .fail(function(xhr) {
                GS.finSolicitud();
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let primer = Object.values(xhr.responseJSON.errors)[0][0];
                    GS.toastError(primer);
                } else {
                    GS.toastError('Error al guardar la asignación.');
                }
            });
        });

        // Eliminar
        $('#tablaAsignaciones tbody').on('click', '.btnEliminar', function() {
            let id = $(this).data('id');
            GS.modalConfirmacion('¿Eliminar asignación?', 'Esta acción no se puede revertir.', function() {
                GS.inicioSolicitud();
                $.ajax({
                    url: "{{ url('admin/complejos/asignacion/eliminar') }}/" + id,
                    type: "DELETE",
                    dataType: "json"
                })
                .done(function(resp) {
                    GS.finSolicitud();
                    if (resp.status === 200) {
                        GS.toastSuccess(resp.message);
                        TablaAsignaciones.ajax.reload(null, false);
                    } else {
                        GS.toastError(resp.message);
                    }
                })
                .fail(function() {
                    GS.finSolicitud();
                    GS.toastError('Error al eliminar la asignación.');
                });
            });
        });

    });
</script>

@endsection
