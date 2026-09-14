@extends('layouts.master')

@section('title', 'Enquiry Details')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/swiper/swiper.js') }}"></script>
    <!-- Page JS -->
    <script src="{{ asset('assets/js/ui-carousel.js') }}"></script>
    <script src="{{ asset('assets/js/enquiry-show.js?v='.filemtime(public_path('assets/js/enquiry-show.js'))) }}"></script>
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

    
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Sticky Actions -->
         @if (session('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger mt-2">{{ session('error') }}</div>
        @endif
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div
                        class="card-header bg-label-primary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <span class="h5 mb-0">
                                ENQUIRY #<span
                                    class="badge bg-label-secondary me-1 ms-2">{{ $enquiry->enquiry_name }}</span>
                            </span>
                            <button type="button" class="btn btn-sm btn-primary d-flex align-items-center gap-1 shadow-sm ms-2" id="btnAiSummarizeEnquiry" title="Analyze with AI and draft response">
                                <i class="ti tabler-sparkles fs-6"></i>
                                <span>AI Summarize &amp; Reply</span>
                            </button>
                        </div>

                        @hasrole(['lead-job-manager', 'enquiry-manager', 'office-manager'])
                            <div class="d-flex align-content-center flex-wrap gap-4">
                                <div class="d-flex gap-4">
                                    @if ($enquiry->enquiry_status == 'new' || $enquiry->enquiry_status == 'inprogress')
                                        <div class="btn-group">
                                            @php
                                                $status = $enquiry->enquiry_status;
                                                $labelClass = match ($status) {
                                                    'new' => 'primary',
                                                    'inprogress' => 'warning',
                                                    'converted_to_lead' => 'success',
                                                    'rejected', 'archived' => 'danger',
                                                    default => 'secondary',
                                                };
                                            @endphp

                                            <button type="button"
                                                class="btn btn-label-{{ $labelClass }} waves-effect waves-dark">
                                                {{ beautify_status($status) }}
                                            </button>

                                            @if (!in_array($status, ['converted_to_lead', 'rejected']))
                                                <button type="button"
                                                    class="btn btn-label-{{ $labelClass }} dropdown-toggle dropdown-toggle-split waves-effect"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>

                                                <ul class="dropdown-menu">
                                                    @if ($enquiry->enquiry_status == 'new')
                                                        <li>
                                                            <a class="dropdown-item waves-effect update-enquiry-status"
                                                                href="#" data-status="inprogress"
                                                                data-id="{{ $enquiry->id }}">
                                                                MARK AS IN PROGRESS
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <a class="dropdown-item waves-effect" data-bs-toggle="modal"
                                                            data-bs-target="#rejectEnquiryModal" data-status="reject_enquiry"
                                                            data-id="{{ $enquiry->id }}">
                                                            REJECT ENQUIRY
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item waves-effect convert-to-lead"
                                                            data-status="converted_to_lead" data-id="{{ $enquiry->id }}">
                                                            SEND to ESTIMATOR
                                                        </a>
                                                    </li>
                                                </ul>
                                            @endif
                                        </div>
                                        <!-- Lead Conversion Modal -->
                                        <div class="modal fade" id="leadConversionModal" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Select Estimator(s)</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>

                                                    <form id="leadConversionForm">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Select one or more users to assign this enquiry:</p>
                                                            <div class="row">
                                                                @foreach ($leadmanagers as $user)
                                                                    <div class="col-md-6 mb-2">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input user-checkbox"
                                                                                type="checkbox" name="users[]"
                                                                                value="{{ $user->id }}"
                                                                                id="user_{{ $user->id }}">
                                                                            <label class="form-check-label"
                                                                                for="user_{{ $user->id }}">
                                                                                {{ $user->name }} ({{ $user->email }})
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>

                                                            <!-- Hidden fields -->
                                                            <input type="hidden" name="enquiry_id" id="modalEnquiryId">
                                                            <input type="hidden" name="status" id="modalStatus">
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-label-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Send to
                                                                Estimator</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/ Lead Conversion Modal -->

                                    @else
                                        <span
                                            class="badge @if ($enquiry->enquiry_status == 'converted_to_lead') bg-success @else bg-danger @endif bg-glow me-1 ms-2">
                                            {{ beautify_status($enquiry->enquiry_status) }}
                                        </span>
                                    @endif

                                </div>
                            </div>
                        @else
                            <span
                                class="badge @if ($enquiry->enquiry_status == 'converted_to_lead') bg-success @else bg-danger @endif bg-glow me-1 ms-2">
                                {{ beautify_status($enquiry->enquiry_status) }}
                            </span>
                        @endhasrole
                    </div>
                    <div class="card-body pt-6">
                        @include('enquiries.reject')

                        <div class="row mb-6 gy-6">
                            <!-- enquiry column-->
                            <div class="col-12 col-lg-8">
                                <!-- Enquiry Information -->
                                <div class="card mb-6">
                                    <div class="card-header header-elements">
                                        <h6 class="mb-0 me-2">Customer Enquiry</h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- enquiry -->
                                        <div class="mb-6">
                                            <div class="form-control p-2">
                                                {!! $enquiry->enquiry !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Enquiry -->
                                <!-- Enquiry Description -->
                                <div class="card mb-6">
                                    <div class="card-header header-elements">
                                        <h6 class="mb-0 me-2">Office Notes</h6>
                                        @if ($enquiry->enquiry_status == 'inprogress')
                                            <div class="card-header-elements ms-auto">
                                                <div class="btn-group">
                                                    <button type="button"
                                                        class="btn btn-warning waves-effect waves-light"
                                                        data-bs-toggle="modal" data-bs-target="#editdescriptionModal">
                                                        Edit
                                                    </button>

                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <!-- Description -->
                                        <div class="mb-6">
                                            <div class="form-control p-2">
                                                {!! $enquiry->enquiry_description !!}
                                            </div>
                                        </div>

                                        <!-- Office Notes Modal -->
                                        <div class="modal-onboarding modal fade animate__animated"
                                            id="editdescriptionModal" tabindex="-1">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content text-center">
                                                    <div class="modal-header border-0">
                                                        <!-- <a class="text-body-secondary close-label"
                                                            href="javascript:void(0);" data-bs-dismiss="modal">Skip
                                                            Intro</a> -->
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form id="enquiryForm" style="margin-top: -10px;">
                                                        @csrf
                                                        <div class="modal-body p-0">
                                                            <div class="onboarding-content mb-0">
                                                                <div class="row">
                                                                    <div class="col-sm-12 col-md-12 mb-4">
                                                                        <div class="mb-6">
                                                                            <label class="mb-1">Description</label>

                                                                            <input type="hidden"
                                                                                name="enquirydescription"
                                                                                id="enquirydescription-hidden">
                                                                            <input type="hidden" name="enquiry_id"
                                                                                value="{{ $enquiry->id }}">

                                                                            <div class="form-control p-0">
                                                                                <div
                                                                                    class="enquiry-toolbar border-0 border-bottom">
                                                                                </div>
                                                                                <div class="border-0 pb-6"
                                                                                    id="enquiry-description-edit">
                                                                                    {!! $enquiry->enquiry_description !!}
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
                                                                onclick="updateEnquiryDetails({{ $enquiry->id }})">Save</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/ Office Notes Modal -->
                                    </div>
                                </div>
                                <!-- Enquiry Description -->
                                <!-- Enquiry Images -->
                                <div class="card mb-6">
                                    <div class="card-header header-elements">
                                        <h6 class="mb-0 me-2">Site Images</h6>

                                        <div class="card-header-elements ms-auto">
                                            <div class="btn-group">
                                                @if ($enquiry->enquiry_status == 'inprogress')
                                                    <!-- Image Modal -->
                                                    <button type="button"
                                                        class="btn btn-primary waves-effect waves-light"
                                                        data-bs-toggle="modal" data-bs-target="#imageAddModal">
                                                        Add More
                                                    </button>
                                                    <!--/ Image Modal -->
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if ($enquiry->enquiryimages->isEmpty())
                                            <div class="alert alert-info">
                                                No images have been added to this enquiry.
                                            </div>
                                        @else
                                            <div id="swiper-gallery">
                                                <div class="swiper gallery-top">
                                                    <div class="swiper-wrapper">
                                                        @foreach ($enquiry->enquiryimages as $enim)
                                                            <div class="swiper-slide position-relative"
                                                                style="background-image: url({{ $enim->source == 's3' || $enim->source == 'public' ? $enim->image_url : Storage::url($enim->image_path) }});">
                                                                @if ($enquiry->enquiry_status == 'inprogress')
                                                                    <div class="position-absolute top-0 end-0 p-2">
                                                                        <button class="btn btn-sm btn-danger delete-image"
                                                                            onclick="deleteEnquiryImage({{ $enim->id }})"
                                                                            style="z-index: 10;">
                                                                            <i class="fa-solid fa-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                @endif
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
                                                        @foreach ($enquiry->enquiryimages as $enim)
                                                            @if ($enim->source == 's3' || $enim->source == 'public')
                                                                <div class="swiper-slide"
                                                                    style="background-image: url({{ $enim->image_url }}); border: 1px solid rgba(0, 0, 0, 0.4);">
                                                                    {{ $enim->image_caption }}
                                                                </div>
                                                            @else
                                                                <div class="swiper-slide"
                                                                    style="background-image: url({{ Storage::url($enim->image_path) }}); border: 1px solid rgba(0, 0, 0, 0.4);">
                                                                    {{ $enim->image_caption }}
                                                                </div>
                                                            @endif
                                                        @endforeach

                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <!-- Form with Image Modal -->
                                        <div class="modal-onboarding modal fade animate__animated" id="imageAddModal"
                                            tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content text-center">
                                                    <div class="modal-header border-0">
                                                        <!-- <a class="text-body-secondary close-label"
                                                            href="javascript:void(0);" data-bs-dismiss="modal">Skip
                                                            Intro</a> -->
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form method="POST"
                                                        action="{{ route('enquiry.add-images') }}"
                                                        enctype="multipart/form-data">
                                                        @csrf

                                                        <div class="modal-body p-0">
                                                            <div class="onboarding-media">
                                                                <div class="mx-2">
                                                                    <!-- Optional illustration here -->
                                                                </div>
                                                            </div>

                                                            <div class="onboarding-content mb-0">
                                                                <div class="row">
                                                                    <div class="col-sm-12 col-md-12 mb-4">
                                                                        <div class="mb-4">
                                                                            <label for="basic-default-upload-file"
                                                                                class="form-label">Select Image</label>
                                                                            <input type="file" class="form-control"
                                                                                id="basic-default-upload-file"
                                                                                name="enquiryphoto"
                                                                                accept=".jpg, .jpeg, .png" required />
                                                                            <input type="hidden" name="enquiry_id"
                                                                                value="{{ $enquiry->id }}">
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
                                <!-- Enquiry Images -->
                                <!-- Enquiry Notes -->
                                <div class="row mb-6 gy-6">
                                    <div class="col-xl">
                                        <div class="card mb-6">
                                            <div class="card-header header-elements">
                                                <h6 class="mb-0 me-2">Make Notes</h5>
                                            </div>
                                            <div class="card-body">
                                                <ul class="timeline mb-0">
                                                    @foreach ($enquiry->enquirynotes as $enot)
                                                        <li class="timeline-item timeline-item-transparent">
                                                            <span class="timeline-point timeline-point-primary"></span>
                                                            <div class="timeline-event">
                                                                <div class="timeline-header mb-3">
                                                                    <h6 class="mb-0">{{ $enot->users->name }}</h6>
                                                                    <small class="text-body-secondary">
                                                                        <span class="badge rounded-pill bg-label-danger">
                                                                            <i class="fa-solid fa-trash delete-note"
                                                                                data-id="{{ $enot->id }}"
                                                                                style="cursor: pointer;"></i>
                                                                        </span>
                                                                    </small>
                                                                </div>
                                                                <p class="mb-2">{!! $enot->note !!}</p>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                <form id="enquirynotesForm" style="margin-top: -10px;">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <input type="hidden" name="enquirynotes"
                                                            id="enquirynotes-hidden">
                                                        <input type="hidden" name="enquiry_id"
                                                            value="{{ $enquiry->id }}">

                                                        <div class="form-control p-0">
                                                            <div class="enquiry-toolbar border-0 border-bottom">
                                                            </div>
                                                            <div class="border-0 pb-6" id="enquirynotes"></div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <button type="button"
                                                            class="btn btn-primary me-4 waves-effect waves-light"
                                                            onclick="addNotes({{ $enquiry->id }})">
                                                            <i class="fa-solid fa-paper-plane fa-md"></i> Add Note
                                                        </button>
                                                        <!-- <button type="reset"
                                                            class="btn btn-label-secondary waves-effect">Cancel</button> -->
                                                    </div>
                                                </form>
                                                <div id="note-message" class="mt-2"></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- Enquiry Notes -->
                            </div>
                            <!-- enquiry column -->

                            <!-- enquiry location column -->
                            <div class="col-12 col-lg-4">
                                <div class="row mb-6 gy-6">
                                    <div class="col-xl">
                                        <div class="card mb-6">
                                            <div class="card-header header-elements">
                                                <h6 class="mb-0 me-2">Site Location</h6>

                                                <div class="card-header-elements ms-auto">
                                                    <div class="btn-group">

                                                        @if ($enquiry->enquiry_status == 'inprogress')
                                                            <!-- Edit location -->
                                                            <button type="button"
                                                                class="btn btn-warning waves-effect waves-light"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editlocationModal">
                                                                Edit
                                                            </button>
                                                            <!--/ Edit location -->
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                            <hr class="m-0">
                                            <div class="card-img-top">
                                                <iframe width="100%" height="450" frameborder="0" style="border:0"
                                                    referrerpolicy="no-referrer-when-downgrade"
                                                    src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google.maps_api_key') }}&q={{ $enquiry->enquiryaddress->address }}, {{ $enquiry->enquiryaddress->county }}, {{ $enquiry->enquiryaddress->postcode }}+{{ $enquiry->enquiryaddress->address }}, {{ $enquiry->enquiryaddress->county }}, {{ $enquiry->enquiryaddress->country }}"
                                                    allowfullscreen>
                                                </iframe>
                                            </div>
                                            <div class="card-body ">
                                                <ul class="timeline mb-0">
                                                    <li class="timeline-item ps-6 border-0">
                                                        <span
                                                            class="timeline-indicator-advanced timeline-indicator-primary border-0 shadow-none">
                                                            <i class="icon-base ti tabler-map-pin"></i>
                                                        </span>
                                                        <div class="timeline-event ps-1">
                                                            <div class="timeline-header">
                                                                <small class="text-primary text-uppercase">Site
                                                                    Address</small>
                                                            </div>
                                                            <!-- <h6 class="my-50">Barry Schowalter</h6> -->
                                                            <p class="text-body mb-0">
                                                                {{ $enquiry->enquiryaddress->address }},
                                                                {{ $enquiry->enquiryaddress->county }},
                                                                {{ $enquiry->enquiryaddress->postcode }}</p>
                                                        </div>
                                                    </li>
                                                </ul>

                                                <!-- Edit location Modal -->
                                                <div class="modal-onboarding modal fade animate__animated"
                                                    id="editlocationModal" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content text-center">
                                                            <div class="modal-header border-0">
                                                                <!-- <a class="text-body-secondary close-label"
                                                                    href="javascript:void(0);"
                                                                    data-bs-dismiss="modal">Skip Intro</a> -->
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form accept="application/json" method="POST"
                                                                action="{{ route('enquiry.updateaddress', $enquiry->id) }}">
                                                                @csrf
                                                                <div class="modal-body p-0">
                                                                    <div class="onboarding-content mb-2">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <label class="form-label"
                                                                                    for="formtabs-enquiry-address">Address</label>
                                                                                <textarea class="form-control" id="formtabs-enquiry-address" name="enquiry_address" required>{{ $enquiry->enquiryaddress->address }}</textarea>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <label class="form-label"
                                                                                    for="formtabs-enquiry-county">County</label>
                                                                                <input class="form-control"
                                                                                    id="formtabs-enquiry-county"
                                                                                    name="enquiry_county"
                                                                                    value="{{ $enquiry->enquiryaddress->county }}"
                                                                                    required />
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <label class="form-label"
                                                                                    for="formtabs-enquiry-postcode">Postalcode</label>
                                                                                <input class="form-control"
                                                                                    id="formtabs-enquiry-postcode"
                                                                                    name="enquiry_postcode"
                                                                                    value="{{ $enquiry->enquiryaddress->postcode }}"
                                                                                    required />
                                                                            </div>
                                                                            <input type="hidden" name="enquiry_id"
                                                                                value="{{ $enquiry->id }}">
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer border-0">
                                                                    <button type="button" class="btn btn-label-secondary"
                                                                        data-bs-dismiss="modal">
                                                                        Close
                                                                    </button>
                                                                    <button type="submit"
                                                                        class="btn btn-warning">Update</button>
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
                                                @if ($enquiry->enquiry_status == 'inprogress')
                                                    <div class="dropdown">
                                                        <button
                                                            class="btn btn-text-secondary btn-icon rounded-pill text-body-secondary border-0 me-n1 waves-effect"
                                                            type="button" id="salesByCountryTabs"
                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <i
                                                                class="icon-base ti tabler-dots-vertical icon-22px text-body-secondary"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end"
                                                            aria-labelledby="salesByCountryTabs">
                                                            <a class="dropdown-item waves-effect waves-light"
                                                                href="{{ route('customers.show', $enquiry->customer->id) }}"
                                                                target="_blank">Update</a>
                                                        </div>
                                                    </div>
                                                @endif
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
                                                            <button type="button" class="nav-link waves-effect"
                                                                role="tab" data-bs-toggle="tab"
                                                                data-bs-target="#navs-justified-link-billing"
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
                                                                                class="text-success text-uppercase">{{ $enquiry->customer->company_name }}</small>
                                                                        </div>
                                                                        <h6 class="my-50">
                                                                            <span class="fw-medium">Contact:
                                                                            </span><span>{{ $enquiry->customer->contact_firstname }}</span>
                                                                            <span>{{ $enquiry->customer->contact_lastname }}</span>
                                                                        </h6>

                                                                        <p class="text-body mb-0">
                                                                            <span class="text-heading fw-medium">eMail:
                                                                            </span>
                                                                            <span>{{ $enquiry->customer->contact_email }}</span>
                                                                            <br>
                                                                            <span class="text-heading fw-medium">Phone:
                                                                            </span>
                                                                            <span>{{ trim(optional($enquiry->customer)->contact_phone) ?: 'N/A' }}</span>
                                                                            <br>
                                                                            <span class="text-heading fw-medium">Mobile:
                                                                            </span>
                                                                            <span>{{ trim(optional($enquiry->customer)->contact_mobile) ?: 'N/A' }}</span>
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
                                                                                @php($assetUrl = optional($enquiry->customer->findetails)->asset_details_url)
                                                                                <a class="btn btn-text-reddit waves-effect{{ !$assetUrl ? ' disabled' : '' }}"
                                                                                    href="{{ $assetUrl ?: '#' }}"
                                                                                    target="_blank"
                                                                                    @if (!$assetUrl) aria-disabled="true" tabindex="-1" @endif>
                                                                                    {{ !$assetUrl ? 'Add Financial Details' : 'View Financial Details' }}
                                                                                </a>
                                                                            </small>
                                                                            @if ($enquiry->enquiry_status == 'inprogress')
                                                                                <small class="text-success text-uppercase">
                                                                                    <button type="button"
                                                                                        class="btn btn-warning btn-sm waves-effect waves-light"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#enquirydetailseditmodal">
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
                                                            @isset($enquiry->customer->billingaddress)
                                                                <ul class="timeline mb-0">
                                                                    <li class="timeline-item ps-6 border-dashed">
                                                                        <span
                                                                            class="timeline-indicator-advanced timeline-indicator-success border-0 shadow-none">
                                                                            <i class="icon-base ti tabler-circle-check"></i>
                                                                        </span>
                                                                        <div class="timeline-event ps-1">
                                                                            <div class="timeline-header">
                                                                                <small
                                                                                    class="text-success text-uppercase">{{ $enquiry->customer->billingaddress->contact_firstname ?? 'N/A' }}
                                                                                    {{ $enquiry->customer->billingaddress->contact_lastname ?? 'N/A' }}</small>
                                                                            </div>
                                                                            <p class="text-body mb-0">
                                                                                <span class="text-heading fw-medium">eMail:
                                                                                </span>
                                                                                <span>{{ $enquiry->customer->billingaddress->contact_email ?? 'N/A' }}</span>
                                                                                <br>
                                                                                <span class="text-heading fw-medium">Phone:
                                                                                </span>
                                                                                <span>{{ $enquiry->customer->billingaddress->contact_phone ?? 'N/A' }}</span>
                                                                                <br>
                                                                                <span class="text-heading fw-medium">Mobile:
                                                                                </span>
                                                                                <span>{{ $enquiry->customer->billingaddress->contact_mobile ?? 'N/A' }}</span>
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
                                                                                <small
                                                                                    class="text-primary text-uppercase">Billing
                                                                                    Address</small>
                                                                            </div>
                                                                            <h6 class="my-50">
                                                                                <span class="fw-medium">Address:
                                                                                </span><span>{{ $enquiry->customer->billingaddress->address ?? 'N/A' }}</span>
                                                                                <br>
                                                                                <span class="fw-medium">County:
                                                                                </span><span>{{ $enquiry->customer->billingaddress->county ?? 'N/A' }}</span>
                                                                                <br>
                                                                                <span class="fw-medium">Postcode:
                                                                                </span><span>{{ $enquiry->customer->billingaddress->postcode ?? 'N/A' }}</span>
                                                                                <br>
                                                                                <span class="fw-medium">Country:
                                                                                </span><span>{{ $enquiry->customer->billingaddress->country ?? 'N/A' }}</span>
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
                                <!-- Enquiry Other Info -->
                                <div class="row mb-6 gy-6">
                                    <div class="col-xl">
                                        <div class="card">
                                            <form method="POST"
                                                action="{{ route('enquiry.otheroptions', $enquiry->id) }}">
                                                @csrf
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0">Other Information</h6>
                                                    <small class="text-body-secondary float-end">
                                                        @if ($enquiry->enquiry_status == 'inprogress')
                                                            <button type="submit"
                                                                class="btn btn-primary btn-sm waves-effect waves-light">
                                                                Update
                                                            </button>
                                                        @endif
                                                    </small>
                                                </div>
                                                <hr class="m-0">
                                                <div class="card-body">
                                                    <div class="mb-6">
                                                        <label class="form-check-label">Roofing</label>
                                                        <div class="col mt-2">
                                                            <div class="form-check form-check-inline">
                                                                <input name="enquiry_info[]" class="form-check-input"
                                                                    type="checkbox" value="repairs" id="roofing_repairs"
                                                                    @if (in_array('repairs', explode(',', $enquiry->enquiry_category))) checked @endif />
                                                                <label class="form-check-label"
                                                                    for="roofing_repairs">Repairs</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input name="enquiry_info[]" class="form-check-input"
                                                                    type="checkbox" value="overclad"
                                                                    id="roofing_overclad"
                                                                    @if (in_array('overclad', explode(',', $enquiry->enquiry_category))) checked @endif />
                                                                <label class="form-check-label"
                                                                    for="roofing_overclad">Overclad</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input name="enquiry_info[]" class="form-check-input"
                                                                    type="checkbox" value="large_works"
                                                                    id="roofing_large_works"
                                                                    @if (in_array('large_works', explode(',', $enquiry->enquiry_category))) checked @endif />
                                                                <label class="form-check-label"
                                                                    for="roofing_large_works">Large Works</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-6">
                                                        <label class="form-check-label">Annual Maintenance</label>
                                                        <div class="col mt-2">
                                                            <div class="form-check form-check-inline">
                                                                <input name="annual_maintenance" class="form-check-input"
                                                                    type="checkbox" value="1"
                                                                    id="annual_maintenance_yes"
                                                                    @if ($enquiry->annual_maintenance == true) checked @endif />
                                                                <label class="form-check-label"
                                                                    for="annual_maintenance_yes">Yes</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-6">
                                                        <label class="form-check-label">Safety</label>
                                                        <div class="col mt-2">
                                                            <div class="form-check form-check-inline">
                                                                <input name="safety_type[]" class="form-check-input"
                                                                    type="checkbox" value="install" id="safety_install"
                                                                    @if ($enquiry->installations == true) checked @endif />
                                                                <label class="form-check-label"
                                                                    for="safety_install">Install</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input name="safety_type[]" class="form-check-input"
                                                                    type="checkbox" value="repairs" id="safety_repairs"
                                                                    @if ($enquiry->repairs == true) checked @endif />
                                                                <label class="form-check-label"
                                                                    for="safety_repairs">Repairs</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input name="safety_type[]" class="form-check-input"
                                                                    type="checkbox" value="testing" id="safety_testing"
                                                                    @if ($enquiry->testing == true) checked @endif />
                                                                <label class="form-check-label"
                                                                    for="safety_testing">Testing</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mb-6">
                                                        <label class="form-check-label">Priority</label>
                                                        <div class="col mt-2">
                                                            <div class="form-check form-check-inline">
                                                                <input name="enquiry_priority" class="form-check-input"
                                                                    type="radio" value="low"
                                                                    id="enquiry-priority-low"
                                                                    @if ($enquiry->enquiry_priority == 'low') checked @endif />
                                                                <label class="form-check-label"
                                                                    for="enquiry-priority-low"> Low </label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input name="enquiry_priority" class="form-check-input"
                                                                    type="radio" value="medium"
                                                                    id="enquiry-priority-medium"
                                                                    @if ($enquiry->enquiry_priority == 'medium') checked @endif />
                                                                <label class="form-check-label"
                                                                    for="enquiry-priority-medium">Medium</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input name="enquiry_priority" class="form-check-input"
                                                                    type="radio" value="high"
                                                                    id="enquiry-priority-high"
                                                                    @if ($enquiry->enquiry_priority == 'high') checked @endif />
                                                                <label class="form-check-label"
                                                                    for="enquiry-priority-high">
                                                                    High
                                                                </label>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="col-12 mb-6">
                                                        <label class="form-label" for="collapsible-source">Source</label>
                                                        <textarea name="enquiry_source" class="form-control" id="collapsible-source" rows="2"
                                                            placeholder="{{ $appbrand->business_website }}">{{ $enquiry->enquiry_source }}</textarea>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                                <!-- /Enquiry Other Info -->
                            </div>
                        </div>
                        <!-- Customar Financial Modal -->
                        <div class="modal fade" id="enquirydetailseditmodal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel1">Update Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form id="editFinForm" accept="application/json" method="POST"
                                        action="{{ route('customer.updatefinancialdetails', $enquiry->customer->id) }}">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="row g-6">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end"
                                                            for="formtabs-contact-company">Company Name</label>
                                                        <div class="col-sm-6">
                                                            <input type="text" class="form-control"
                                                                name="company_name"
                                                                value="{{ $enquiry->customer->company_name }}" readonly />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" style="display: none;">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end"
                                                            for="formtabs-assets">Reg.
                                                            Number</label>
                                                        <div class="col-sm-6">
                                                            <input type="text" id="formtabs-assets"
                                                                name="registration_no" class="form-control"
                                                                placeholder="registration number"
                                                                value="{{ $enquiry->customer->findetails->registration_number ?? '' }}" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end"
                                                            for="formtabs-asset-url">Asset
                                                            URL</label>
                                                        <div class="col-sm-6">
                                                            <input type="url" id="formtabs-asset-url"
                                                                name="asset_details_url" class="form-control"
                                                                placeholder="https://example.com/financial-details"
                                                                value="{{ $enquiry->customer->findetails->asset_details_url ?? '' }}" />
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-label-secondary"
                                                        data-bs-dismiss="modal">
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
        </div>
        <!-- /Sticky Actions -->
    </div>
    <!-- / Content -->

    <!-- AI Enquiry Summarize Modal -->
    <div class="modal fade" id="aiEnquiryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-label-primary py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ti tabler-sparkles fs-4 text-primary"></i>
                        <h5 class="modal-title fw-bold mb-0">AI Enquiry Analysis &amp; Draft Reply</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="ai-enquiry-loading" class="text-center py-5">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <div class="fw-semibold">AI is analyzing client enquiry and drafting professional response...</div>
                        <small class="text-muted">Powered by Gemini &amp; NeoERP live intelligence</small>
                    </div>
                    <div id="ai-enquiry-content" class="d-none">
                        <div class="p-3 bg-light rounded-3 border mb-3" id="ai-enquiry-text" style="white-space: pre-wrap; font-family: inherit; font-size: 0.95rem; line-height: 1.6;"></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted" id="ai-enquiry-meta"></small>
                            <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" id="copyEnquiryAiBtn">
                                <i class="ti tabler-copy fs-6"></i> Copy Text
                            </button>
                        </div>
                    </div>
                    <div id="ai-enquiry-error" class="alert alert-danger d-none my-3"></div>
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('btnAiSummarizeEnquiry');
        if (btn) {
            btn.addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('aiEnquiryModal'));
                modal.show();

                const loadingEl = document.getElementById('ai-enquiry-loading');
                const contentEl = document.getElementById('ai-enquiry-content');
                const textEl = document.getElementById('ai-enquiry-text');
                const metaEl = document.getElementById('ai-enquiry-meta');
                const errorEl = document.getElementById('ai-enquiry-error');

                loadingEl.classList.remove('d-none');
                contentEl.classList.add('d-none');
                errorEl.classList.add('d-none');

                fetch('{{ route("ai.summarize.enquiry", ["id" => $enquiry->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    loadingEl.classList.add('d-none');
                    if (data.success && data.text) {
                        contentEl.classList.remove('d-none');
                        textEl.textContent = data.text;
                        metaEl.textContent = `Completed in ${Math.round(data.latencyMs)}ms`;
                    } else {
                        errorEl.classList.remove('d-none');
                        errorEl.textContent = data.errorMessage || 'Failed to generate AI response.';
                    }
                })
                .catch(err => {
                    loadingEl.classList.add('d-none');
                    errorEl.classList.remove('d-none');
                    errorEl.textContent = 'Network or server error during AI generation.';
                });
            });
        }

        const copyBtn = document.getElementById('copyEnquiryAiBtn');
        if (copyBtn) {
            copyBtn.addEventListener('click', function() {
                const text = document.getElementById('ai-enquiry-text').textContent;
                navigator.clipboard.writeText(text).then(() => {
                    copyBtn.innerHTML = '<i class="ti tabler-check fs-6"></i> Copied!';
                    setTimeout(() => {
                        copyBtn.innerHTML = '<i class="ti tabler-copy fs-6"></i> Copy Text';
                    }, 2000);
                });
            });
        }
    });
    </script>
@endsection
