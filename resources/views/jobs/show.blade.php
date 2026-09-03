@extends('layouts.master')

@section('title', 'Job Details')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/highlight/highlight.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/swiper/swiper.js') }}"></script>
    <!-- Page JS -->
    <script src="{{ asset('assets/js/ui-carousel.js') }}"></script>
    <script src="{{ asset('assets/js/job-show.js?v='.filemtime(public_path('assets/js/job-show.js'))) }}"></script>
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
    <style type="text/css">
        #swiper-gallery .gallery-thumbs .swiper-slide-thumb-active {
            border: 1px solid rgba(0, 0, 0, 1) !important;
        }
    </style>
@endsection

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success mt-2">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger mt-2">{{ session('error') }}</div>
                @endif
                <div
                    class="card-header bg-label-primary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row p-4 row-gap-4">
                    <div class="d-flex flex-column justify-content-center">
                        <div class="d-flex flex-column flex-sm-row">
                            <span class="badge bg-label-dark text-uppercase pt-3">
                                {{ beautify_status($job->job_type) }} #
                            </span>
                            <span class="badge bg-label-dark text-uppercase pt-3 ms-2"
                                style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $job->job_name }}
                                @if ($job->job_title)
                                    - {{ $job->job_title }}
                                @endif
                            </span>

                            @php
                                $status = $job->job_status;
                                $labelClass = match ($status) {
                                    'new' => 'primary',
                                    'inprogress', 'on_hold' => 'info',
                                    'draft' => 'warning',
                                    'rejected', 'archived' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            @hasrole(['job-manager', 'office-manager', 'accounts-manager', 'lead-job-manager'])
                                <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalCenter">
                                    <i class="icon-base ti tabler-edit"></i>
                                </button>
                            @endhasrole
                            <!-- Modal -->
                            <div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalCenterTitle">Job Title & Type</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form id="jobTitleForm">
                                            @csrf
                                            <input type="hidden" name="job_id" value="{{ $job->id }}">
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col mb-4">
                                                        <input type="text" id="nameWithTitle" class="form-control"
                                                            placeholder="Enter Name" value="{{ $job->job_title }}"
                                                            name="job_title" required />
                                                    </div>
                                                    <div class="col mb-4">
                                                        <select id="jobType" class="form-select" name="job_type" required>
                                                            @foreach ($jobtype as $type)
                                                                <option value="{{ $type->slug }}"
                                                                    {{ $job->job_type == $type->slug ? 'selected' : '' }}>
                                                                    {{ ucfirst(str_replace('_', ' ', $type->name)) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-label-secondary"
                                                    data-bs-dismiss="modal">
                                                    Close
                                                </button>
                                                <button type="button" class="btn btn-primary"
                                                    onclick="updateJobTitle({{ $job->id }})">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    @hasrole(['job-manager', 'office-manager', 'lead-job-manager'])
                        <div class="d-flex align-content-center flex-wrap gap-4">
                            <!-- Alert message area -->
                            <div id="status-alert" style="display:none;" class="alert mt-2"></div>
                            <div class="d-flex gap-4">

                                @if (!in_array($job->job_status, ['new', 'converted_to_job', 'rejected', 'archived']))
                                    <a class="btn btn-label-success convert-to-job-btn" href="{{ route('scheduler.index') }}"
                                        target="_blank">
                                        SCHEDULER
                                    </a>
                                @endif
                                <div class="btn-group">
                                    <button type="button" class="btn btn-label-{{ $labelClass }} waves-effect">
                                        {{ beautify_status($status) }}
                                    </button>

                                    @hasrole(['job-manager', 'office-manager', 'lead-job-manager'])
                                        <button type="button"
                                            class="btn btn-label-{{ $labelClass }} dropdown-toggle dropdown-toggle-split waves-effect"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>

                                        <ul class="dropdown-menu">

                                            <li>
                                                @if ($job->job_status == 'draft')
                                                    <a class="dropdown-item waves-effect waves-light"
                                                        onclick="updateJobStatus({{ $job->id }}, 'accepted')">
                                                        Accept
                                                    </a>
                                                @endif
                                            </li>
                                            @if ($job->job_status != 'inprogress')
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                    onclick="updateJobStatus({{ $job->id }}, 'inprogress')">
                                                    IN PROGRESS
                                                </a>
                                            </li>
                                            @endif
                                            <li>
                                                <a class="dropdown-item waves-effect waves-light" data-bs-toggle="modal"
                                                    data-bs-target="#rejectjobModal">
                                                    REJECT JOB
                                                </a>
                                            </li>
                                        </ul>
                                    @endhasrole
                                </div>

                            </div>
                        </div>
                    @else
                        @hasrole(['accounts-manager'])
                            <div class="d-flex align-content-center flex-wrap gap-4">
                                <!-- Alert message area -->
                                <div id="status-alert" style="display:none;" class="alert mt-2"></div>
                                <div class="d-flex gap-4">

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-label-{{ $labelClass }} waves-effect">
                                            {{ beautify_status($status) }}
                                        </button>

                                        @if (auth()->user()->hasRole('accounts-manager') && $status !== 'accepted')
                                            <button type="button"
                                                class="btn btn-label-{{ $labelClass }} dropdown-toggle dropdown-toggle-split waves-effect"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="visually-hidden">Toggle Dropdown</span>
                                            </button>

                                            <ul class="dropdown-menu">

                                                <li>
                                                    @if ($job->job_status == 'draft')
                                                        <a class="dropdown-item waves-effect waves-light"
                                                            onclick="updateJobStatus({{ $job->id }}, 'accepted')">
                                                            Accept
                                                        </a>
                                                    @endif
                                                </li>
                                                <li>
                                                    <a class="dropdown-item waves-effect waves-light" data-bs-toggle="modal"
                                                        data-bs-target="#rejectjobModal">
                                                        REJECT job
                                                    </a>
                                                </li>

                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="badge bg-label-primary me-1 ms-2">
                                {{ beautify_status($job->job_status) }}
                            </span>
                        @endhasrole
                    @endhasrole

                </div>

                <!-- Reject mail Modal -->
                <div class="modal-onboarding modal fade animate__animated" id="rejectjobModal" tabindex="-1">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header border-0">
                                <a class="text-body-secondary close-label" href="javascript:void(0);"
                                    data-bs-dismiss="modal">Skip Intro</a>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0">
                                <div class="onboarding-content mb-0">
                                    <strong class="onboarding-title text-body">Reject job</strong>
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 mb-4">
                                            <div class="input-group">
                                                <select class="form-select" id="mailTemplateSelect"
                                                    aria-label="Choose mail template">
                                                    <option selected disabled>Choose mail template</option>
                                                    @foreach ($mailtemplates as $mt)
                                                        <option value="{{ $mt->id }}">{{ $mt->name }}</option>
                                                    @endforeach
                                                </select>

                                                <button class="btn btn-outline-primary waves-effect" type="button"
                                                    onclick="selectMailTemplate()">
                                                    Select Mail
                                                </button>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="mb-4">
                                                        <label for="sendtoemail" class="form-label">Send to</label>
                                                        <input type="email" class="form-control" id="sendtoemail"
                                                            placeholder="name@example.com" name="sendtoemail"
                                                            value="{{ $job->customer->contact_email }}">
                                                    </div>
                                                    <div class="mb-4">
                                                        <label for="mailsubject" class="form-label">Subject</label>
                                                        <input class="form-control" type="text" id="mailsubject"
                                                            aria-label="readonly input example" name="mailsubject"
                                                            value="job {{ $job->job_name }} has been rejected">
                                                    </div>
                                                    <div class="form-control p-0">
                                                        <div class="border-0 pb-6" id="mailmessage"></div>
                                                    </div>
                                                    <input type="hidden" id="mailmessage_input" name="mailmessage">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                                    Close
                                </button>
                                <button type="button" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Reject mail Modal -->

                <div class="row mb-6 gy-6">
                    <!-- job column-->
                    <div class="col-12 col-lg-8">
                        <!-- Enquiry Information -->
                        <div class="card mb-6">
                            <div class="card-header header-elements">
                                <h6 class="mb-0 me-2">Customer Enquiry</h6>
                            </div>
                            <div class="card-body">
                                <!-- Description -->
                                <div class="mb-6">
                                    <div class="form-control p-2">
                                        {!! $job->enquiry !!}
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- Enquiry Information -->
                        <!-- job Information -->
                        <div class="card mb-6">
                            <div class="card-header header-elements">
                                <h6 class="mb-0 me-2">Job Information</h6>
                                @hasrole(['job-manager', 'office-manager', 'lead-job-manager'])
                                    @if ($job->job_status == 'inprogress')
                                        <div class="card-header-elements ms-auto">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-warning waves-effect waves-light"
                                                    data-bs-toggle="modal" data-bs-target="#editdescriptionModal">
                                                    Edit
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                @endhasrole
                            </div>
                            <div class="card-body">
                                <!-- Description -->
                                <div class="mb-6">
                                    <div class="form-control p-2">
                                        {!! $job->job_description !!}
                                    </div>
                                </div>

                                <!-- Form with Image Modal -->
                                <div class="modal-onboarding modal fade animate__animated" id="editdescriptionModal"
                                    tabindex="-1">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content text-center">
                                            <div class="modal-header border-0">
                                                <a class="text-body-secondary close-label" href="javascript:void(0);"
                                                    data-bs-dismiss="modal">Skip Intro</a>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form id="jobForm">
                                                @csrf
                                                <div class="modal-body p-0">
                                                    <div class="onboarding-content mb-0">
                                                        <div class="row">
                                                            <div class="col-sm-12 col-md-12 mb-4">
                                                                <div class="mb-6">
                                                                    <label class="mb-1">Description</label>

                                                                    <input type="hidden" name="jobdescription"
                                                                        id="jobdescription-hidden">
                                                                    <input type="hidden" name="job_id"
                                                                        value="{{ $job->id }}">

                                                                    <div class="form-control p-0">
                                                                        <div class="border-0 pb-6"
                                                                            id="job-description-edit">
                                                                            {!! $job->job_description !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="button" class="btn btn-label-secondary"
                                                        data-bs-dismiss="modal">
                                                        Close
                                                    </button>
                                                    <button type="button" class="btn btn-success"
                                                        onclick="updatejobDetails({{ $job->id }})">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!--/ Form with Image Modal -->
                            </div>
                        </div>
                        <!-- job Information -->
                        <!-- job Images -->
                        <div class="card mb-6">
                            <div class="card-header header-elements">
                                <h6 class="mb-0 me-2">Job Images</h6>

                                <div class="card-header-elements ms-auto">
                                    @hasrole(['job-manager', 'office-manager', 'lead-job-manager'])
                                        <div class="btn-group">
                                            @if ($job->job_status == 'inprogress')
                                                <!-- Image Modal -->
                                                <button type="button" class="btn btn-warning waves-effect waves-light"
                                                    data-bs-toggle="modal" data-bs-target="#imageAddModal">
                                                    Add More
                                                </button>
                                                <!--/ Image Modal -->
                                            @endif
                                        </div>
                                    @endhasrole
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="swiper-gallery">
                                    <div class="swiper gallery-top">
                                        <div class="swiper-wrapper">
                                            @foreach ($job->jobimages as $enim)
                                                <div class="swiper-slide position-relative"
                                                    style="background-image: url({{ $enim->source == 's3' || $enim->source == 'public' ? $enim->image_url : asset($enim->image_path) }})">
                                                    @hasrole(['job-manager', 'office-manager', 'lead-job-manager'])
                                                        @if ($job->job_status == 'inprogress')
                                                            <div class="position-absolute top-0 end-0 p-2">
                                                                <button class="btn btn-sm btn-danger delete-image"
                                                                    data-id="{{ $enim->id }}" style="z-index: 10;">
                                                                    <i class="fa-solid fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    @endhasrole
                                                    <div
                                                        class="text-white bg-dark bg-opacity-50 p-1 position-absolute bottom-0 w-100 text-center">
                                                        {{ $enim->image_caption }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <!-- Add Arrows -->
                                        <div class="swiper-button-next swiper-button-white_notused"></div>
                                        <div class="swiper-button-prev swiper-button-white_notused"></div>
                                    </div>
                                    <div class="swiper gallery-thumbs">
                                        <div class="swiper-wrapper">
                                            @foreach ($job->jobimages as $enim)
                                                @if ($enim->source == 's3' || $enim->source == 'public')
                                                    <div class="swiper-slide"
                                                        style="background-image: url({{ $enim->image_url }}); border: 1px solid rgba(0, 0, 0, 0.4);">
                                                        {{ $enim->image_caption }}
                                                    </div>
                                                @else
                                                    <div class="swiper-slide"
                                                        style="background-image: url({{ asset('/') }}/{{ $enim->image_path }}); border: 1px solid rgba(0, 0, 0, 0.4);">
                                                        {{ $enim->image_caption }}
                                                    </div>
                                                @endif
                                            @endforeach

                                        </div>
                                    </div>
                                </div>

                                <!-- Form with Image Modal -->
                                <div class="modal-onboarding modal fade animate__animated" id="imageAddModal"
                                    tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content text-center">
                                            <div class="modal-header border-0">
                                                <a class="text-body-secondary close-label" href="javascript:void(0);"
                                                    data-bs-dismiss="modal">Skip Intro</a>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form id="job-image-upload-form" method="POST"
                                                action="{{ route('job.add-images') }}" enctype="multipart/form-data">
                                                @csrf

                                                <div class="modal-body p-0">
                                                    <div class="onboarding-media">
                                                        <div class="mx-2">
                                                            <!-- preview image -->
                                                        </div>
                                                    </div>

                                                    <div class="onboarding-content mb-0">
                                                        <div class="row">
                                                            <div class="col-sm-12 col-md-12 mb-4">
                                                                <div class="mb-4">
                                                                    <label for="basic-default-upload-file"
                                                                        class="form-label">Select Image ({{ $appbrand->getAllowedFileType('IMG') }})</label>
                                                                    <input type="file" class="form-control"
                                                                        id="basic-default-upload-file" name="jobphoto"
                                                                        accept="{{ $appbrand->getAllowedFileType('IMG') }}" required />
                                                                    <input type="hidden" name="job_id"
                                                                        value="{{ $job->id }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer border-0">
                                                    <button type="button" class="btn btn-label-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-success">Add</button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                                <!--/ Form with Image Modal -->

                            </div>
                        </div>
                        <!-- job Images -->
                        <!-- job Notes -->
                        <div class="row mb-6 gy-6">
                            <div class="col-xl">
                                <div class="card mb-6">
                                    <div class="card-header header-elements">
                                        <h6 class="mb-0 me-2">Notes</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="timeline mb-0">
                                            @foreach ($job->jobnotes as $enot)
                                                <li class="timeline-item timeline-item-transparent">
                                                    <span class="timeline-point timeline-point-primary"></span>
                                                    <div class="timeline-event">
                                                        <div class="timeline-header mb-3">
                                                            <h6 class="mb-0">{{ $enot->user->name ?? 'User' }}</h6>
                                                            @if ($job->job_status == 'inprogress')
                                                                <small class="text-body-secondary">
                                                                    <span class="badge rounded-pill bg-label-danger">
                                                                        <i class="fa-solid fa-trash delete-note"
                                                                            data-id="{{ $enot->id }}"
                                                                            style="cursor: pointer;"></i>
                                                                    </span>

                                                                </small>
                                                            @endif
                                                        </div>
                                                        <p class="mb-2">{!! $enot->note !!}</p>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                        @if ($job->job_status == 'inprogress')
                                            <form id="jobnotesForm" style="margin-top: -10px;">
                                                @csrf
                                                <div class="mb-2">
                                                    <label class="mb-1">Notes</label>

                                                    <input type="hidden" name="jobnotes" id="jobnotes-hidden">
                                                    <input type="hidden" name="job_id" value="{{ $job->id }}">

                                                    <div class="form-control p-0">
                                                        <div class="job-toolbar border-0 border-bottom">
                                                        </div>
                                                        <div class="border-0 pb-6" id="jobnotes"></div>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <button type="button"
                                                        class="btn btn-primary me-4 waves-effect waves-light"
                                                        onclick="addNotes({{ $job->id }})">
                                                        <i class="fa-solid fa-paper-plane fa-md me-1"></i> Add Note
                                                    </button>
                                                    <button type="reset"
                                                        class="btn btn-label-secondary waves-effect" onclick="clearJobNote()">Cancel</button>
                                                </div>
                                            </form>
                                            <div id="note-message" class="mt-2"></div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- job Notes -->
                    </div>
                    <!-- job column -->

                    <!-- job location column -->
                    <div class="col-12 col-lg-4">
                        <!-- job Attributes -->
                        @hasrole(['job-manager', 'office-manager', 'lead-job-manager'])
                            <div class="row mb-6 gy-6">
                                <div class="col-xl order-2 order-xl-0">
                                    <div class="card mb-6">
                                        <div class="card-header header-elements">
                                            <h5 class="mb-0 me-2">Attributes</h5>

                                            <div class="card-header-elements ms-auto">
                                                <a href="{{ url('job-add-attributes') }}/{{ $job->id }}"
                                                    class="btn btn-xs btn-primary waves-effect waves-light">
                                                    <span class="icon-base ti tabler-plus icon-xs me-1"></span>Manage
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card-body">

                                            <ul class="timeline mb-0">

                                                @foreach ($job->jobattributes as $fjobattb)
                                                    <li class="timeline-item ps-6 border-dashed">
                                                        <span
                                                            class="timeline-indicator-advanced timeline-indicator-dark border-0 shadow-none">
                                                            <i class="icon-base ti tabler-circle-check"></i>
                                                        </span>
                                                        <div class="timeline-event ps-1">
                                                            <div class="timeline-header">
                                                                <strong
                                                                    class="text-dark text-uppercase">{{ $fjobattb->attribute_value }}</strong>
                                                            </div>

                                                            @if ($fjobattb->details)
                                                                @foreach ($fjobattb->details as $fjobattbd)
                                                                    <ul class="list-unstyled mb-0">
                                                                        <li class="d-flex align-items-center mb-2">
                                                                            <i class="icon-base ti tabler-check icon-md"></i>
                                                                            <span
                                                                                class="fw-medium mx-2">{{ $fjobattbd->detail_value }}</span>
                                                                        </li>
                                                                    </ul>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endhasrole
                        <!-- job Attributes -->

                        <!-- Attachments -->
                        <div class="row mb-6 gy-6">
                            <div class="col-xl order-2 order-xl-0">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between">
                                        <h5 class="card-title m-0 me-2 pt-1 mb-2 d-flex align-items-center">
                                            <i class="icon-base ti tabler-list-details me-3"></i> Attachments
                                        </h5>
                                        @if ($job->job_status == 'draft')
                                            <div class="card-header-elements ms-auto">
                                                <button type="button"
                                                    class="btn btn-xs btn-dark waves-effect waves-light"
                                                    data-bs-toggle="modal" data-bs-target="#uploadAttachmentsModal">
                                                    <span class="icon-base ti tabler-plus icon-xs me-1"></span>Upload
                                                    Attachments
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body pb-0">
                                        <ul class="timeline mb-0">

                                            @if ($job->jobquotations)
                                                <li class="timeline-item timeline-item-transparent">
                                                    <span class="timeline-point timeline-point-success"></span>
                                                    <div class="timeline-event">
                                                        <div class="timeline-header mb-3">
                                                            <h6 class="mb-0">Accepted Quotaion</h6>
                                                            <small class="text-body-secondary">Created on
                                                                {{ $job->jobquotations->created_at->format('d M Y') }}
                                                            </small>
                                                        </div>

                                                        <div class="d-flex align-items-center mb-2">
                                                            <a href="{{ route('quotations.download-pdf', $job->jobquotations->id) }}"
                                                                target="_blank" download class="text-decoration-none">
                                                                <div
                                                                    class="badge bg-lighter rounded d-flex align-items-center p-2">
                                                                    <img src="{{ asset('assets/img/icons/misc/pdf.png') }}"
                                                                        alt="PDF Icon" width="15" class="me-2" />
                                                                    <span
                                                                        class="h6 mb-0 text-body">{{ $job->jobquotations->quotation_number }}</span>
                                                                </div>
                                                            </a>
                                                        </div>

                                                    </div>
                                                </li>
                                            @endif
                                            @if ($job->jobattachments->count() > 0)
                                                @foreach ($job->jobattachments as $enat)
                                                    <li class="timeline-item timeline-item-transparent">
                                                        <span class="timeline-point timeline-point-primary"></span>
                                                        <div class="timeline-event">
                                                            <div class="timeline-header mb-3">
                                                                <h6 class="mb-0">{{ $enat->attachment_name }}</h6>
                                                                @if ($job->job_status == 'inprogress')
                                                                    <small class="text-body-secondary">
                                                                        <span class="badge rounded-pill bg-label-danger">
                                                                            <i class="fa-solid fa-trash delete-attachment"
                                                                                data-id="{{ $enat->id }}"
                                                                                style="cursor: pointer;"></i>
                                                                        </span>
                                                                    </small>
                                                                @endif
                                                            </div>
                                                            <p class="mb-2">PO has been sent by the company</p>
                                                            <div class="d-flex align-items-center mb-2">
                                                                <a href="{{ $enat->attachment_url }}" target="_blank"
                                                                    download class="text-decoration-none">
                                                                    <div
                                                                        class="badge bg-lighter rounded d-flex align-items-center p-2">
                                                                        <img src="{{ asset('assets/img/icons/misc/pdf.png') }}"
                                                                            alt="PDF Icon" width="15"
                                                                            class="me-2" />
                                                                        <span
                                                                            class="h6 mb-0 text-body">{{ $enat->attachment_name }}</span>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            @endif

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal -->
                        <div class="modal fade" id="uploadAttachmentsModal" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog">
                                <form id="uploadAttachmentsForm" class="modal-content" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="uploadAttachmentsModalTitle">Upload Attachments</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="row g-4">
                                            <div class="col mb-0">
                                                <div class="mb-4">
                                                    <label for="attachmentType" class="form-label">Attachment Type</label>
                                                    <select class="form-select" name="attachment_type"
                                                        id="attachmentType" required>
                                                        <option value="">Select</option>
                                                        <option value="PO">PO</option>
                                                        <option value="Others">Others</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col mb-4">
                                                <label for="attachmentTitle" class="form-label">Title</label>
                                                <input type="text" name="title" id="attachmentTitle"
                                                    class="form-control" placeholder="Enter Title" required>
                                            </div>
                                        </div>

                                        <div class="row g-4">
                                            <div class="col mb-0">
                                                <label for="attachmentFiles" class="form-label">
                                                    Upload Files (.doc, .pdf, .jpg, .png)
                                                </label>
                                                <input class="form-control" type="file" name="attachment"
                                                    id="attachmentFiles" multiple
                                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <input type="hidden" name="jobid" value="{{ $job->id }}">
                                        <button type="button" class="btn btn-label-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                        <!--/ Attachments -->

                        <div class="row mb-6 gy-6">
                            <div class="col-xl">
                                <div class="card mb-6">
                                    <div class="card-header header-elements">
                                        <h6 class="mb-0 me-2">Site Location</h6>

                                        <div class="card-header-elements ms-auto">
                                            @hasrole(['job-manager', 'office-manager','lead-job-manager'])
                                                <div class="btn-group">
                                                    @if ($job->job_status == 'inprogress')
                                                        <!-- Edit location -->
                                                        <button type="button"
                                                            class="btn btn-warning waves-effect waves-light"
                                                            data-bs-toggle="modal" data-bs-target="#editlocationModal">
                                                            Edit
                                                        </button>
                                                        <!--/ Edit location -->
                                                    @endif
                                                </div>
                                            @endhasrole
                                        </div>
                                    </div>
                                    <hr class="m-0">
                                    <div class="card-img-top">
                                        <iframe width="100%" height="450" frameborder="0" style="border:0"
                                            referrerpolicy="no-referrer-when-downgrade"
                                            src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google.maps_api_key') }}&q={{ $job->jobaddress->address }}, {{ $job->jobaddress->county }}, {{ $job->jobaddress->postcode }}+{{ $job->jobaddress->address }}, {{ $job->jobaddress->county }}, {{ $job->jobaddress->country }}"
                                            allowfullscreen>
                                        </iframe>
                                    </div>
                                    <div class="card-body">
                                        <ul class="timeline mb-0">
                                            <li class="timeline-item ps-6 border-0">
                                                <span
                                                    class="timeline-indicator-advanced timeline-indicator-primary border-0 shadow-none">
                                                    <i class="icon-base ti tabler-map-pin"></i>
                                                </span>
                                                <div class="timeline-event ps-1">
                                                    <div class="timeline-header">
                                                        <small class="text-primary text-uppercase">Site Address</small>
                                                    </div>
                                                    <!-- <h6 class="my-50">Barry Schowalter</h6> -->
                                                    <p class="text-body mb-0">{{ $job->jobaddress->address }},
                                                        {{ $job->jobaddress->county }}, {{ $job->jobaddress->postcode }}
                                                    </p>
                                                </div>
                                            </li>
                                        </ul>

                                        <!-- Edit location Modal -->
                                        <div class="modal-onboarding modal fade animate__animated" id="editlocationModal"
                                            tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content text-center">
                                                    <div class="modal-header border-0">
                                                        <a class="text-body-secondary close-label"
                                                            href="javascript:void(0);" data-bs-dismiss="modal">Skip
                                                            Intro</a>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form id="editLocationForm" accept="application/json" method="POST"
                                                        action="{{ route('job.updateaddress', $job->id) }}">
                                                        @csrf
                                                        <div class="modal-body p-0">
                                                            <div class="onboarding-content mb-2">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <label class="form-label"
                                                                            for="formtabs-job-address">Address</label>
                                                                        <textarea class="form-control" id="formtabs-job-address" name="job_address" required>{{ $job->jobaddress->address }}</textarea>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <label class="form-label"
                                                                            for="formtabs-job-county">County</label>
                                                                        <input class="form-control"
                                                                            id="formtabs-job-county" name="job_county"
                                                                            value="{{ $job->jobaddress->county }}"
                                                                            required />
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <label class="form-label"
                                                                            for="formtabs-job-postcode">Postalcode</label>
                                                                        <input class="form-control"
                                                                            id="formtabs-job-postcode" name="job_postcode"
                                                                            value="{{ $job->jobaddress->postcode }}"
                                                                            required />
                                                                    </div>
                                                                    <input type="hidden" name="job_id"
                                                                        value="{{ $job->id }}">
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0">
                                                            <button type="button" class="btn btn-label-secondary"
                                                                data-bs-dismiss="modal">
                                                                Close
                                                            </button>
                                                            <button type="submit" class="btn btn-success">Update</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/ Edit location Modal -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-6 gy-6">
                            <div class="col-xl">
                                <div class="card h-100">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div class="card-title mb-0">
                                            <h6 class="mb-1">Customer details</h6>
                                        </div>
                                        <div class="dropdown">
                                            <button
                                                class="btn btn-text-secondary btn-icon rounded-pill text-body-secondary border-0 me-n1 waves-effect"
                                                type="button" id="salesByCountryTabs" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i
                                                    class="icon-base ti tabler-dots-vertical icon-22px text-body-secondary"></i>
                                            </button>
                                            @if ($job->job_status == 'inprogress')
                                                <div class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="salesByCountryTabs">
                                                    <a class="dropdown-item waves-effect"
                                                        href="{{ route('customers.show', $job->customer->id) }}"
                                                        target="_blank">Update</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="m-0">
                                    <div class="card-body p-0">
                                        <div class="nav-align-top">
                                            <ul class="nav nav-tabs nav-fill rounded-0 timeline-indicator-advanced"
                                                role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button type="button" class="nav-link active waves-effect"
                                                        role="tab" data-bs-toggle="tab"
                                                        data-bs-target="#navs-justified-details"
                                                        aria-controls="navs-justified-new" aria-selected="true">
                                                        Details
                                                    </button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button type="button" class="nav-link waves-effect" role="tab"
                                                        data-bs-toggle="tab" data-bs-target="#navs-justified-link-billing"
                                                        aria-controls="navs-justified-link-preparing"
                                                        aria-selected="false" tabindex="-1">
                                                        Billing
                                                    </button>
                                                </li>
                                            </ul>
                                            <div class="tab-content border-0 mx-1">
                                                <div class="tab-pane fade show active" id="navs-justified-details"
                                                    role="tabpanel">
                                                    <ul class="timeline mb-0">
                                                        <li class="timeline-item ps-6 border-dashed">
                                                            <span
                                                                class="timeline-indicator-advanced timeline-indicator-success border-0 shadow-none">
                                                                <i class="icon-base ti tabler-circle-check"></i>
                                                            </span>
                                                            <div class="timeline-event ps-1">
                                                                <div class="timeline-header">
                                                                    <small
                                                                        class="text-success text-uppercase">{{ $job->customer->company_name }}</small>
                                                                </div>
                                                                <h6 class="my-50">
                                                                    <span class="fw-medium">Contact:
                                                                    </span><span>{{ $job->customer->contact_firstname }}</span>
                                                                    <span>{{ $job->customer->contact_lastname ?? '' }}</span>
                                                                </h6>

                                                                <p class="text-body mb-0">
                                                                    <span class="text-heading fw-medium">eMail: </span>
                                                                    <span>{{ $job->customer->contact_email ?? '-' }}</span>
                                                                    <br>
                                                                    <span class="text-heading fw-medium">Phone: </span>
                                                                    <span>{{ $job->customer->contact_phone ?? '-' }}</span>
                                                                    <br>
                                                                    <span class="text-heading fw-medium">Mobile: </span>
                                                                    <span>{{ $job->customer->contact_mobile ?? '-' }}</span>
                                                                    <br>
                                                                </p>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                    <div class="border-1 border-light border-dashed my-4"></div>

                                                    <ul class="timeline mb-0">
                                                        <li class="timeline-item ps-6 border-dashed">
                                                            <span
                                                                class="timeline-indicator-advanced timeline-indicator-success border-0 shadow-none">
                                                                <i class="icon-base ti tabler-circle-check"></i>
                                                            </span>
                                                            <div class="timeline-event ps-1">
                                                                <div class="timeline-header">
                                                                    <small class="text-success text-uppercase">
                                                                        @php($assetUrl = optional($job->customer->findetails)->asset_details_url)
                                                                        <a class="btn btn-text-reddit waves-effect{{ !$assetUrl ? ' disabled' : '' }}"
                                                                            href="{{ $assetUrl ?: '#' }}" target="_blank"
                                                                            @if (!$assetUrl) aria-disabled="true" tabindex="-1" @endif>
                                                                            {{ !$assetUrl ? 'Add Financial Details' : 'View Financial Details' }}
                                                                        </a>
                                                                    </small>
                                                                    @if ($job->job_status == 'inprogress')
                                                                        <small class="text-success text-uppercase">
                                                                            <button type="button"
                                                                                class="btn btn-primary btn-sm waves-effect waves-light"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#jobdetailseditmodal">
                                                                                Edit
                                                                            </button>
                                                                        </small>
                                                                    @endif
                                                                </div>

                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="tab-pane fade" id="navs-justified-link-billing"
                                                    role="tabpanel">
                                                    @isset($job->customer->billingaddress)
                                                        <ul class="timeline mb-0">
                                                            <li class="timeline-item ps-6 border-dashed">
                                                                <span
                                                                    class="timeline-indicator-advanced timeline-indicator-success border-0 shadow-none">
                                                                    <i class="icon-base ti tabler-circle-check"></i>
                                                                </span>
                                                                <div class="timeline-event ps-1">
                                                                    <div class="timeline-header">
                                                                        <small
                                                                            class="text-success text-uppercase">{{ $job->customer->billingaddress->contact_firstname ?? 'N/A' }}
                                                                            {{ $job->customer->billingaddress->contact_lastname ?? 'N/A' }}</small>
                                                                    </div>
                                                                    <p class="text-body mb-0">
                                                                        <span class="text-heading fw-medium">eMail: </span>
                                                                        <span>{{ $job->customer->billingaddress->contact_email ?? 'N/A' }}</span>
                                                                        <br>
                                                                        <span class="text-heading fw-medium">Phone: </span>
                                                                        <span>{{ $job->customer->billingaddress->contact_phone ?? 'N/A' }}</span>
                                                                        <br>
                                                                        <span class="text-heading fw-medium">Mobile: </span>
                                                                        <span>{{ $job->customer->billingaddress->contact_mobile ?? 'N/A' }}</span>
                                                                        <br>
                                                                    </p>
                                                                </div>
                                                            </li>
                                                            <li class="timeline-item ps-6 border-0">
                                                                <span
                                                                    class="timeline-indicator-advanced timeline-indicator-primary border-0 shadow-none">
                                                                    <i class="icon-base ti tabler-map-pin"></i>
                                                                </span>
                                                                <div class="timeline-event ps-1">
                                                                    <div class="timeline-header">
                                                                        <small class="text-primary text-uppercase">Billing
                                                                            Address</small>
                                                                    </div>
                                                                    <h6 class="my-50">
                                                                        <span class="fw-medium">Address:
                                                                        </span><span>{{ $job->customer->billingaddress->address ?? 'N/A' }}</span>
                                                                        <br>
                                                                        <span class="fw-medium">County:
                                                                        </span><span>{{ $job->customer->billingaddress->county ?? 'N/A' }}</span>
                                                                        <br>
                                                                        <span class="fw-medium">Postcode:
                                                                        </span><span>{{ $job->customer->billingaddress->postcode ?? 'N/A' }}</span>
                                                                        <br>
                                                                        <span class="fw-medium">Country:
                                                                        </span><span>{{ $job->customer->billingaddress->country ?? 'N/A' }}</span>
                                                                        <br>
                                                                    </h6>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    @endisset
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Customar Financial Modal -->
                <div class="modal fade" id="jobdetailseditmodal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel1">Update Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form id="editFinForm" accept="application/json" method="POST"
                                action="{{ route('customer.updatefinancialdetails', $job->customer->id) }}">
                                @csrf
                                <div class="modal-body">
                                    <div class="row g-6">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <label class="col-sm-3 col-form-label text-sm-end"
                                                    for="formtabs-contact-company">Company Name</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" name="company_name"
                                                        value="{{ $job->customer->company_name }}" readonly />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12" style="display:none;">
                                            <div class="row">
                                                <label class="col-sm-3 col-form-label text-sm-end"
                                                    for="formtabs-assets">Reg.
                                                    Number</label>
                                                <div class="col-sm-6">
                                                    <input type="text" id="formtabs-assets" name="registration_no"
                                                        class="form-control" placeholder="registration number"
                                                        value="{{ $job->customer->findetails->registration_number ?? '' }}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <label class="col-sm-3 col-form-label text-sm-end"
                                                    for="formtabs-asset-url">Asset
                                                    Details URL</label>
                                                <div class="col-sm-6">
                                                    <input type="url" id="formtabs-asset-url"
                                                        name="asset_details_url" class="form-control"
                                                        placeholder="Asset Details URL"
                                                        value="{{ $job->customer->findetails->asset_details_url ?? '' }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                                        Close
                                    </button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->

@endsection
