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
            let categoryId = "{{ $category->id ?? '' }}"; // from Blade, or fetch dynamically
            let ajaxUrl = "{{ route('vehicle-checkings.index-checklists', ':id') }}".replace(':id', categoryId);

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
                        data: 'title',
                        name: 'title',
                    },
                    {
                        data: 'attributes',
                        name: 'attributes',
                        render: function(data, type, row) {
                            if (!data || data.length === 0) {
                                return '<span class="badge bg-danger">N/A</span>';
                            }

                            let html = '';
                            data.forEach(attr => {
                                html += `
                                <span class="badge rounded-pill bg-info me-1">
                                    ${attr.attribute_name}
                                    <a href="javascript:void(0)" class="text-white ms-1 delete-attr" 
                                    data-id="${attr.id}" title="Remove">
                                        &times;
                                    </a>
                                </span>
                            `;
                            });
                            return html;
                        }
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

            // Fetch checklist details
            $.get(`/vehicle-checkings/checklistshow/${id}`, function(data) {
                // Fill in the modal fields
                $('#checklisttitleedit').val(data.title);
                $('#vehicle_check_category_id_edit').val(data.vehicle_check_category_id);

                // Set form action dynamically
                var url = '{{ route('vehicle-checkings.update-checklists', ':id') }}';
                url = url.replace(':id', data.id);
                $('#editVehicleCheckingChecklistForm').attr('action', url);


                // Show modal
                $('#editVehicleCheckingChecklistModal').modal('show');
            });
        });
    </script>

    <script>
        $(document).on('click', '.attribute', function() {
            const checklistId = $(this).data('id');
            // Put checklist ID into hidden input
            $('#vehicle_check_checklist_id').val(checklistId);

            // Reset textarea before showing
            $('#checklistattributename').val('');

            // Show the modal
            $('#addVehicleCheckingChecklistAttributeModal').modal('show');
        });

        $(document).on('click', '.delete-attr', function() {
            const attrId = $(this).data('id');
            if (!confirm("Are you sure you want to delete this attribute?")) {
                return;
            }

            $.ajax({
                url: `/vehicle-checkings/checklist-attributes/${attrId}/delete`,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    alert(res.message || "Attribute deleted!");
                    $('#yourDataTableId').DataTable().ajax.reload(null,
                    false); // reload table without resetting page
                },
                error: function(err) {
                    alert("Something went wrong while deleting attribute!");
                }
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
                <h4 class="mb-1">{{$category->name}} Checklists</h4>
            </div>

            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#addVehicleCheckingChecklistModal">
                    <i class="icon-base ti tabler-plus"></i>
                    Add New Checklists
                </button>

                <!-- Enable Checklist Modal -->
                <div class="modal fade" id="addVehicleCheckingChecklistModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-simple modal-enable-otp modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                <div class="text-center mb-6">
                                    <h4 class="mb-2">Vehicle Check Checklist</h4>
                                    <p>Checklist details</p>
                                </div>

                                <form class="row g-5" method="POST"
                                    action="{{ route('vehicle-checkings.store-checklists') }}">
                                    @csrf
                                    <div class="col-12 form-control-validation">
                                        <label class="form-label" for="checklisttitle">Checklist</label>
                                        <div class="input-group">
                                            <textarea class="form-control" name="title" id="checklisttitle" placeholder="Title"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <input type="number" name="vehicle_check_category_id"
                                            id="vehicle_check_category_id" value="{{ $category->id }}" hidden>
                                        <button type="submit" class="btn btn-primary me-3">Submit</button>
                                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                            aria-label="Close">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Enable Checklist Modal -->

            </div>
        </div>

        <div class="card">
            <div class="card-datatable table-responsive p-2">
                <table class="datatables-ajax table table-bordered" id="Vehicle-categories-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>TITLE</th>
                            <th>ATTRIBUTES</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <!-- Edit Checklist Modal -->
            <div class="modal fade" id="editVehicleCheckingChecklistModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-simple modal-enable-otp modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            <div class="text-center mb-6">
                                <h4 class="mb-2">Vehicle Check Checklist</h4>
                                <p>Checklist details</p>
                            </div>

                            <form class="row g-5" id="editVehicleCheckingChecklistForm" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="col-12 form-control-validation">
                                    <label class="form-label" for="checklisttitleedit">Checklist</label>
                                    <div class="input-group">
                                        <textarea class="form-control" name="title" id="checklisttitleedit" placeholder="Title"></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <input type="number" name="vehicle_check_category_id"
                                        id="vehicle_check_category_id_edit" hidden>
                                    <button type="submit" class="btn btn-primary me-3">Submit</button>
                                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Edit Checklist Modal -->

            <!-- Enable Checklist Attribute Modal -->
            <div class="modal fade" id="addVehicleCheckingChecklistAttributeModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-simple modal-enable-otp modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            <div class="text-center mb-6">
                                <h4 class="mb-2">Vehicle Check Checklist Attribute</h4>
                            </div>

                            <form class="row g-5" method="POST"
                                action="{{ route('vehicle-checkings.store-checklist-attributes') }}">
                                @csrf
                                <div class="col-12 form-control-validation">
                                    <label class="form-label" for="checklistattributename">Checklist Attribute</label>
                                    <div class="input-group">
                                        <textarea class="form-control" name="attribute_name" id="checklistattributename" placeholder="Name"></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <input type="number" name="vehicle_check_checklist_id"
                                        id="vehicle_check_checklist_id" hidden>
                                    <button type="submit" class="btn btn-primary me-3">Submit</button>
                                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Enable Checklist Modal -->



        </div>
    </div>
    <!-- / Content -->

@endsection
