@extends('layouts.app')
@section('content')

<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius:0!important; margin:0 3px; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { border-radius:0!important; }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Métodos de Pago</h4>
                <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
                    <i class="fa fa-plus me-1"></i> Nuevo Método
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaMetodos" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nombre</th>
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

{{-- Modal Método de Pago --}}
<div class="modal fade" id="modalMetodo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formMetodo" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModal">Nuevo Método de Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="metodoId" name="id">

                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" maxlength="80" required>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                        <select class="form-control" id="estado" name="estado" required>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
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

        let modalMetodo = new bootstrap.Modal(document.getElementById('modalMetodo'));

        const estadoBadge = {
            'activo':   'badge-success',
            'inactivo': 'badge-secondary',
        };

        let TablaMetodos = $('#tablaMetodos').DataTable({
            processing: true,
            serverSide: false,
            responsive: true,
            destroy: true,
            pagingType: "simple_numbers",
            language: {
                url: "{{ asset('/vendor/datatables/js/Spanish.json') }}"
            },
            ajax: {
                url: "{{ route('admin.metodospago.lista') }}",
                type: "GET",
                dataSrc: "data"
            },
            columns: [
                { data: "nombre" },
                { data: "estado", render: d => `<span class="badge ${estadoBadge[d] || 'badge-secondary'}">${d}</span>` },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex">
                                <button type="button"
                                    class="btn btn-primary shadow btn-xs sharp me-1 btnEdit"
                                    data-id="${row.id}">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button type="button"
                                    class="btn btn-danger shadow btn-xs sharp btnEliminar"
                                    data-id="${row.id}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });

        // Nuevo
        $('#btnNuevo').on('click', function() {
            $('#formMetodo')[0].reset();
            $('#metodoId').val('');
            $('#tituloModal').text('Nuevo Método de Pago');
            modalMetodo.show();
        });

        // Editar - cargar datos
        $('#tablaMetodos tbody').on('click', '.btnEdit', function() {
            let id = $(this).data('id');
            GS.inicioSolicitud();
            $.ajax({
                url: "{{ url('admin/metodos-pago/obtener') }}/" + id,
                type: "GET",
                dataType: "json"
            })
            .done(function(resp) {
                GS.finSolicitud();
                let metodo = resp.data;
                $('#metodoId').val(metodo.id);
                $('#nombre').val(metodo.nombre);
                $('#estado').val(metodo.estado);
                $('#tituloModal').text('Editar Método de Pago');
                modalMetodo.show();
            })
            .fail(function() {
                GS.finSolicitud();
                GS.toastError('No se pudo obtener el método de pago.');
            });
        });

        // Guardar (crear / actualizar)
        $('#formMetodo').on('submit', function(e) {
            e.preventDefault();
            let id = $('#metodoId').val();
            let esEditar = id !== '';

            let url = esEditar
                ? "{{ url('admin/metodos-pago/actualizar') }}/" + id
                : "{{ route('admin.metodospago.guardar') }}";
            let metodo = esEditar ? "PUT" : "POST";

            GS.inicioSolicitud();
            $.ajax({
                url: url,
                type: metodo,
                data: {
                    nombre: $('#nombre').val(),
                    estado: $('#estado').val()
                },
                dataType: "json"
            })
            .done(function(resp) {
                GS.finSolicitud();
                if (resp.status === 200) {
                    modalMetodo.hide();
                    GS.toastSuccess(resp.message);
                    TablaMetodos.ajax.reload(null, false);
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
                    GS.toastError('Error al guardar el método de pago.');
                }
            });
        });

        // Eliminar
        $('#tablaMetodos tbody').on('click', '.btnEliminar', function() {
            let id = $(this).data('id');
            GS.modalConfirmacion('¿Eliminar método de pago?', 'Esta acción no se puede revertir.', function() {
                GS.inicioSolicitud();
                $.ajax({
                    url: "{{ url('admin/metodos-pago/eliminar') }}/" + id,
                    type: "DELETE",
                    dataType: "json"
                })
                .done(function(resp) {
                    GS.finSolicitud();
                    if (resp.status === 200) {
                        GS.toastSuccess(resp.message);
                        TablaMetodos.ajax.reload(null, false);
                    } else {
                        GS.toastError(resp.message);
                    }
                })
                .fail(function() {
                    GS.finSolicitud();
                    GS.toastError('Error al eliminar el método de pago.');
                });
            });
        });

    });
</script>

@endsection
