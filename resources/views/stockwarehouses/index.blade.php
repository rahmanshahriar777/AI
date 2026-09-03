@extends('layouts.master')

@section('title', 'Stock Warehouses')

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
            let ajaxUrl = "{{ route('stockwarehouses.index') }}";

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
                        data: 'location',
                        name: 'location'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
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
                    text: "This warehouse will be deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('stockwarehouses.destroy', ':id') }}'.replace(
                                ':id', id),
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire('Deleted!', response.message, 'success');
                                $('#stock-warehouses-table').DataTable().ajax.reload();
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

            $.get(`/stockwarehouses/${id}`, function(data) {
                $('#edit-id').val(data.id);
                $('#edit-name').val(data.name);
                $('#edit-location').val(data.location);
                $('#edit-description').val(data.description ?? '');
                $('#edit-is-active').prop('checked', data.is_active == 1);

                // Set the form's action URL dynamically:
                var url = '{{ route('stockwarehouses.update', ':id') }}';
                url = url.replace(':id', data.id);
                $('#editWarehouseForm').attr('action', url);

                $('#editWarehouseModal').modal('show');
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
                <h4 class="mb-1">Stock Warehouses</h4>
            </div>

            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#addStockWarehouseModal">
                    <i class="icon-base ti tabler-plus"></i>
                    Add New Warehouse
                </button>

                <!--Add Modal -->
                <div class="modal fade" id="addStockWarehouseModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form action="{{ route('stockwarehouses.store') }}" method="POST">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addStockWarehouseModalTitle">Stock Warehouse</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12 mb-4">
                                            <label for="warehouse-name" class="form-label">Warehouse Name</label>
                                            <input type="text" class="form-control" name="name" id="warehouse-name"
                                                required>
                                        </div>
                                        <div class="col-12 mb-4">
                                            <label for="warehouse-location" class="form-label">Location</label>
                                            <input type="text" class="form-control" name="location"
                                                id="warehouse-location" required>
                                        </div>
                                        <div class="col-12 mb-4">
                                            <label for="warehouse-description" class="form-label">Description</label>
                                            <textarea class="form-control" name="description" id="warehouse-description"></textarea>
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
                <table class="datatables-ajax table table-bordered" id="stock-Warehouses-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NAME</th>
                            <th>Location</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <!-- Edit warehouse Modal -->
            <div class="modal fade" id="editWarehouseModal" tabindex="-1" aria-labelledby="editWarehouseModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <form id="editWarehouseForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Warehouse</h5>
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
                                    <label for="edit-location" class="form-label">Location</label>
                                    <input type="text" name="location" id="edit-location" class="form-control"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit-description" class="form-label">Description</label>
                                    <textarea name="description" id="edit-description" class="form-control"></textarea>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="edit-is-active"
                                        value="1">
                                    <label class="form-check-label" for="edit-is-active">Active</label>
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
