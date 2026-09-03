@extends('layouts.master')

@section('title', 'Vehicle Details')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/highlight/highlight.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script>
        function setNa(fieldId) {
            const input = document.getElementById(fieldId);
            input.value = ''; // clears the date input
        }
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
        <!-- Vehicle Details -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <form method="POST" action="{{ route('vehicles.store') }}">
            @csrf
            <div class="app-ecommerce">
                <!-- Add Vehicle -->
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
                    <div class="d-flex flex-column justify-content-center">
                        <h4 class="mb-1">Add a Vehicle</h4>
                    </div>
                    <div class="d-flex align-content-center flex-wrap gap-4">
                        <div class="d-flex gap-4">
                            <button class="btn btn-label-secondary">Discard</button>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Vehicle</button>
                    </div>
                </div>

                <div class="row">
                    <!-- First column-->
                    <div class="col-12 col-lg-8">
                        <!-- Vehicle Information -->
                        <div class="card mb-6">
                            <div class="card-header">
                                <h5 class="card-tile mb-0">Primary information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-6">
                                    <label class="form-label" for="vehicle-category">Vehicle Category</label>
                                    <select class="form-select" id="vehicle-category" name="category_id">
                                        <option value="">Select Vehicle Category</option>
                                        @foreach ($vehicleCategories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="registration-no">Registration No</label>
                                    <input type="text" class="form-control" id="registration-no"
                                        placeholder="Registration No" name="registration_no" aria-label="Registration No" />
                                </div>
                                <div class="row mb-6">
                                    <div class="col">
                                        <label class="form-label" for="serial-no">Serial No</label>
                                        <input type="text" class="form-control" id="serial-no" placeholder="Serial No"
                                            name="serial_no" aria-label="Serial No" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label" for="vin">VIN Number</label>
                                        <input type="text" class="form-control" id="vin" placeholder="VIN"
                                            name="vin_number" aria-label="VIN" />
                                    </div>
                                </div>

                                <div class="row mb-6">
                                    <div class="col">
                                        <label class="form-label" for="electronic-code">Electronic Code</label>
                                        <input type="text" class="form-control" id="electronic-code"
                                            placeholder="Electronic Code" name="electronic_code"
                                            aria-label="Electronic Code" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label" for="mechanical-code">Mechanical Code</label>
                                        <input type="text" class="form-control" id="mechanical-code"
                                            placeholder="Mechanical Code" name="mechanical_code"
                                            aria-label="Mechanical Code" />
                                    </div>
                                </div>
                                <div class="row mb-6">
                                    <div class="col">
                                        <label class="form-label" for="radio-code">Radio Code</label>
                                        <input type="text" class="form-control" id="radio-code" placeholder="Radio Code"
                                            name="radio_code" aria-label="Radio Code" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label" for="deadlock-key-duplication-code">Deadlock Key
                                            Duplication
                                            Code</label>
                                        <input type="text" class="form-control" id="deadlock-key-duplication-code"
                                            placeholder="Deadlock Key Duplication Code"
                                            name="deadlock_key_duplication_codes"
                                            aria-label="Deadlock Key Duplication Code" />
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- Vehicle Information -->
                        <!-- Vehicle Details -->
                        <div class="card mb-6">
                            <div class="card-header">
                                <h5 class="card-tile mb-0">Detailed information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-6">
                                    <div class="col">
                                        <label class="form-label" for="manufacturer">Manufacturer</label>
                                        <input type="text" class="form-control" id="manufacturer"
                                            placeholder="Audi, BMW, etc." name="manufacturer"
                                            aria-label="Manufacturer" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label" for="model">Model</label>
                                        <input type="text" class="form-control" id="model"
                                            placeholder="Xcrolla etc" name="model" aria-label="Model" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label" for="engine">Engine</label>
                                        <input type="text" class="form-control" id="engine"
                                            placeholder="2.0L TDI, 1.5L Petrol, etc." name="engine"
                                            aria-label="Engine" />
                                    </div>
                                </div>
                                <div class="row mb-6">
                                    <div class="col">
                                        <label class="form-label" for="color">Color</label>
                                        <input type="text" class="form-control" id="color"
                                            placeholder="Red, Blue, etc." name="color" aria-label="Color" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label" for="year">Year</label>
                                        <input type="text" class="form-control" id="year"
                                            placeholder="2023, 2022, etc." name="year" aria-label="Year" />
                                    </div>
                                </div>

                                <div class="row mb-6">
                                    <div class="col">
                                        <label class="form-label" for="fuel-type">Fuel Type</label>
                                        <select class="form-select" id="fuel-type" name="fuel_type">
                                            <option value="">Select Fuel Type</option>
                                            <option value="petrol">Petrol</option>
                                            <option value="diesel">Diesel</option>
                                            <option value="electric">Electric</option>
                                            <option value="hybrid">Hybrid</option>
                                            <option value="hydrogen">Hydrogen</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label class="form-label" for="mileage">Mileage</label>
                                        <input type="text" class="form-control" id="mileage"
                                            placeholder="Mileage in km" name="mileage" aria-label="Mileage" />
                                    </div>
                                </div>

                                <div class="row mb-6">
                                    <div class="col">
                                        <label class="form-label" for="front-tyre-size">Front Tyre Size </label>
                                        <input type="text" class="form-control" id="front-tyre-size"
                                            placeholder="235/65R16C etc" name="tyre_size_front"
                                            aria-label="Front Tyre Size" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label" for="rear-tyre-size">Rear Tyre Size</label>
                                        <input type="text" class="form-control" id="rear-tyre-size"
                                            placeholder="235/65R16C etc" name="tyre_size_rear"
                                            aria-label="Rear Tyre Size" />
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <!-- /Second column -->

                    <!-- Second column -->
                    <div class="col-12 col-lg-4">

                        <div class="card mb-6">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Status</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-6">
                                    <label class="form-label" for="status">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="Available">Available</option>
                                        <option value="Assigned">Assigned</option>
                                        <option value="Under Maintenance">Under Maintenance</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <!-- Notification Card -->
                        <div class="card mb-6">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Notifications</h5>
                            </div>
                            <div class="card-body">
                                <!-- Tax Date -->
                                <div class="mb-6">
                                    <label for="tax-expire-date" class="form-label">Tax Expire Date</label>
                                    <input class="form-control" type="date" id="tax-expire-date"
                                        name="tax_expiry_date" value="{{ date('Y-m-d') }}" />
                                </div>

                                <!-- MOT expiry Date -->
                                <div class="mb-6">
                                    <label for="mot-expire-date" class="form-label">MOT Expire Date</label>
                                    <input class="form-control" type="date" id="mot_expiry_date" name="mot_date"
                                        value="{{ date('Y-m-d') }}" />
                                </div>

                                <!-- Insurance Expire Date -->
                                <div class="mb-6">
                                    <label for="insurance-expire-date" class="form-label">Insurance Expire Date</label>
                                    <div class="input-group">
                                        <input class="form-control" type="date" id="insurance-expire-date"
                                            name="insurance_expiry_date" value="{{ old('insurance_expiry_date') }}" />
                                        <button type="button" class="btn btn-outline-secondary"
                                            onclick="setNa('insurance-expire-date')">N/A</button>
                                    </div>
                                </div>

                                <!-- LOLER Expire Date -->
                                <div class="mb-6">
                                    <label for="loler-expire-date" class="form-label">LOLER Expire Date</label>
                                    <div class="input-group">
                                        <input class="form-control" type="date" id="loler-expire-date"
                                            name="loler_expire_date" value="{{ old('loler_expire_date') }}" />
                                        <button type="button" class="btn btn-outline-secondary"
                                            onclick="setNa('loler-expire-date')">N/A</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- /Notification Card -->

                        <div class="card mb-6">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Service Notifications</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-6">
                                    <label for="last-service-date" class="form-label">Last Service Date</label>
                                    <div class="input-group">
                                        <input class="form-control" type="date" id="last-service-date"
                                            name="last_service_date" value="{{ old('last_service_date') }}" />
                                        <button type="button" class="btn btn-outline-secondary"
                                            onclick="setNa('last-service-date')">N/A</button>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <label for="service-due-date" class="form-label">Next Service Date</label>
                                    <div class="input-group">
                                        <input class="form-control" type="date" id="service-due-date"
                                            name="next_service_date" value="{{ old('next_service_date') }}" />
                                        <button type="button" class="btn btn-outline-secondary"
                                            onclick="setNa('service-due-date')">N/A</button>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <!-- /Second column -->
                </div>
            </div>
        </form>
    </div>
    <!-- / Content -->
@endsection
