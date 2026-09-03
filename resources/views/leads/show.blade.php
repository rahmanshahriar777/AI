@extends('layouts.master')

@section('title', 'Lead Details')

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
    <!-- <script src="{{ asset('assets/js/lead-show.js') }}"></script> -->
    <script src="{{ asset('assets/js/lead-show.js?v='.filemtime(public_path('assets/js/lead-show.js'))) }}"></script>


    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>

    <script type="text/javascript">
        $(function() {
            var table = $('.datatables-ajax').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('quotations.lead', $lead->id) }}",
                order: [[4, 'desc']],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },

                    {
                        data: 'quotation_version',
                        name: 'quotation_version',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'job_type_id',
                        name: 'job_type_id',
                        render: function(data, type, row) {
                            return row.job_type.name;
                        },
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'quotation_date',
                        name: 'quotation_date',
                        orderable: false,
                        searchable: true
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
    </script>

    <script>
        $('#quotationTemplateForm').on('submit', function(e) {
            e.preventDefault();

            var leadId = $('input[name="lead_id"]').val();
            var templateId = $('#templateSelect').val();

            if (templateId === 'Select..' || !templateId) {
                alert('Please select a template.');
                return;
            }

            // Construct the route dynamically
            var url = '/quotation/create/' + leadId + '/' + templateId;
            //window.location.href = url;

            $('#quotationCreateModal').modal('show');

            // Make the AJAX request to fetch data
            $.ajax({
                url: url, // The URL of your server-side script
                type: 'GET', // or 'POST', depending on your backend
                data: {  }, // Pass the ID as a parameter
                dataType: 'html', // Expect HTML content as the response

                beforeSend: function() {
                    // Optional: Show a loading spinner or message before the call
                    $('#quotationCreateModal .modal-body').html('<div class="spinner"></br></br></br>Loading...</br></br></br></br></div>');
                },

                success: function(response) {
                    // On success, inject the returned HTML into the modal body
                    $('#quotationCreateModal .modal-body').html(response);
                    
                    // Then, open the modal
                    //$('#myModal').modal('show'); 
                },

                error: function(xhr, status, error) {
                    // Handle any errors
                    //console.error("Error: " + error);
                    $('#quotationCreateModal .modal-body').html('<p class="text-danger">Failed to load data.</p>');
                    
                }
            });
            
        });

        $('#quotationCreateModal').on('shown.bs.modal', function (e) {
            // Code to run when the modal is fully shown
            //alert('The modal is fully displayed!');
            // Example: Focus on an input field inside the modal
            //$(this).find('#myInput').focus();
        });
    </script>

    <script>
        $(document).on('click', '.delete', function() {
            var id = $(this).data('id');

            if (confirm('Are you sure you want to delete this item?')) {
                $.ajax({
                    url: '/quotation/destroy/' + id,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        $('.datatables-ajax').DataTable().ajax.reload(); // Reload table
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message || 'An error occurred.');
                    }
                });
            }
        });
    </script>

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

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />

