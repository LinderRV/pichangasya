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
                    <h4 class="card-title">Gestión Roles</h4>
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
                        <i class="fa fa-plus me-1"></i> Nuevo Rol
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example3" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nombres</th>
                                <th>Descripción</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal Rol --}}
    <div class="modal fade" id="modalRol" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formRol" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloModal">Nuevo Rol</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="rolId" name="id">

                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="85" required>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-bold">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="255"></textarea>
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

        let modalRol = new bootstrap.Modal(document.getElementById('modalRol'));

        let TablaRoles = $('#example3').DataTable({
            processing: true,
            serverSide: false,
            responsive: true,
            destroy: true,
            pagingType: "simple_numbers",
            language: {
                url: "{{ asset('/vendor/datatables/js/Spanish.json') }}"
            },
            ajax: {
                url: "{{ route('admin.rol.lista') }}",
                type: "GET",
                dataSrc: "data"
            },
            columns: [
                { data: "nombre" },
                { data: "descripcion" },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let btnEliminar = row.id === 1 ? '' : `
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
            $('#formRol')[0].reset();
            $('#rolId').val('');
            $('#tituloModal').text('Nuevo Rol');
            modalRol.show();
        });

        // Editar - cargar datos
        $('#example3 tbody').on('click', '.btnEdit', function() {
            let id = $(this).data('id');
            GS.inicioSolicitud();
            $.ajax({
                url: "{{ url('admin/rol/obtener') }}/" + id,
                type: "GET",
                dataType: "json"
            })
            .done(function(resp) {
                GS.finSolicitud();
                let rol = resp.data;
                $('#rolId').val(rol.id);
                $('#nombre').val(rol.nombre);
                $('#descripcion').val(rol.descripcion);
                $('#tituloModal').text('Editar Rol');
                modalRol.show();
            })
            .fail(function() {
                GS.finSolicitud();
                GS.toastError('No se pudo obtener el rol.');
            });
        });

        // Guardar (crear / actualizar)
        $('#formRol').on('submit', function(e) {
            e.preventDefault();
            let id = $('#rolId').val();
            let esEditar = id !== '';

            let url = esEditar
                ? "{{ url('admin/rol/actualizar') }}/" + id
                : "{{ route('admin.rol.guardar') }}";
            let metodo = esEditar ? "PUT" : "POST";

            GS.inicioSolicitud();
            $.ajax({
                url: url,
                type: metodo,
                data: {
                    nombre: $('#nombre').val(),
                    descripcion: $('#descripcion').val()
                },
                dataType: "json"
            })
            .done(function(resp) {
                GS.finSolicitud();
                if (resp.status === 200) {
                    modalRol.hide();
                    GS.toastSuccess(resp.message);
                    TablaRoles.ajax.reload(null, false);
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
                    GS.toastError('Error al guardar el rol.');
                }
            });
        });

        // Eliminar
        $('#example3 tbody').on('click', '.btnEliminar', function() {
            let id = $(this).data('id');
            GS.modalConfirmacion('¿Eliminar rol?', 'Esta acción no se puede revertir.', function() {
                GS.inicioSolicitud();
                $.ajax({
                    url: "{{ url('admin/rol/eliminar') }}/" + id,
                    type: "DELETE",
                    dataType: "json"
                })
                .done(function(resp) {
                    GS.finSolicitud();
                    if (resp.status === 200) {
                        GS.toastSuccess(resp.message);
                        TablaRoles.ajax.reload(null, false);
                    } else {
                        GS.toastError(resp.message);
                    }
                })
                .fail(function() {
                    GS.finSolicitud();
                    GS.toastError('Error al eliminar el rol.');
                });
            });
        });

    });
</script>

@endsection
