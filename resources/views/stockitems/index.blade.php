@extends('layouts.master')

@section('title', 'Stock Items')

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
            let ajaxUrl = "{{ route('stockitems.index') }}";

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
                        data: 'category',
                        name: 'category'
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
                    text: "This Item will be deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('stockitems.destroy', ':id') }}'.replace(
                                ':id', id),
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire('Deleted!', response.message, 'success');
                                $('#stock-items-table').DataTable().ajax.reload();
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
                <h4 class="mb-1">Stock items</h4>
            </div>

            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStockItemModal">
                    <i class="icon-base ti tabler-plus"></i>
                    Add New Item
                </button>

                <!--Add Modal -->
                <div class="modal fade" id="addStockItemModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form action="{{ route('stockitems.store') }}" method="POST">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addStockItemModalTitle">Stock Item</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12 mb-4">
                                            <label for="select2Basic" class="form-label">Select Category</label>
                                            <select id="select2Basic" class="select2 form-select" data-allow-clear="true"
                                                name="category_id">
                                                <option value="0">None</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 mb-4">
                                            <label for="item-name" class="form-label">Item Name</label>
                                            <input type="text" class="form-control" name="name" id="item-name"
                                                required>
                                        </div>
                                        <div class="col-12 mb-4">
                                            <label for="item-description" class="form-label">Description</label>
                                            <textarea class="form-control" name="description" id="item-description" rows="3"></textarea>
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
                <table class="datatables-ajax table table-bordered" id="stock-items-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NAME</th>
                            <th>PARENT</th>
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