@endsection

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
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

        @php
            $status = $lead->lead_status;
        @endphp
        
                <div
                    class="card-header bg-label-primary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row p-4">
                    <div class="d-flex flex-column justify-content-center">
                        <span class="h5 mb-0 d-flex align-items-center flex-wrap gap-2">
                            LEAD #<span class="badge bg-label-secondary me-1 ms-2">{{ $lead->lead_name }}</span>

                            <button type="button" class="btn btn-sm btn-primary shadow-sm" onclick="triggerLeadAIAnalysis('{{ addslashes($lead->customer->name ?? '') }}', '{{ addslashes($lead->jobType->name ?? '') }}', '{{ addslashes(strip_tags($lead->description ?? '')) }}', {{ $lead->id }}, null)">
                                <i class="ti tabler-sparkles me-1"></i> AI Analyze
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" onclick="triggerEmailAIDraft('{{ addslashes($lead->customer->name ?? '') }}', '{{ addslashes($lead->customer->email ?? '') }}', '{{ addslashes(strip_tags($lead->description ?? '')) }}')">
                                <i class="ti tabler-mail-spark me-1"></i> AI Reply
                            </button>

                            @hasrole(['lead-manager', 'office-manager', 'lead-job-manager'])
                                @if ($lead->lead_status == 'inprogress')
                                    @if( isset($viewwith) && $viewwith == 'quotations')
                                        <!--  -->
                                    @else
                                        <a class="btn btn-label-info ms-2" href="{{ route('lead.show.quotations', ['id' => $lead->slug, 'viewwith' => 'quotations']) }}">
                                        GENERATE QUOTATION
                                        </a>
                                    @endif
                                @endif
                            @endhasrole
                        </span>
                    </div>
                    @hasrole(['lead-manager', 'office-manager', 'lead-job-manager'])
                        <div class="d-flex align-content-center flex-wrap gap-4">
                            <!-- Alert message area -->
                            <div id="status-alert" style="display:none;" class="alert mt-2"></div>
                            <div class="d-flex gap-4">

                                @if ($lead->lead_status == 'inprogress')
                                    @if( isset($viewwith) && $viewwith == 'quotations')
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quotationModal">
                                        Create New Quotation
                                        </button>
                                    @endif
                                @endif

                                <div class="btn-group">
                                    @php
                                        $status = $lead->lead_status;
                                        $labelClass = match ($status) {
                                            'new' => 'primary',
                                            'inprogress' => 'warning',
                                            'converted_to_job' => 'success',
                                            'rejected' =>  'danger', 
                                            'archived' => 'secondary',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <button type="button" class="btn btn-label-{{ $labelClass }} waves-effect">
                                        {{ beautify_status($status) }}
                                    </button>
                                    @if( isset($viewwith) && $viewwith == 'quotations')
                                    <!--  -->
                                    @else

                                    @if (!in_array($status, ['converted_to_job', 'rejected']))
                                        <button type="button"
                                            class="btn btn-label-{{ $labelClass }} dropdown-toggle dropdown-toggle-split waves-effect"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if ($lead->lead_status == 'new')
                                                <li>
                                                    <a class="dropdown-item waves-effect" data-status="inprogress"
                                                        data-id="{{ $lead->id }}" href="#"
                                                        onclick="updateLeadStatus({{ $lead->id }}, 'inprogress')">In
                                                        Progress</a>
                                                </li>
                                            @endif
                                            
                                            @if ($lead->lead_status == 'inprogress')
                                                <li>
                                                    <a type="button" class="dropdown-item waves-effect waves-light"
                                                        data-bs-toggle="modal" data-bs-target="#rejectleadModal">
                                                        REJECT LEAD
                                                    </a>
                                                </li>
                                            @endif

                                            <li>
                                                @if (!in_array($lead->lead_status, ['converted_to_job', 'rejected', 'archived']))
                                                    <a type="button"
                                                        class="dropdown-item waves-effect waves-light convert-to-job-btn"
                                                        data-id="{{ $lead->id }}" data-status="converted_to_job">
                                                        SEND ACCOUNTS MANAGER
                                                    </a>
                                                @endif
                                            </li>
                                        </ul>
                                    @endif

                                    @endif
                                </div>
                            </div>
                        </div>
                    
                    @elsehasrole(['enquiry-manager'])

                        <div class="d-flex align-content-center flex-wrap gap-4">
                            <!-- Alert message area -->
                            <div id="status-alert" style="display:none;" class="alert mt-2"></div>
                            <div class="d-flex gap-4">

                                <div class="btn-group">
                                    @php
                                        $labelClass = match ($status) {
                                            'new' => 'primary',
                                            'inprogress' => 'warning',
                                            'converted_to_job' => 'success',
                                            'rejected', 'archived' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <button type="button" class="btn btn-label-{{ $labelClass }} waves-effect">
                                        {{ beautify_status($status) }}
                                    </button>

                                    @if (in_array($status, ['rejected']))
                                        <button type="button"
                                            class="btn btn-label-{{ $labelClass }} dropdown-toggle dropdown-toggle-split waves-effect"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a type="button" class="dropdown-item waves-effect waves-light"
                                                    data-bs-toggle="modal" data-bs-target="#rejectEnquiryModal">
                                                    REJECT ENQUIRY
                                                </a>
                                            </li>
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>

                    @else
                        <span class="badge badge @if ($lead->lead_status == 'converted_to_job') bg-success @else bg-danger @endif bg-glow me-1 ms-2">{{ beautify_status($lead->lead_status) }}</span>

                    @endhasrole
                </div>
                <div class="card-body pt-2">

                    @if (!in_array($status, ['archived', 'rejected']))
                    <!-- Reject lead Modal -->
                    <div class="modal-onboarding modal fade animate__animated" id="rejectleadModal" tabindex="-1">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header border-0">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-0">
                                    <div class="onboarding-content mb-0">
                                        <strong class="onboarding-title text-body">Reject lead</strong>
                                        
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="form-control p-0">
                                                            <div class="border-0 pb-6" id="rejectmessage"></div>
                                                        </div>
                                                        <input type="hidden" id="rejectmessage_input" name="rejectmessage">
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
                                    <button type="button" class="btn btn-primary" onclick="return updateLeadStatus('{{ $lead->id }}', 'rejected')">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--/ Reject lead Modal -->
                    @else
                    @include('enquiries.reject')
                    @endif

                    @php
                    $viewwith_quotation_column_class = 'd-none';
                    $viewwith_lead_column_class = 'col-12 col-lg-8';
                    if( isset($viewwith) && $viewwith == 'quotations'){
                        $viewwith_quotation_column_class = 'col-12 col-lg-8';
                        $viewwith_lead_column_class = 'col-12 col-lg-4';
                    }
                    @endphp

                    <div class="row mb-6 gy-6">
                        <!-- lead column-->
                        <div class="{{ $viewwith_lead_column_class }}">
                            <!-- Enquiry Information -->
                            <div class="card mb-6">
                                <div class="card-header header-elements">
                                    <h6 class="mb-0 me-2">Customer Enquiry</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Description -->
                                    <div class="mb-6">
                                        <div class="form-control p-2">
                                            {!! $lead->enquiry !!}
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- Enquiry Information -->
                            <!-- Office Notes -->
                            <div class="card mb-6">
                                <div class="card-header header-elements">
                                    <h6 class="mb-0 me-2">Office Notes</h6>
                                    @hasrole(['lead-manager', 'office-manager', 'lead-job-manager'])
                                        @if ($lead->lead_status == 'inprogress')
                                            <div class="card-header-elements ms-auto">
                                                <div class="btn-group">
                                                    <button type="button"
                                                        class="btn btn-sm btn-warning waves-effect waves-light"
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
                                            {!! $lead->lead_description !!}
                                        </div>
                                    </div>

                                    <!-- Form with Lead description Modal -->
                                    <div class="modal-onboarding modal fade animate__animated" id="editdescriptionModal"
                                        tabindex="-1">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content text-center">
                                                <div class="modal-header border-0">
                                                    <a class="text-body-secondary close-label" href="javascript:void(0);"
                                                        data-bs-dismiss="modal">Skip Intro</a>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form id="leadForm">
                                                    @csrf
                                                    <div class="modal-body p-0">
                                                        <div class="onboarding-content mb-0">
                                                            <div class="row">
                                                                <div class="col-sm-12 col-md-12 mb-4">
                                                                    <div class="mb-6">
                                                                        <label class="mb-1">Description</label>

                                                                        <input type="hidden" name="leaddescription"
                                                                            id="leaddescription-hidden">
                                                                        <input type="hidden" name="lead_id"
                                                                            value="{{ $lead->id }}">

                                                                        <div class="form-control p-0">

                                                                            <div class="border-0 pb-6"
                                                                                id="lead-description-edit">
                                                                                {!! $lead->lead_description !!}
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
                                                        <button type="button" class="btn btn-warning"
                                                            onclick="updateleadDetails({{ $lead->id }})">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!--/ Form with Lead description Modal -->
                                </div>
                            </div>
                            <!-- Office Notes -->
                            <!-- lead Images -->
                            <div class="card mb-6">
                                <div class="card-header header-elements">
                                    <h6 class="mb-0 me-2">Site Images</h6>

                                    <div class="card-header-elements ms-auto">
                                        @hasrole(['lead-manager', 'office-manager', 'lead-job-manager'])
                                            <div class="btn-group">
                                                @if ($lead->lead_status == 'inprogress')
                                                    <!-- Image Modal -->
                                                    <button type="button"
                                                        class="btn btn-warning btn-sm waves-effect waves-light"
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
                                                @foreach ($lead->leadimages as $enim)
                                                    <div class="swiper-slide position-relative"
                                                        style="background-image: url({{ $enim->source == 's3' ? $enim->image_url : asset($enim->image_path) }})">
                                                        @hasrole(['lead-manager', 'office-manager', 'lead-job-manager'])
                                                            @if ($lead->lead_status == 'inprogress')
                                                                <div class="position-absolute top-0 end-0 p-2">
                                                                    <button class="btn btn-sm btn-danger delete-image"
                                                                        onclick="deleteLeadImage({{ $enim->id }})"
                                                                        style="z-index: 10;">
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
                                            <div class="swiper-button-next swiper-button-white"></div>
                                            <div class="swiper-button-prev swiper-button-white"></div>
                                        </div>
                                        <div class="swiper gallery-thumbs">
                                            <div class="swiper-wrapper">
                                                @foreach ($lead->leadimages as $enim)
                                                    @if ($enim->source == 's3')
                                                        <div class="swiper-slide"
                                                            style="background-image: url({{ $enim->image_url }})">
                                                            {{ $enim->image_caption }}
                                                        </div>
                                                    @else
                                                        <div class="swiper-slide"
                                                            style="background-image: url({{ asset('/') }}/{{ $enim->image_path }})">
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
                                                <form id="lead-image-upload-form" method="POST"
                                                    action="{{ route('leads.add-images') }}"
                                                    enctype="multipart/form-data">
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
                                                                            id="basic-default-upload-file"
                                                                            name="leadphoto" accept="{{ $appbrand->getAllowedFileType('IMG') }}"
                                                                            required />
                                                                        <input type="hidden" name="lead_id"
                                                                            value="{{ $lead->id }}">
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
                            <!-- lead Images -->
                            <!-- lead Notes -->
                            <div class="row mb-6 gy-6">
                                <div class="col-xl">
                                    <div class="card mb-6">
                                        <div class="card-header header-elements">
                                            <h6 class="mb-0 me-2">Notes</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="timeline mb-0">
                                                @foreach ($lead->leadnotes as $enot)
                                                    <li class="timeline-item timeline-item-transparent">
                                                        <span class="timeline-point timeline-point-primary"></span>
                                                        <div class="timeline-event">
                                                            <div class="timeline-header mb-3">
                                                                <h6 class="mb-0">{{ $enot->user->name ?? 'User' }}</h6>
                                                                @if ($lead->lead_status == 'inprogress')
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
                                            @if ($lead->lead_status == 'inprogress')
                                                <form id="leadnotesForm" style="margin-top: -10px;">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <label class="mb-1">Notes</label>

                                                        <input type="hidden" name="leadnotes" id="leadnotes-hidden">
                                                        <input type="hidden" name="lead_id"
                                                            value="{{ $lead->id }}">

                                                        <div class="form-control p-0">
                                                            <div class="lead-toolbar border-0 border-bottom">
                                                            </div>
                                                            <div class="border-0 pb-6" id="leadnotes"></div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <button type="button"
                                                            class="btn btn-primary me-4 waves-effect waves-light"
                                                            onclick="addNotes({{ $lead->id }})">
                                                            <i class="fa-solid fa-paper-plane fa-md me-1"></i> Add Note
                                                        </button>
                                                        <button type="reset"
                                                            class="btn btn-label-secondary waves-effect" onclick="clearLeadNote()">Cancel</button>
                                                    </div>
                                                </form>
                                                <div id="note-message" class="mt-2"></div>
                                            @endif

                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- lead Notes -->
                        </div>
                        <!-- lead column -->
                         @hasrole(['lead-manager', 'office-manager', 'lead-job-manager'])
                            @if ($lead->lead_status == 'inprogress')
                                @if( isset($viewwith) && $viewwith == 'quotations')
                                <div class="{{ $viewwith_quotation_column_class }}">
                                    @include('leads.quotations', [
                                    'leaddata' => $lead,
                                    'customerdata' => $customerdata,
                                    'leadid' => $lead->id,
                                    'quotationTemplates' => $quotationTemplates])
                                </div>
                                @endif
                            @endif
                        @endhasrole
                        <!-- lead location column -->
                        <div class="col-12 col-lg-4">

                            <!-- Rejected Lead -->
                            @if (in_array($status, ['archived', 'rejected']))
                            <div class="row mb-6 gy-6">
                                <div class="col-xl order-1 order-xl-0">
                                    <div class="card">
                                        <div class="card-header header-elements">
                                            <h6 class="mb-0 me-2">Rejected Notes</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                {!! $lead->lead_close_reason !!}
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Assigned To -->
                            <div class="row mb-6 gy-6">
                                <div class="col-xl order-1 order-xl-0">
                                    <div class="card">
                                        <div class="card-header header-elements">
                                            <h6 class="mb-0 me-2">Assigned To</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                @php
                                                    $assignedUsers = json_decode($lead->assigned_to, true);
                                                    $leadmen = App\Models\User::whereIn(
                                                        'id',
                                                        $assignedUsers ?? [],
                                                    )->get();
                                                @endphp

                                                @if ($leadmen->count())
                                                    @foreach ($leadmen as $leadman)
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div>
                                                                <h6 class="mb-0">{{ $leadman->name }}</h6>
                                                                <span
                                                                    class="text-body-secondary">{{ $leadman->email }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="d-flex align-items-center mb-3">
                                                        <span class="text-body-secondary">Not Assigned</span>
                                                    </div>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Attachments -->
                            <div class="row mb-6 gy-6">
                                <div class="col-xl order-2 order-xl-0">
                                    <div class="card h-100">
                                        <div class="card-header d-flex justify-content-between">
                                            <h6 class="card-title m-0 me-2 pt-1 mb-2 d-flex align-items-center">
                                                <i class="icon-base ti tabler-list-details me-3"></i> Attachments
                                            </h6>
                                            @if ($lead->lead_status == 'inprogress')
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
                                        <hr class="m-0">
                                        <div class="card-body pb-0">
                                            <ul class="timeline mb-0">

                                                @if ($lead->completedquotation)
                                                    <li class="timeline-item timeline-item-transparent">
                                                        <span class="timeline-point timeline-point-success"></span>
                                                        <div class="timeline-event">
                                                            <div class="timeline-header mb-3">
                                                                <h6 class="mb-0">Accepted Quotaion</h6>
                                                                <small class="text-body-secondary">Created on
                                                                    {{ $lead->completedquotation->created_at->format('d M Y') }}
                                                                </small>
                                                            </div>
                                                            <p class="mb-2">{{ $lead->completedquotation->remarks }}
                                                            </p>

                                                            <div class="d-flex align-items-center mb-2">
                                                                <a href="{{ route('quotations.download-pdf', $lead->completedquotation->id) }}"
                                                                    target="_blank" download class="text-decoration-none">
                                                                    <div
                                                                        class="badge bg-lighter rounded d-flex align-items-center p-2">
                                                                        <img src="{{ asset('assets/img/icons/misc/pdf.png') }}"
                                                                            alt="PDF Icon" width="15"
                                                                            class="me-2" />
                                                                        <span
                                                                            class="h6 mb-0 text-body">{{ $lead->completedquotation->quotation_version }}</span>
                                                                    </div>
                                                                </a>
                                                            </div>

                                                        </div>
                                                    </li>
                                                @endif
                                                @if ($lead->leadattachments->count() > 0)
                                                    @foreach ($lead->leadattachments as $enat)
                                                        <li class="timeline-item timeline-item-transparent">
                                                            <span class="timeline-point timeline-point-primary"></span>
                                                            <div class="timeline-event">
                                                                <div class="timeline-header mb-3">
                                                                    <h6 class="mb-0">{{ $enat->attachment_name }}
                                                                    </h6>
                                                                    @if ($lead->lead_status == 'inprogress')
                                                                        <small class="text-body-secondary">
                                                                            <span
                                                                                class="badge rounded-pill bg-label-danger">
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
                            <div class="modal fade" id="uploadAttachmentsModal" data-bs-backdrop="static"
                                tabindex="-1">
                                <div class="modal-dialog">
                                    <form id="uploadAttachmentsForm" class="modal-content" enctype="multipart/form-data"
                                        action="{{ route('leads.attachments-upload') }}">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="uploadAttachmentsModalTitle">Upload
                                                Attachments
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row g-4">
                                                <div class="col mb-0">
                                                    <div class="mb-4">
                                                        <label for="attachmentType" class="form-label">Attachment
                                                            Type</label>
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
                                                        Upload Files ({{ $appbrand->getAllowedFileType('DOC') }})
                                                    </label>
                                                    <input class="form-control" type="file" name="attachment"
                                                        id="attachmentFiles" required
                                                        accept="{{ $appbrand->getAllowedFileType('DOC') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <input type="hidden" name="leadid" value="{{ $lead->id }}">
                                            <button type="button" class="btn btn-label-secondary"
                                                data-bs-dismiss="modal">Close</button>

                                            <button type="button" class="btn btn-primary"
                                                onclick="uploadAttachments({{ $lead->id }}, this)">Save</button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                            <!--/ Attachments -->
                            
                            <!-- Site Location -->
                            <div class="row mb-6 gy-6">
                                <div class="col-xl">
                                    <div class="card mb-6">
                                        <div class="card-header header-elements">
                                            <h6 class="mb-0 me-2">Site Location</h6>
                                            <div class="card-header-elements ms-auto">
                                                @hasrole(['lead-manager', 'office-manager', 'lead-job-manager'])
                                                    <div class="btn-group">
                                                        @if ($lead->lead_status == 'inprogress')
                                                            <!-- Edit location -->
                                                            <button type="button"
                                                                class="btn btn-warning btn-sm waves-effect waves-light"
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
                                                src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google.maps_api_key') }}&q={{ $lead->leadaddress->address }}, {{ $lead->leadaddress->county }}, {{ $lead->leadaddress->postcode }}+{{ $lead->leadaddress->address }}, {{ $lead->leadaddress->county }}, {{ $lead->leadaddress->country }}"
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
                                                            <small class="text-primary text-uppercase">Site
                                                                Address</small>
                                                        </div>
                                                        <!-- <h6 class="my-50">Barry Schowalter</h6> -->
                                                        <p class="text-body mb-0">{{ $lead->leadaddress->address }},
                                                            {{ $lead->leadaddress->county }},
                                                            {{ $lead->leadaddress->postcode }}
                                                        </p>
                                                    </div>
                                                </li>
                                            </ul>

                                            <!-- Edit location Modal -->
                                            <div class="modal-onboarding modal fade animate__animated"
                                                id="editlocationModal" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content text-center">
                                                        <div class="modal-header border-0">
                                                            <a class="text-body-secondary close-label"
                                                                href="javascript:void(0);" data-bs-dismiss="modal">Skip
                                                                Intro</a>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form id="editLocationForm" accept="application/json" method="POST"
                                                            action="{{ route('leads.updateaddress', $lead->id) }}">
                                                            @csrf
                                                            <div class="modal-body p-0">
                                                                <div class="onboarding-content mb-2">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <label class="form-label"
                                                                                for="formtabs-lead-address">Address</label>
                                                                            <textarea class="form-control" id="formtabs-lead-address" name="lead_address" required>{{ $lead->leadaddress->address }}</textarea>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label"
                                                                                for="formtabs-lead-county">County</label>
                                                                            <input class="form-control"
                                                                                id="formtabs-lead-county"
                                                                                name="lead_county"
                                                                                value="{{ $lead->leadaddress->county }}"
                                                                                required />
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label"
                                                                                for="formtabs-lead-postcode">Postalcode</label>
                                                                            <input class="form-control"
                                                                                id="formtabs-lead-postcode"
                                                                                name="lead_postcode"
                                                                                value="{{ $lead->leadaddress->postcode }}"
                                                                                required />
                                                                        </div>
                                                                        <input type="hidden" name="lead_id"
                                                                            value="{{ $lead->id }}">
                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-0">
                                                                <button type="button" class="btn btn-label-secondary"
                                                                    data-bs-dismiss="modal">
                                                                    Close
                                                                </button>
                                                                <button type="submit"
                                                                    class="btn btn-success">Update</button>
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

                            <!-- Customer Details -->
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
                                                @if ($lead->lead_status == 'inprogress')
                                                    <div class="dropdown-menu dropdown-menu-end"
                                                        aria-labelledby="salesByCountryTabs">
                                                        <a class="dropdown-item waves-effect"
                                                            href="{{ route('customers.show', $lead->customer->id) }}"
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
                                                                            class="text-success text-uppercase">{{ $lead->customer->company_name }}</small>
                                                                    </div>
                                                                    <h6 class="my-50">
                                                                        <span class="fw-medium">Contact:
                                                                        </span><span>{{ $lead->customer->contact_firstname }}</span>
                                                                        <span>{{ $lead->customer->contact_lastname ?? '' }}</span>
                                                                    </h6>

                                                                    <p class="text-body mb-0">
                                                                        <span class="text-heading fw-medium">eMail:
                                                                        </span>
                                                                        <span>{{ $lead->customer->contact_email ?? '-' }}</span>
                                                                        <br>
                                                                        <span class="text-heading fw-medium">Phone:
                                                                        </span>
                                                                        <span>{{ $lead->customer->contact_phone ?? '-' }}</span>
                                                                        <br>
                                                                        <span class="text-heading fw-medium">Mobile:
                                                                        </span>
                                                                        <span>{{ $lead->customer->contact_mobile ?? '-' }}</span>
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
                                                                            @php($assetUrl = optional($lead->customer->findetails)->asset_details_url)
                                                                            <a class="btn btn-text-reddit waves-effect{{ !$assetUrl ? ' disabled' : '' }}"
                                                                                href="{{ $assetUrl ?: '#' }}"
                                                                                target="_blank"
                                                                                @if (!$assetUrl) aria-disabled="true" tabindex="-1" @endif>
                                                                                {{ !$assetUrl ? 'Add Financial Details' : 'View Financial Details' }}
                                                                            </a>
                                                                        </small>

                                                                        @if ($lead->lead_status == 'inprogress')
                                                                            <small class="text-success text-uppercase">
                                                                                <button type="button"
                                                                                    class="btn btn-warning btn-sm waves-effect waves-light"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#leaddetailseditmodal">
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
                                                        @isset($lead->customer->billingaddress)
                                                            <ul class="timeline mb-0">
                                                                <li class="timeline-item ps-6 border-dashed">
                                                                    <span
                                                                        class="timeline-indicator-advanced timeline-indicator-success border-0 shadow-none">
                                                                        <i class="icon-base ti tabler-circle-check"></i>
                                                                    </span>
                                                                    <div class="timeline-event ps-1">
                                                                        <div class="timeline-header">
                                                                            <small
                                                                                class="text-success text-uppercase">{{ $lead->customer->billingaddress->contact_firstname ?? 'N/A' }}
                                                                                {{ $lead->customer->billingaddress->contact_lastname ?? 'N/A' }}</small>
                                                                        </div>
                                                                        <p class="text-body mb-0">
                                                                            <span class="text-heading fw-medium">eMail:
                                                                            </span>
                                                                            <span>{{ $lead->customer->billingaddress->contact_email ?? 'N/A' }}</span>
                                                                            <br>
                                                                            <span class="text-heading fw-medium">Phone:
                                                                            </span>
                                                                            <span>{{ $lead->customer->billingaddress->contact_phone ?? 'N/A' }}</span>
                                                                            <br>
                                                                            <span class="text-heading fw-medium">Mobile:
                                                                            </span>
                                                                            <span>{{ $lead->customer->billingaddress->contact_mobile ?? 'N/A' }}</span>
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
                                                                            </span><span>{{ $lead->customer->billingaddress->address ?? 'N/A' }}</span>
                                                                            <br>
                                                                            <span class="fw-medium">County:
                                                                            </span><span>{{ $lead->customer->billingaddress->county ?? 'N/A' }}</span>
                                                                            <br>
                                                                            <span class="fw-medium">Postcode:
                                                                            </span><span>{{ $lead->customer->billingaddress->postcode ?? 'N/A' }}</span>
                                                                            <br>
                                                                            <span class="fw-medium">Country:
                                                                            </span><span>{{ $lead->customer->billingaddress->country ?? 'N/A' }}</span>
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

                            <!-- lead Other Info -->
                            <div class="row mb-6 gy-6">
                                <div class="col-xl">
                                    <div class="card">
                                        <form method="POST" action="{{ route('lead.otheroptions', $lead->id) }}">
                                            @csrf
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">Other Information</h6>
                                                <small class="text-body-secondary float-end">
                                                    @if ($lead->lead_status == 'inprogress')
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
                                                            <input name="lead_info[]" class="form-check-input"
                                                                type="checkbox" value="repairs" id="roofing_repairs"
                                                                @if (in_array('repairs', explode(',', $lead->lead_category))) checked @endif />
                                                            <label class="form-check-label"
                                                                for="roofing_repairs">Repairs</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input name="lead_info[]" class="form-check-input"
                                                                type="checkbox" value="overclad" id="roofing_overclad"
                                                                @if (in_array('overclad', explode(',', $lead->lead_category))) checked @endif />
                                                            <label class="form-check-label"
                                                                for="roofing_overclad">Overclad</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input name="lead_info[]" class="form-check-input"
                                                                type="checkbox" value="large_works"
                                                                id="roofing_large_works"
                                                                @if (in_array('large_works', explode(',', $lead->lead_category))) checked @endif />
                                                            <label class="form-check-label"
                                                                for="roofing_large_works">Large
                                                                Works</label>
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
                                                                @if ($lead->annual_maintenance == true) checked @endif />
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
                                                                @if ($lead->installations == true) checked @endif />
                                                            <label class="form-check-label"
                                                                for="safety_install">Install</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input name="safety_type[]" class="form-check-input"
                                                                type="checkbox" value="repairs" id="safety_repairs"
                                                                @if ($lead->repairs == true) checked @endif />
                                                            <label class="form-check-label"
                                                                for="safety_repairs">Repairs</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input name="safety_type[]" class="form-check-input"
                                                                type="checkbox" value="testing" id="safety_testing"
                                                                @if ($lead->testing == true) checked @endif />
                                                            <label class="form-check-label"
                                                                for="safety_testing">Testing</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-6">
                                                    <label class="form-check-label">Priority</label>
                                                    <div class="col mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input name="lead_priority" class="form-check-input"
                                                                type="radio" value="low" id="lead-priority-low"
                                                                @if ($lead->lead_priority == 'low') checked @endif />
                                                            <label class="form-check-label" for="lead-priority-low">
                                                                Low
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input name="lead_priority" class="form-check-input"
                                                                type="radio" value="medium" id="lead-priority-medium"
                                                                @if ($lead->lead_priority == 'medium') checked @endif />
                                                            <label class="form-check-label"
                                                                for="lead-priority-medium">Medium</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input name="lead_priority" class="form-check-input"
                                                                type="radio" value="high" id="lead-priority-high"
                                                                @if ($lead->lead_priority == 'high') checked @endif />
                                                            <label class="form-check-label" for="lead-priority-high">
                                                                High
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                            <!-- /lead Other Info -->

                        </div>
                    </div>
                    
                    <!-- Customar Financial Modal -->
                    <div class="modal fade" id="leaddetailseditmodal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel1">Update Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form accept="application/json" method="POST"
                                    action="{{ route('customer.updatefinancialdetails', $lead->customer->id) }}">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="row g-6">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <label class="col-sm-3 col-form-label text-sm-end"
                                                        for="formtabs-contact-company">Company Name</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control" name="company_name"
                                                            value="{{ $lead->customer->company_name }}" readonly />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <label class="col-sm-3 col-form-label text-sm-end"
                                                        for="formtabs-assets">Reg.
                                                        Number</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" id="formtabs-assets" name="registration_no"
                                                            class="form-control" placeholder="registration number"
                                                            value="{{ $lead->customer->findetails->registration_number ?? '' }}" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <label class="col-sm-3 col-form-label text-sm-end"
                                                        for="formtabs-asset-details-url">Asset Details URL</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" id="formtabs-asset-details-url"
                                                            name="asset_details_url" class="form-control"
                                                            placeholder="Asset Details URL"
                                                            value="{{ $lead->customer->findetails->asset_details_url ?? '' }}" />
                                                    </div>
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

    @if( isset($viewwith) && $viewwith == 'quotations' )
    <div class="modal fade" id="quotationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quotationModalTitle">Quotation Templates</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="quotationTemplateForm">
                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-4">
                                <label for="templateSelect" class="form-label">Select Template</label>
                                <select class="form-select" id="templateSelect" aria-label="Select Template"
                                    name="template_id">
                                    <option selected>Select..</option>
                                    @foreach ($quotationTemplates as $template)
                                        <option value="{{ $template->id }}">
                                            {{ $template->job_type_name }} -
                                            {{ $template->template_slug }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-success">Go</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    

    <div class="modal fade" id="quotationCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quotationCreateModalTitle">
                        <span class="h4">Create Quotation #
                                        <a href="{{ route('leads.show', $lead->slug) }}">
                                            <span
                                                class="badge bg-label-success me-1 ms-2">{{ $lead->lead_name }}</span>
                                        </a>
                                    </span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="quotationFormSubmit">Submit</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- / Content -->

@endsection
