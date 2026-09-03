@extends('layouts.master')

@section('title', 'Quotations')

@section('scripts')
@parent
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/quill/katex.js')}}"></script>
<script src="{{asset('assets/vendor/libs/highlight/highlight.js')}}"></script>
<script src="{{asset('assets/vendor/libs/quill/quill.js')}}"></script>
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script>
    $(function() {
        $("#sortable").sortable({
            update: function(event, ui) {
                var order = [];
                $('#sortable .ui-state-default').each(function(index) {
                    order.push({
                        id: $(this).data('id'),
                        position: index + 1
                    });
                });

                $.ajax({
                    url: "{{ route('quotationsections.reorder') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order: order
                    },
                    success: function(response) {
                        toastr.success(response.message);
                    },
                    error: function() {
                        toastr.error('Failed to update order.');
                    }
                });
            }
        });
    });
</script>
<script>
    const editors = {};
    document.addEventListener("DOMContentLoaded", function () {
        @foreach ($quotation->sections as $section)
            @if ($section->section_type == "textbox")
                const quill{{ $section->id }} = new Quill('#{{ $section->section_slug }}', {
                    theme: 'snow'
                });
                editors["{{ $section->section_slug }}"] = quill{{ $section->id }};
            @endif
        @endforeach

        // Handle Save button click
        $('.save-section-btn').on('click', function () {
            const sectionSlug = $(this).data('section');
            const sectionId = $(this).data('id');
            const editor = editors[sectionSlug];
            const html = editor.root.innerHTML.trim();
            const text = editor.getText().trim();
            const hasAttachments = $(`#hasattachment-${sectionSlug}`).is(':checked') ? 1 : 0;

            if (text === '') {
                toastr.error('Content cannot be empty.');
                return;
            }

            // AJAX request to update
            $.ajax({
                url: `/quotationsections/update/${sectionId}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    content: html,
                    has_attachments: hasAttachments
                },
                success: function (res) {
                    toastr.success(res.message);
                    $(`#modal${sectionSlug}`).modal('hide');
                    location.reload(); // Optional: refresh to show updated content
                },
                error: function (err) {
                    toastr.error('Failed to update section.');
                }
            });
        });
    });
</script>

<script>
$(document).ready(function () {
    // Trigger file input on button click
    $(document).on('click', '.trigger-file-upload', function () {
        const inputId = $(this).data('input-id');
        $(`#${inputId}`).click();
    });

    // Handle file selection and AJAX upload
    $(document).on('change', '.section-image-upload', function (e) {
        const input = this;
        const sectionId = $(input).data('section-id');
        const previewContainer = $(`#preview-${sectionId}`);
        previewContainer.empty();

        const files = input.files;
        const formData = new FormData();
        formData.append('section_id', sectionId);
        formData.append('_token', '{{ csrf_token() }}');

        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            // Append file to formData
            formData.append('attachments[]', file);

            // Preview
            const reader = new FileReader();
            reader.onload = function (e) {
                previewContainer.append(`
                    <div class="col-6 mb-2">
                        <img src="${e.target.result}" class="img-fluid border rounded" />
                    </div>
                `);
            };
            reader.readAsDataURL(file);
        }

        // Upload via AJAX
        $.ajax({
            url: `/quotationsections/upload-attachments/${sectionId}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                toastr.success(res.message);
                location.reload();
            },
            error: function () {
                toastr.error('Failed to upload images.');
            }
        });
    });
});
$(document).ready(function() {
    $('.delete-image').click(function() {
        const imageId = $(this).data('id');
        const confirmation = confirm('Are you sure you want to delete this image?');
        if (confirmation) {
            $.ajax({
                url: `/quotationsections/delete-attachments/${imageId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        window.location.reload(); // Reload the page to see the changes
                    } else {
                        toastr.error('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('Something went wrong!');
                }
            });
        }
    });
});
$(document).on('click', '.update-quotation-status', function(e) {
    e.preventDefault();

    let status = $(this).data('status');
    let quotationId = $(this).data('id');

    if (!confirm(`Are you sure you want to change the status to "${status}"?`)) {
        return; // Stop if user cancels
    }

    $.ajax({
        url: `/quotation/updatestatus/${quotationId}`,
        type: 'POST',
        data: {
            quotation_status: status,
            _token: $('meta[name="csrf-token"]').attr('content') // Make sure CSRF token is available
        },
        success: function(response) {
            toastr.success('Status updated successfully');
            location.reload();
        },
        error: function(xhr) {
            toastr.danger('Something went wrong');
        }
    });
});
</script>


@endsection

@section('styles')
@parent
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/typography.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/katex.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/editor.css')}}" />

<!-- Row Group CSS -->
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css')}}" />

@endsection

