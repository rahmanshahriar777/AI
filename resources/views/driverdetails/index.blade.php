@extends('layouts.master')

@section('title', 'Drivers')

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
            var table = $('.datatables-ajax').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('drivers.index') }}",

                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'license_no',
                        name: 'license_no',
                    },
                    {
                        data: 'license_expiry',
                        name: 'license_expiry',
                        render: function(data) {
                            return moment(data).format('DD-MM-YYYY');
                        }
                    },
                    {
                        data: 'vehicle_assigned',
                        name: 'vehicle_assigned'
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
        $(document).on('click', '.delete', function() {
            let driverId = $(this).data('id');
            let url = `/drivers/${driverId}`;

            if (confirm("Are you sure you want to delete this driver profile?")) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Get CSRF token
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        $('.datatables-ajax').DataTable().ajax.reload(); // Reload DataTable
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            }
        });
        $(document).on('click', '.assignvehicle', function() {
            let driverId = $(this).data('id');
            $('#driver_id').val(driverId);
            $('#addVehicleAssignModal').modal('show');
        });

        $(document).ready(function() {
            // Re-init select2 every time modal opens
            $('#addVehicleAssignModal').on('shown.bs.modal', function() {
                $('#vehicle_id').select2({
                    dropdownParent: $('#addVehicleAssignModal'), // important for modal
                    width: '100%'
                });
            });
        });

        $(document).on('click', '.unassugnvehicle', function(e) {
            e.preventDefault();

            let driverId = $(this).data('id'); // from button
            let url = "{{ route('drivers.unassign-vehicle', ':id') }}".replace(':id', driverId);

            Swal.fire({
                title: "Are you sure?",
                text: "This will unassign the vehicle from the driver.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, unassign!",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire("Unassigned!", response.message, "success");
                            // refresh page or table
                            location.reload();
                        },
                        error: function(xhr) {
                            Swal.fire("Error!", xhr.responseJSON?.message ||
                                "Something went wrong", "error");
                        }
                    });
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
                <h4 class="mb-1">Drivers</h4>
            </div>
            <div class="d-flex align-content-center flex-wrap gap-4">

                <a href="{{ url('drivers/create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-1_5"></i>
                    New Driver Profile
                </a>
                <div class="d-flex gap-4">
                    <a class="btn btn-label-warning">Archived</a>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-datatable table-responsive p-2">
                <table class="datatables-ajax table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NAME</th>
                            <th>LICENSE NO</th>
                            <th>LICENSE EXPIRY</th>
                            <th>VEHICLE ASSIGNED</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <!--Add assign vehicle  -->
            <div class="modal fade" id="addVehicleAssignModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <form action="{{ route('drivers.assign-vehicle') }}" method="POST">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addVehicleAssignModalTitle">Vehicle Assign</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <input type="hidden" name="user_id" id="driver_id">
                                        <label for="vehicle_id" class="form-label">Select Vehicle</label>
                                        <select class="form-select select2" name="vehicle_id" id="vehicle_id" required>
                                            <option value="">Select Vehicle</option>
                                            @foreach ($unassignedvehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}">{{ $vehicle->registration_no }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="assign_date" class="form-label">Assign Date</label>
                                        <input type="date" class="form-control flatpickr" id="assign_date"
                                            name="assigned_date" required />
                                    </div>
                                    <div class="col-md-6">
                                        <label for="return_date" class="form-label">Return Date</label>
                                        <input type="date" class="form-control flatpickr" id="return_date"
                                            name="unassigned_date" />
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
    <!-- / Content -->

@endsection
