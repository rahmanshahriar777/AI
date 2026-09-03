@extends('layouts.master')

@section('title', 'Stock Attributes')

@section('scripts')
    @parent

    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script type="text/javascript">
        $(function() {
            let ajaxUrl = "{{ route('stockattributes.index') }}";

            var table = $('.datatables-ajax').DataTable({
                processing: true,
                serverSide: true,
                ajax: ajaxUrl,

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            var select2Elements = $('.select2');

            if (select2Elements.length) {
                select2Elements.each(function() {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'Select value',
                        dropdownParent: $this.parent()
                    });
                });
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.delete', function() {
                var id = $(this).data('id');

                // SweetAlert confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This attribute will be deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('stockattributes.destroy', ':id') }}'.replace(
                                ':id', id),
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire('Deleted!', response.message, 'success');
                                $('#stock-categories-table').DataTable().ajax.reload();
                            },
                            error: function() {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(document).on('click', '.edit', function() {
            const id = $(this).data('id');

            $.get(`/stockattributes/${id}`, function(data) {
                $('#edit-id').val(data.id);
                $('#edit-name').val(data.name);
                $('#edit-unit').val(data.unit ?? '');


                // Set the form's action URL dynamically:
                var url = '{{ route('stockattributes.update', ':id') }}';
                url = url.replace(':id', data.id);
                $('#editAttributeForm').attr('action', url);
                $('#editAttributeModal').modal('show');
            });
        });
    </script>


@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('content')

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
            <div class="d-flex flex-column justify-content-center">
                <h4 class="mb-1">Stock Attribute</h4>
            </div>

            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#addStockAttributeModal">
                    <i class="icon-base ti tabler-plus"></i>
                    Add New Attribute
                </button>

                <!--Add Modal -->
                <div class="modal fade" id="addStockAttributeModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form action="{{ route('stockattributes.store') }}" method="POST">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addStockAttributeModalTitle">Stock Attribute</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label for="attribute-name" class="form-label">Attribute Name</label>
                                            <input type="text" class="form-control" name="name" id="attribute-name"
                                                required>
                                        </div>
                                        <div class="col mb-4">
                                            <label for="attribute-unit" class="form-label">Unit</label>
                                            <input type="text" class="form-control" name="unit" id="attribute-unit"
                                                placeholder="e.g. kg, pcs, etc.">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                                        Close
                                    </button>
                                    <button type="submit" class="btn btn-primary">Create</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-datatable table-responsive p-2">
                <table class="datatables-ajax table table-bordered" id="stock-categories-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NAME</th>
                            <th>UNIT</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <!-- Edit attribute Modal -->
            <div class="modal fade" id="editAttributeModal" tabindex="-1" aria-labelledby="editAttributeModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <form id="editAttributeForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Attribute</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <input type="hidden" name="id" id="edit-id">

                                <div class="mb-3">
                                    <label for="edit-name" class="form-label">Name</label>
                                    <input type="text" name="name" id="edit-name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit-unit" class="form-label">Unit</label>
                                    <input type="text" name="unit" id="edit-unit" class="form-control"
                                        placeholder="e.g. kg, pcs, etc.">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <!-- / Content -->

@endsection
