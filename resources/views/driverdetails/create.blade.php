@extends('layouts.master')

@section('title', 'Create Drivers Profile')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
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
        <div class="app-ecommerce">

            <form action="{{ route('drivers.store') }}" method="POST">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger mt-2">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
                    <div class="d-flex flex-column justify-content-center">
                        <h4 class="mb-1">Add a new Drivers profile</h4>
                    </div>
                    <div class="d-flex align-content-center flex-wrap gap-4">
                        <div class="d-flex gap-4">
                            <button type="submit" class="btn btn-label-success">Save</button>
                            <button class="btn btn-label-secondary">Discard</button>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-lg-12">
                        <div class="card mb-6">
                            <div class="card-header">
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success mt-2">{{ session('success') }}</div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger mt-2">{{ session('error') }}</div>
                                @endif
                                <div class="row g-3">
                                    <!-- User -->
                                    <div class="col-md-6">
                                        <label class="form-label" for="form-users">Select Staff</label>
                                        <select class="form-select select2" id="form-users" name="user_id" required>
                                            <option value="">Select Staff</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6"></div>

                                    <!-- Licence Category -->
                                    <div class="col-md-6">
                                        <label class="form-label" for="form-license-category">Licence Category</label>
                                        <select class="form-select select2" id="form-license-category"
                                            name="license_category" required>
                                            <option value="">Select License Category</option>
                                            <option value="B">B</option>
                                            <option value="C1">C1</option>
                                            <option value="HGV">HGV</option>
                                        </select>
                                    </div>

                                    <!-- Driving Licence -->
                                    <div class="col-md-6">
                                        <label class="form-label" for="form-driving-licence">Driving Licence</label>
                                        <input type="text" class="form-control" id="form-driving-licence"
                                            placeholder="Driving Licence No" name="license_no" required />
                                    </div>

                                    <!-- Licence Issue Date -->
                                    <div class="col-md-6">
                                        <label class="form-label" for="form-license-issue">Licence Issue Date</label>
                                        <input type="date" class="form-control flatpickr" id="form-license-issue"
                                            name="license_issue_date" required />
                                    </div>

                                    <!-- Licence Expiry Date -->
                                    <div class="col-md-6">
                                        <label class="form-label" for="form-license-expiry">Licence Expiry Date</label>
                                        <input type="date" class="form-control flatpickr" id="form-license-expiry"
                                            name="license_expiry" required />
                                    </div>

                                    <!-- Digital Tachograph Card -->
                                    <div class="col-md-12 d-flex align-items-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="has-digital-tachograph-card"
                                                name="digital_tacho_card" value="1">
                                            <label class="form-check-label" for="has-digital-tachograph-card">Has Digital
                                                Tachograph Card?</label>
                                        </div>
                                    </div>

                                    <!-- DBS Check -->
                                    <div class="col-md-12 d-flex align-items-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="has-dbs-check-passed"
                                                name="dbs_check_passed" value="1">
                                            <label class="form-check-label" for="has-dbs-check-passed">Has DBS check
                                                passed?</label>
                                        </div>
                                    </div>

                                    <!-- Medical Check -->
                                    <div class="col-md-12 d-flex align-items-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="has-medical-check-passed"
                                                name="medical_check_passed" value="1">
                                            <label class="form-check-label" for="has-medical-check-passed">Has Medical check
                                                passed?</label>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- / Content -->

@endsection
