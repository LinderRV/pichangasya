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
                <div class="card-header">                    
                    <h4 class="card-title">Gestión Roles</h4>
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

@endsection



@section('script')
<script>
     $(document).ready(function() {
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

     
    });

    </script>

@endsection

