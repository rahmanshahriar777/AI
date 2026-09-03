@extends('layouts.master')

@section('title', 'Dashboard')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/cards-advance.css') }}" />
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
            <!-- Support Tracker -->
            <div class="col-4 col-md-4">
                <div class="card h-100">
                    
                    <div class="card-body row">
                        <div class="col-12 col-sm-4">
                            <div class="mt-lg-4 mt-lg-2 mb-lg-6 mb-2">
                                <h2 class="mb-0">{{ $totalVehicles }}</h2>
                                <p class="mb-0">Total Vehicles</p>
                            </div>
                            <ul class="p-0 m-0">
                                <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
                                    <div class="badge rounded bg-label-primary p-1_5">
                                        <i class="icon-base ti tabler-car icon-md"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-nowrap">Assigned Vehicles</h6>
                                        <small class="text-body-secondary">{{ $assignedVehicles }}</small>
                                    </div>
                                </li>
                                <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
                                    <div class="badge rounded bg-label-info p-1_5">
                                        <i class="icon-base ti tabler-car icon-md"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-nowrap">Available Vehicles</h6>
                                        <small class="text-body-secondary">{{ $availableVehicles }}</small>
                                    </div>
                                </li>
                                <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
                                    <div class="badge rounded bg-label-warning p-1_5">
                                        <i class="icon-base ti tabler-car icon-md"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-nowrap">Vehicles Under Maintenance</h6>
                                        <small class="text-body-secondary">{{ $underMaintenanceVehicles }}</small>
                                    </div>
                                </li>
                                <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
                                    <div class="badge rounded bg-label-dark p-1_5">
                                        <i class="icon-base ti tabler-car icon-md"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-nowrap">Inactive Vehicles</h6>
                                        <small class="text-body-secondary">{{ $inactiveVehicles }}</small>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Support Tracker -->

            <!-- Jobs Under Review -->
            <div class="col-4 col-md-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0 me-2">Assigned Vehicle's Summary</h5>
                        <button class="btn rounded-pill btn-warning waves-effect waves-light" type="button">
                            SEE ALL >>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless border-top">
                            <thead class="border-bottom">
                                <tr>
                                    <th>staff</th>
                                    <th>VEHICLE</th>
                                    <th>ASSIGNED (FROM - TO)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assignedVehiclesList as $avdl)
                                    <tr>
                                        <td class="pt-5">
                                            <p class="mb-0 text-heading">{{ $avdl->driver->name }} </p>
                                        </td>

                                        <td class="pt-5">
                                            <p class="mb-0 text-heading">{{ $avdl->vehicle->registration_no }} </p>
                                        </td>

                                        <td class="pt-5">
                                            <span
                                                class="badge bg-label-primary me-1">{{ \Carbon\Carbon::parse($avdl->assigned_date)->format('d M, Y') }}</span>
                                            to
                                            <span
                                                class="badge bg-label-danger me-1">{{ \Carbon\Carbon::parse($avdl->unassigned_date)->format('d M, Y') }}</span>
                                        </td>


                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--/ Jobs Under Review -->

        </div>
    </div>

@endsection-
