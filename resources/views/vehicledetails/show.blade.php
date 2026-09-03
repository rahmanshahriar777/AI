@extends('layouts.master')

@section('title', 'Vehicle Details')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/highlight/highlight.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
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

        <div class="app-ecommerce">
            <!-- Vehicle Header -->
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
                <div class="d-flex flex-column justify-content-center">
                    <h4 class="mb-1">Vehicle Details</h4>
                    <p class="text-muted mb-0">Registration No: {{ $vehicle->registration_no }}</p>
                </div>
                <div class="d-flex align-content-center flex-wrap gap-4">
                    <a href="{{ route('vehicles.index') }}" class="btn btn-label-secondary">Back</a>
                    <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="btn btn-primary">Edit Vehicle</a>
                </div>
            </div>

            <div class="row">
                <!-- First column -->
                <div class="col-12 col-lg-8">
                    <!-- Vehicle Information -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Primary Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Vehicle Category:</strong> {{ $vehicle->category->name ?? 'N/A' }}</p>
                            <p><strong>Registration No:</strong> {{ $vehicle->registration_no }}</p>
                            <p><strong>Serial No:</strong> {{ $vehicle->serial_no }}</p>
                            <p><strong>VIN Number:</strong> {{ $vehicle->vin_number }}</p>
                            <p><strong>Electronic Code:</strong> {{ $vehicle->electronic_code }}</p>
                            <p><strong>Mechanical Code:</strong> {{ $vehicle->mechanical_code }}</p>
                            <p><strong>Radio Code:</strong> {{ $vehicle->radio_code }}</p>
                            <p><strong>Deadlock Key Duplication Codes:</strong>
                                {{ $vehicle->deadlock_key_duplication_codes }}</p>
                        </div>
                    </div>

                    <!-- Detailed Information -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Detailed Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Manufacturer:</strong> {{ $vehicle->manufacturer }}</p>
                            <p><strong>Model:</strong> {{ $vehicle->model }}</p>
                            <p><strong>Engine:</strong> {{ $vehicle->engine }}</p>
                            <p><strong>Color:</strong> {{ $vehicle->color }}</p>
                            <p><strong>Year:</strong> {{ $vehicle->year }}</p>
                            <p><strong>Fuel Type:</strong> {{ ucfirst($vehicle->fuel_type) }}</p>
                            <p><strong>Mileage:</strong> {{ number_format($vehicle->mileage) }} km</p>
                            <p><strong>Tyre Size (Front):</strong> {{ $vehicle->tyre_size_front }}</p>
                            <p><strong>Tyre Size (Rear):</strong> {{ $vehicle->tyre_size_rear }}</p>
                        </div>
                    </div>
                </div>

                <!-- Second column -->
                <div class="col-12 col-lg-4">
                    <!-- Notifications -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Notifications</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Tax Expiry Date:</strong> {{ $vehicle->tax_expiry_date }}</p>
                            <p><strong>MOT Expiry Date:</strong> {{ $vehicle->mot_expiry_date }}</p>
                            <p><strong>Insurance Expiry Date:</strong> {{ $vehicle->insurance_expiry_date }}</p>
                            <p><strong>LOLER Expiry Date:</strong> {{ $vehicle->loler_expire_date }}</p>
                        </div>
                    </div>

                    <!-- Service -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Service Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Last Service Date:</strong> {{ $vehicle->last_service_date }}</p>
                            <p><strong>Next Service Date:</strong> {{ $vehicle->next_service_date }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- / Content -->
@endsection
