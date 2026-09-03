@extends('layouts.master')

@section('title', 'Vehicles')

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
            let ajaxUrl = "{{ route('vehicles.index') }}";

            var table = $('.datatables-ajax').DataTable({
                processing: true,
                serverSide: true,
                ajax: ajaxUrl,

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }

                    },
                    {
                        data: 'registration_no',
                        name: 'registration_no',
                        render: function(data, type, row, meta) {
                            // Row number
                            let rowNumber = meta.row + meta.settings._iDisplayStart + 1;

                            // Status badge
                            let statusBadge = '';
                            if (row.status === 'Available') {
                                statusBadge = '<span class="badge bg-success">Available</span>';
                            } else if (row.status === 'Assigned') {
                                statusBadge = '<span class="badge bg-danger">Assigned</span>';
                            } else if (row.status === 'under-maintenance') {
                                statusBadge =
                                    '<span class="badge bg-warning text-dark">Under Maintenance</span>';
                            } else {
                                statusBadge = '<span class="badge bg-secondary">Unknown</span>';
                            }

                            // Merge row number, registration_no, and status
                            return data + ' ' + statusBadge;
                        }
                    }, // MEWPS
                    {
                        data: 'make_model_engine',
                        name: 'make_model_engine'
                    }, // manufacturer + model + engine
                    {
                        data: 'tax_due',
                        name: 'tax_due'
                    }, // from tax_expiry_date
                    {
                        data: 'loler_due',
                        name: 'loler_due'
                    }, // from loler_expire_date
                    {
                        data: 'serial_no',
                        name: 'serial_no'
                    },
                    {
                        data: 'mot_due',
                        name: 'mot_due'
                    }, // from mot_expiry_date
                    {
                        data: 'tyre_sizes',
                        name: 'tyre_sizes'
                    }, // tyre_size_front + rear
                    {
                        data: 'service_due',
                        name: 'service_due'
                    }, // from next_service_date
                    {
                        data: 'vin_number',
                        name: 'vin_number'
                    },
                    {
                        data: 'vehicle_codes',
                        name: 'vehicle_codes'
                    }, // mechanical_code + electronic_code + radio_code
                    
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
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


@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
            <div class="d-flex flex-column justify-content-center">
                <h4 class="mb-1">Vehicles</h4>
            </div>

            <div class="d-flex justify-content-end mb-3">
                <a type="button" class="btn btn-primary" href="{{ route('vehicles.create') }}">
                    <i class="icon-base ti tabler-plus"></i>
                    Add Vehicle
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-datatable table-responsive p-2">
                <table class="datatables-ajax table table-bordered" id="Vehicle-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>MEWPS</th>
                            <th>MAKE / MODEL / ENGINE</th>
                            <th>TAX</th>
                            <th>LOLER</th>
                            <th>SERIAL</th>
                            <th>MOT</th>
                            <th>TYRE SIZES</th>
                            <th>SERVICE DUE</th>
                            <th>VIN NUMBER</th>
                            <th>VEHICLE CODES</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- / Content -->

@endsection
