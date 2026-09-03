@extends('layouts.master')

@section('title', 'Create Drivers Profile')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
    <!-- Page JS -->
    <script>
        $(function() {
            $('.select2').select2();
        });
    </script>
@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/tagify/tagify.css') }}" />

@endsection

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- User Sidebar -->
            <div class="col-xl-4 col-lg-5 order-1 order-md-0">
                <!-- User Card -->
                <div class="card mb-6">
                    <div class="card-body pt-12">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">

                                <div class="user-info text-center">
                                    <h5>{{ $driver->user->name }}</h5>
                                    <span
                                        class="badge bg-label-secondary">{{ $driver->user->roles->pluck('name')->first() }}</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="pb-4 border-bottom mb-4">Details</h5>
                        <div class="info-container">
                            <ul class="list-unstyled mb-6">
                                <li class="mb-2">
                                    <span class="h6">License Category:</span>
                                    <span>{{ $driver->license_category }}</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">License No:</span>
                                    <span>{{ $driver->license_no }}</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">License Issue Date:</span>
                                    <span>{{ $driver->license_issue_date }}</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">License Expiry Date:</span>
                                    <span>{{ $driver->license_expiry }}</span>
                                </li>

                                <li class="mb-2">
                                    <span class="h6">Has digital tachograph (tacho) card? :</span>
                                    <span>
                                        {{ $driver->digital_tacho_card === true ? 'Yes' : ($driver->digital_tacho_card === 0 ? 'No' : 'N/A') }}
                                    </span>
                                </li>

                                <li class="mb-2">
                                    <span class="h6">Has DBS check passed? :</span>
                                    <span>
                                        {{ $driver->dbs_check_passed === true ? 'Yes' : ($driver->dbs_check_passed === 0 ? 'No' : 'N/A') }}
                                    </span>
                                </li>

                                <li class="mb-2">
                                    <span class="h6">Has medical check passed? :</span>
                                    <span>
                                        {{ $driver->medical_check_passed === true ? 'Yes' : ($driver->medical_check_passed === 0 ? 'No' : 'N/A') }}
                                    </span>
                                </li>

                            </ul>
                            <div class="d-flex justify-content-center">
                                <a href="javascript:;" class="btn btn-primary me-4" data-bs-target="#editUser"
                                    data-bs-toggle="modal">Edit</a>
                                <a href="javascript:;" class="btn btn-label-danger suspend-user">Suspend</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /User Card -->

            </div>
            <!--/ User Sidebar -->

            <!-- User Content -->
            <div class="col-xl-8 col-lg-7 order-0 order-md-1">

                <!-- Project table -->
                <div class="card mb-6">
                    <div class="table-responsive mb-4">
                        <table class="table table-vehicledetails">
                            <thead class="border-top">
                                <tr>
                                    <th>Vehicle Code</th>
                                    <th>Assigned (From - Till)</th>
                                    <th>Notes</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vehicledetails as $vehicledetail)
                                    <tr>
                                        <td>
                                            {{ $vehicledetail->vehicle ? $vehicledetail->vehicle->registration_no : 'N/A' }}
                                        </td>
                                        <td>
                                            {{ $vehicledetail->assigned_date }} -
                                            {{ $vehicledetail->unassigned_date ?? 'Present' }}
                                        </td>
                                        <td>{{ $vehicledetail->notes ?? 'N/A' }}</td>
                                        <td>
                                            @if ($vehicledetail->status === 'active')
                                                <span class="badge bg-label-success">Active</span>
                                            @else
                                                <span class="badge bg-label-secondary">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                        </table>
                    </div>
                </div>
                <!-- /Project table -->


            </div>

        </div>


    </div>
    <!-- / Content -->

@endsection
