@extends('layouts.master')

@section('title', 'Task Details')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/highlight/highlight.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/swiper/swiper.js') }}"></script>
    <!-- Page JS -->
    <script src="{{ asset('assets/js/ui-carousel.js') }}"></script>
    <script src="{{ asset('assets/js/item-show.js') }}"></script>
@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/ui-carousel.css') }}" />
@endsection

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row gy-6">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success mt-2">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger mt-2">{{ session('error') }}</div>
                @endif
                <form id="updateTaskForm" method="POST" action="{{ route('tasks.update') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="task_id" value="{{ $taskdetails->id }}">
                <div class="card h-100">
                    <div
                        class="card-header bg-label-primary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row p-4">
                        <div class="d-flex flex-column justify-content-center">
                            <span class="h5 mb-0">
                                <span class="badge bg-label-secondary me-1 ms-2">
                                    {{ $taskdetails->jobSchedule->job_name . '-' . $taskdetails->jobSchedule->job_title }}
                                </span>
                            </span>
                        </div>

                        <div class="d-flex align-content-center flex-wrap gap-4">
                            <!-- Alert message area -->
                            <div id="status-alert" style="display:none;" class="alert mt-2"></div>
                            <div class="d-flex gap-4">
                                <a href="{{ route('tasks.index') }}" class="btn btn-label-secondary">Back</a>
                                <button class="btn btn-label-primary">Update</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="row">

                            <div class="col-md-6 mb-4">
                                <div class="row g-4">
                                    <div class="col mb-0">
                                        <label for="status" class="form-label">Status: </label>
                                        <select id="status" name="status" class="form-select">
                                            <option value="assigned"
                                                {{ $taskdetails->status == 'assigned' ? 'selected' : '' }}>Assigned
                                            </option>
                                            <option value="inprogress"
                                                {{ $taskdetails->status == 'inprogress' ? 'selected' : '' }}>In Progress
                                            </option>
                                            <option value="completed"
                                                {{ $taskdetails->status == 'completed' ? 'selected' : '' }}>Completed
                                            </option>
                                            <option value="cancelled"
                                                {{ $taskdetails->status == 'cancelled' ? 'selected' : '' }}>Cancelled
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-4 mt-2">
                                    <div class="col mb-4">
                                        <label class="form-label" for="multicol-job">Instructions: </label>
                                        <div class="border p-4" id="section-block">
                                            <p>
                                                {!! $taskdetails->jobSchedule->instructions !!}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-4">
                                    <div class="col mb-0">
                                        <label for="upSelect2Info" class="form-label">staffs: </label>
                                        @foreach ($taskdetails->jobSchedule->workers as $worker)
                                            <span class="badge text-bg-info">{{ $worker->name }}</span>
                                        @endforeach
                                    </div>
                                    <div class="col mb-0">
                                        <label for="flatpickr-range" class="form-label">Date: </label>
                                        <span
                                            class="badge text-bg-info">{{ date('d M Y', strtotime($taskdetails->assigned_date)) }}</span>
                                        -
                                        <span
                                            class="badge text-bg-info">{{ date('d M Y', strtotime($taskdetails->completion_date)) }}</span>
                                    </div>
                                </div>
                                <div class="row g-4 mt-2">
                                    <div class="col mb-4">
                                        <p><strong>Description:</strong> <span
                                                id="jobDescription">{!! $taskdetails->jobSchedule->job->job_description !!}</span></p>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="row">
                                    <div class="card p-2">
                                        <div class="card-img-top">
                                            @if(config('services.google.maps_api_key'))
                                                <iframe width="100%" height="450" frameborder="0" style="border:0"
                                                    referrerpolicy="no-referrer-when-downgrade"
                                                    src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google.maps_api_key') }}&q={{ urlencode(($taskdetails->jobSchedule->job->jobaddress->address ?? '') . ', ' . ($taskdetails->jobSchedule->job->jobaddress->county ?? '') . ', ' . ($taskdetails->jobSchedule->job->jobaddress->postcode ?? '') . ', ' . ($taskdetails->jobSchedule->job->jobaddress->country ?? '')) }}"
                                                    allowfullscreen>
                                                </iframe>
                                            @else
                                                <div class="text-center p-4 bg-light text-muted">
                                                    <i class="icon-base ti tabler-map-pin fs-2 mb-2 d-block"></i>
                                                    <p class="mb-0 small">Google Maps preview requires <code>GOOGLE_MAPS_API_KEY</code>.</p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="card-body">
                                            <div class="row contact-info">
                                                <div class="col-6 mb-3">
                                                    <strong>Contact:</strong> <span id="contactName">{{ $taskdetails->jobSchedule->job->jobaddress->contact_firstname }} {{ $taskdetails->jobSchedule->job->jobaddress->contact_lastname }}</span>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <strong>Email:</strong> <span id="contactEmail">{{ $taskdetails->jobSchedule->job->jobaddress->contact_email }}</span>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <strong>Phone:</strong> <span id="contactPhone">{{ $taskdetails->jobSchedule->job->jobaddress->contact_phone }}</span>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <strong>Mobile:</strong> <span id="contactMobile">{{ $taskdetails->jobSchedule->job->jobaddress->contact_mobile }}</span>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <strong>Address:</strong> <span id="jobAddress">{{ $taskdetails->jobSchedule->job->jobaddress->address }}</span>
                                                </div>
                                            </div>
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
    </div>
    <!-- / Content -->

@endsection