@section('content')

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-2">
        <div class="d-flex flex-column justify-content-center">
            <div class="mb-1">
                <a href="{{ route( 'quotations.show', $quotation->id) }}"
                    class="btn btn-icon btn-label-secondary waves-effect btn-sm me-2">
                    <span class="icon-base ti tabler-corner-up-left-double icon-20px"></span>
                </a>
                <span class="h5">QUOTATION FOR LEAD #{{ $quotation->lead->lead_name }} </span>
                <span class="badge bg-label-success me-1 ms-2">{{ $quotation->quotation_version }}</span>
                @hasrole('lead-manager')
                <div class="btn-group">
                    @if($quotation->status == 'draft')
                    <button type="button" class="btn btn-label-info waves-effect">{{ $quotation->status }}</button>
                    @elseif($quotation->status == 'selected')
                    <button type="button" class="btn btn-label-success waves-effect">{{ $quotation->status }}</button>
                    @elseif($quotation->status == 'rejected')
                    <button type="button" class="btn btn-label-danger waves-effect">{{ $quotation->status }}</button>
                    @endif
                    <button type="button" class="btn btn-label-primary dropdown-toggle dropdown-toggle-split waves-effect" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" style="">
                        <li><a class="dropdown-item waves-effect update-quotation-status" data-status="draft" data-id="{{ $quotation->id }}" href="#">Draft</a></li>
                        <li><a class="dropdown-item waves-effect update-quotation-status" data-status="selected" data-id="{{ $quotation->id }}" href="#">Selected</a></li>
                        <li><a class="dropdown-item waves-effect update-quotation-status" data-status="rejected" data-id="{{ $quotation->id }}" href="#">Rejected</a></li>
                    </ul>
                </div>
                @else
                <span class="badge bg-label-primary me-1 ms-2">{{ $quotation->status }}</span>
                @endrole
                <span class="justify-content-center ms-12">
                    <a href="{{ route('quotations.download-pdf', $quotation->id) }}" class="btn btn-sm btn-label-dark">Download PDF</a>
                </span>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card mb-6">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            <p class="mb-6">
                                {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('F jS Y') }}
                                <br><br>
                                {{ $quotation->customer->contact_firstname }} {{ $quotation->customer->contact_lastname }}
                                <br>
                                {{ $quotation->customer->company_name }}
                                <br>
                                @isset($quotation->customer->billingaddress)
                                <span style="text-transform: capitalize;">
                                    {{ $quotation->customer->billingaddress->address }} , {{ $quotation->customer->billingaddress->county }}
                                </span>
                                @endisset
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="mb-6">
                                <img src="{{ $appbrand->business_logo??'' }}" alt="{{ $appbrand->business_name??'Logo' }}" class="mb-4" style="max-width: 100px; max-height: 100px;" >
                                <br>
                                Tel: {{ $appbrand->business_phone??'' }}
                                <br>
                                Email: {{ $appbrand->business_email??'' }}
                                <br>
                                Website: {{ $appbrand->business_website??'' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-1">
                    <div id="sortable">
                        @foreach ( $quotation->sections as $sections )
                        <div class="ui-state-default mb-4 p-4" data-id="{{ $sections->id }}">
                            <div class="card-title header-elements">
                                <h5 class="m-0 me-2">
                                    <span class="icon-base ti tabler-arrows-move icon-20px"></span>
                                    {{ $sections->section_name }}:
                                </h5>
                                <div class="card-title-elements ms-auto">
                                    <button type="button" class="btn btn-icon btn-sm btn-warning waves-effect waves-light"
                                        data-bs-toggle="modal" data-bs-target="#modal{{ $sections->section_slug }}">
                                        <span class="icon-base ti tabler-edit icon-20px"></span>
                                    </button>

                                </div>
                            </div>

                            @if ($sections->section_type == "textbox")
                            <p class="mb-6">
                                @isset($sections->content->sample_source)
                                {!! $sections->content->sample_source !!}
                                @endisset
                            </p>
                            @endif
                            @if($sections->has_attachments)
                            <div class="row g-6">
                                <div class="col-12">
                                    <h6 class="mb-2">Attachments:</h6>
                                    <div class="input-group">
                                        <input type="file" class="form-control d-none section-image-upload" id="section-file-{{ $sections->id }}" multiple data-section-id="{{ $sections->id }}">
                                        <button class="btn btn-outline-primary waves-effect trigger-file-upload" type="button" data-input-id="section-file-{{ $sections->id }}">Add pictures</button>
                                    </div>
                                    <div class="row mt-3 preview-images" id="preview-{{ $sections->id }}"></div>
                                </div>
                            </div>

                            <div class="row mt-2 uploaded-images-{{ $sections->id }}">
                                @foreach ($sections->attachments as $saa)
                                <div class="col-md-4 col-xl-4 mb-3">
                                    <div class="card shadow-none bg-transparent border border-secondary text-secondary">
                                        <div class="card-body">
                                            <div class="p-2 d-flex justify-content-end">
                                                <button class="btn btn-sm btn-danger delete-image" data-id="{{ $saa->id }}" >
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                            <img src="{{ $saa->source == 's3' ? $saa->attachment_url : asset($saa->attachment_path) }}" alt="{{ $saa->attachment_name }}" width="100%" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                                
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    @foreach ( $quotation->sections as $sections )
                    @if ($sections->section_type == "textbox")
                    <!-- Modal -->
                    <div class="modal fade" id="modal{{ $sections->section_slug }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalCenterTitle">{{ $sections->section_name }}</h5>
                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        {{-- Hidden input to store the HTML from Quill --}}
                                        <input type="hidden" name="{{ $sections->section_slug }}" id="input-{{ $sections->section_slug }}">

                                        {{-- Div for the Quill editor --}}
                                        <div id="{{ $sections->section_slug }}" class="form-control" style="min-height: 120px;">
                                            {!! $sections->content->sample_source !!}
                                        </div>
                                    </div>
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" id="hasattachment-{{ $sections->section_slug }}" name="hasattachment" value="1" {{ $sections->has_attachments ? 'checked' : '' }}>
                                        <label class="form-check-label" for="hasattachment-{{ $sections->section_slug }}">
                                            Has Attachment?
                                        </label>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                                        Close
                                    </button>
                                    <button type="button" class="btn btn-primary save-section-btn" data-section="{{ $sections->section_slug }}" data-id="{{ $sections->id }}">
                                        Save changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>

    </div>

</div>
<!-- / Content -->

@endsection