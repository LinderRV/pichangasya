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
                    <h4 class="card-title">Gestión de Usuarios</h4>
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
                        <i class="fa fa-plus me-1"></i> Nuevo Usuario
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaUsuarios" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Rol</th>
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

    {{-- Modal Usuario --}}
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formUsuario" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloModal">Nuevo Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="usuarioId" name="id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombres" class="form-label fw-bold">Nombres <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombres" name="nombres" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellidos" class="form-label fw-bold">Apellidos <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-bold">Correo <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label fw-bold">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" maxlength="20">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sexo" class="form-label fw-bold">Sexo</label>
                                <select class="form-control" id="sexo" name="sexo">
                                    <option value="">-- Selecciona --</option>
                                    <option value="masculino">Masculino</option>
                                    <option value="femenino">Femenino</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="id_rol" class="form-label fw-bold">Rol <span class="text-danger">*</span></label>
                                <select class="form-control" id="id_rol" name="id_rol" required>
                                    <option value="">-- Selecciona --</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="estado" class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                                <select class="form-control" id="estado" name="estado" required>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-bold">
                                    Contraseña <span class="text-danger" id="reqPass">*</span>
                                </label>
                                <input type="password" class="form-control" id="password" name="password" autocomplete="new-password">
                                <small class="text-muted" id="ayudaPass" style="display:none;">Déjalo vacío para no cambiarla.</small>
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

        let modalUsuario = new bootstrap.Modal(document.getElementById('modalUsuario'));

        let TablaUsuarios = $('#tablaUsuarios').DataTable({
            processing: true,
            serverSide: false,
            responsive: true,
            destroy: true,
            pagingType: "simple_numbers",
            language: {
                url: "{{ asset('/vendor/datatables/js/Spanish.json') }}"
            },
            ajax: {
                url: "{{ route('admin.usuarios.lista') }}",
                type: "GET",
                dataSrc: "data"
            },
            columns: [
                { data: "nombres" },
                { data: "apellidos" },
                { data: "email" },
                { data: "telefono", render: d => d || '-' },
                { data: "rol" },
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
                        let btnEliminar = row.id_rol === 1 ? '' : `
                                <button type="button"
                                    class="btn btn-danger shadow btn-xs sharp btnEliminar"
                                    data-id="${row.id}">
                                    <i class="fa fa-trash"></i>
                                </button>`;
                        return `
                            <div class="d-flex">
                                <button type="button"
                                    class="btn btn-primary shadow btn-xs sharp me-1 btnEdit"
                                    data-id="${row.id}">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                ${btnEliminar}
                            </div>
                        `;
                    }
                }
            ]
        });

        // Nuevo
        $('#btnNuevo').on('click', function() {
            $('#formUsuario')[0].reset();
            $('#usuarioId').val('');
            $('#tituloModal').text('Nuevo Usuario');
            $('#password').prop('required', true);
            $('#reqPass').show();
            $('#ayudaPass').hide();
            modalUsuario.show();
        });

        // Editar - cargar datos
        $('#tablaUsuarios tbody').on('click', '.btnEdit', function() {
            let id = $(this).data('id');
            GS.inicioSolicitud();
            $.ajax({
                url: "{{ url('admin/usuarios/obtener') }}/" + id,
                type: "GET",
                dataType: "json"
            })
            .done(function(resp) {
                GS.finSolicitud();
                let u = resp.data;
                $('#usuarioId').val(u.id);
                $('#nombres').val(u.nombres);
                $('#apellidos').val(u.apellidos);
                $('#email').val(u.email);
                $('#telefono').val(u.telefono);
                $('#sexo').val(u.sexo || '');
                $('#id_rol').val(u.id_rol || '');
                $('#estado').val(u.estado);
                $('#password').val('').prop('required', false);
                $('#reqPass').hide();
                $('#ayudaPass').show();
                $('#tituloModal').text('Editar Usuario');
                modalUsuario.show();
            })
            .fail(function() {
                GS.finSolicitud();
                GS.toastError('No se pudo obtener el usuario.');
            });
        });

        // Guardar (crear / actualizar)
        $('#formUsuario').on('submit', function(e) {
            e.preventDefault();
            let id = $('#usuarioId').val();
            let esEditar = id !== '';

            let url = esEditar
                ? "{{ url('admin/usuarios/actualizar') }}/" + id
                : "{{ route('admin.usuarios.guardar') }}";
            let metodo = esEditar ? "PUT" : "POST";

            GS.inicioSolicitud();
            $.ajax({
                url: url,
                type: metodo,
                data: $('#formUsuario').serialize(),
                dataType: "json"
            })
            .done(function(resp) {
                GS.finSolicitud();
                if (resp.status === 200) {
                    modalUsuario.hide();
                    GS.toastSuccess(resp.message);
                    TablaUsuarios.ajax.reload(null, false);
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
                    GS.toastError('Error al guardar el usuario.');
                }
            });
        });

        // Eliminar
        $('#tablaUsuarios tbody').on('click', '.btnEliminar', function() {
            let id = $(this).data('id');
            GS.modalConfirmacion('¿Eliminar usuario?', 'Esta acción no se puede revertir.', function() {
                GS.inicioSolicitud();
                $.ajax({
                    url: "{{ url('admin/usuarios/eliminar') }}/" + id,
                    type: "DELETE",
                    dataType: "json"
                })
                .done(function(resp) {
                    GS.finSolicitud();
                    if (resp.status === 200) {
                        GS.toastSuccess(resp.message);
                        TablaUsuarios.ajax.reload(null, false);
                    } else {
                        GS.toastError(resp.message);
                    }
                })
                .fail(function() {
                    GS.finSolicitud();
                    GS.toastError('Error al eliminar el usuario.');
                });
            });
        });

    });
</script>

@endsection
