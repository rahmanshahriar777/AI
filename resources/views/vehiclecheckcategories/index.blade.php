@extends('layouts.master')

@section('title', 'Vehicle Checking Categories')

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
            let ajaxUrl = "{{ route('vehicle-checkings.index-categories') }}";

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
                        data: 'parent',
                        name: 'parent'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            return data ? '<span class="badge bg-success">Active</span>' :
                                '<span class="badge bg-danger">Inactive</span>';
                        }
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
            $('#is_parent_switch').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#parent-category-div').hide();
                } else {
                    $('#parent-category-div').show();
                }
            });
        });
        $(document).ready(function() {
            $(document).on('click', '.delete', function() {
                var id = $(this).data('id');

                // SweetAlert confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This category will be deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('vehiclecategories.destroy', ':id') }}'.replace(
                                ':id', id),
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire('Deleted!', response.message, 'success');
                                $('#Vehicle-categories-table').DataTable().ajax
                                    .reload();
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

            $.get(`/vehiclecategories/${id}`, function(data) {
                $('#edit-id').val(data.id);
                $('#edit-name').val(data.name);
                $('#edit-description').val(data.description ?? '');
                $('#edit-is-active').prop('checked', data.is_active == 1);

                // Set the form's action URL dynamically:
                var url = '{{ route('vehiclecategories.update', ':id') }}';
                url = url.replace(':id', data.id);
                $('#editCategoryForm').attr('action', url);

                $('#editCategoryModal').modal('show');
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
                <h4 class="mb-1">Vehicle Checking Categories</h4>
            </div>

            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#addVehicleCheckingCategoryModal">
                    <i class="icon-base ti tabler-plus"></i>
                    Add New Category
                </button>

                <!--Add Modal -->
                <div class="modal fade" id="addVehicleCheckingCategoryModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form action="{{ route('vehicle-checkings.store-categories') }}" method="POST">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addVehicleCheckingCategoryModalTitle">Vehicle Checking
                                        Category</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12 mb-4">
                                            <label for="category-name" class="form-label">Category Name</label>
                                            <input type="text" class="form-control" name="name" id="category-name"
                                                required>
                                        </div>

                                        <div class="col-12 mb-4 form-check form-switch">
                                            <input type="hidden" name="is_parent" value="false">
                                            <!-- fallback when unchecked -->
                                            <input class="form-check-input" type="checkbox" id="is_parent_switch"
                                                name="is_parent" value="true">
                                            <label class="form-check-label ms-2" for="is_parent_switch">Is Parent
                                                Category?</label>
                                        </div>

                                        <div class="col-12 mb-4" id="parent-category-div" style="display: block;">
                                            <label for="parent_id" class="form-label">Parent Parent Category</label>
                                            <select class="form-select" name="parent_id" id="parent_id">
                                                <option value="">Select Parent Category</option>
                                                @foreach ($parentCategories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col mb-4">
                                            <label for="category-description" class="form-label">Description</label>
                                            <input type="text" class="form-control" name="description"
                                                id="category-description">
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
                <table class="datatables-ajax table table-bordered" id="Vehicle-categories-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NAME</th>
                            <th>DESCRIPTION</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                </table>
            </div>



        </div>
    </div>
    <!-- / Content -->

@endsection
