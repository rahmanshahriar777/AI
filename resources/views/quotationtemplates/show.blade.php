@extends('layouts.master')

@section('title', 'Quotation Template')

@section('scripts')
@parent
<script src="{{asset('assets/vendor/libs/quill/katex.js')}}"></script>
<script src="{{asset('assets/vendor/libs/highlight/highlight.js')}}"></script>
<script src="{{asset('assets/vendor/libs/quill/quill.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<!-- Vendors JS -->
<script src="{{asset('assets/vendor/libs/swiper/swiper.js')}}"></script>
<!-- Page JS -->
<script src="{{asset('assets/js/ui-carousel.js')}}"></script>
<!-- Page JS -->
<script>
    const fullToolbar = [
        [{
            header: [1, 2, false]
        }],
        ['bold', 'italic', 'underline'],
        ['code-block', 'blockquote'],
        [{
            'list': 'ordered'
        }, {
            'list': 'bullet'
        }],
        ['link', 'image'],
        ['clean']
    ];

    $(document).ready(function() {
        @foreach($quotationTemplate->sections as $qts)
        @if($qts->section_type == 'textbox')
        const quill_{{$qts->id}} = new Quill('#{{ $qts->section_slug }}', {
            placeholder: 'Type here...',
            modules: {
                syntax: true,
                toolbar: fullToolbar
            },
            theme: 'snow'
        });

        // Sync content before form submission
        $('form').on('submit', function() {
            $('#input-{{ $qts->section_slug }}').val(quill_{{$qts->id}}.root.innerHTML);
        });
        @endif
        @endforeach
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sectionForm = document.getElementById('QuotationTemplateSectionForm');

        sectionForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(sectionForm);

            fetch(sectionForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': sectionForm.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    toastr.success(data.message || 'Section added successfully');
                    sectionForm.reset();
                    location.reload(); // Or use AJAX to reload table/section list
                } else {
                    toastr.error(data.message || 'Failed to add section');
                }
            })
            .catch(error => {
                console.error(error);
                toastr.error('An error occurred while adding the section');
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('.delete-section').click(function() {
            const sectionid = $(this).data('id');
            const confirmation = confirm('Are you sure you want to delete this note?');
            if (confirmation) {
                $.ajax({
                    url: `/quotationtemplate/deletesection/${sectionid}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);
                            window.location.reload(); // Reload the page to see the changes
                        } else {
                            toastr.danger('Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.danger('Something went wrong!');
                    }
                });
            }
        });
    });
</script>

@endsection

@section('styles')
@parent
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/typography.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/katex.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/editor.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/swiper/swiper.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/ui-carousel.css')}}" />

@endsection

@section('content')
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row gy-6">
        <!-- Quotation Templates -->
        <div class="col-12">
            <div class="card">
                <h5 class="card-header">Quotation Templates</h5>
                <div class="card-body">
                    <form action="{{ route('quotationtemplate.section-populate') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                        @endif

                        <div class="row g-6">
                            <div class="col-md-6 mb-6">
                                <p><strong>Job Type: </strong> {{ $quotationTemplate->job_type_name }}</p>
                            </div>
                            <div class="col-md-6 mb-6">
                                <p><strong>Template Name: </strong> {{ $quotationTemplate->template_name }}</p>
                                <input type="hidden" name="quotation_template_id" value="{{ $quotationTemplate->id }}">
                            </div>
                        </div>
                        <div class="row g-6">
                            @foreach ($quotationTemplate->sections as $qts)
                            @if($qts->section_type == 'textbox')
                            <div class="col-md-12 mb-6">
                                <small class="text-body-secondary">
                                    <span class="badge rounded-pill bg-label-danger">
                                        <i class="fa-solid fa-trash delete-section" data-id="{{ $qts->id }}" style="cursor: pointer;"></i>
                                    </span>
                                </small>

                                <label class="form-label" for="{{ $qts->section_slug }}">{{ $qts->section_name }}</label>
                                @if ($qts->has_attachments)
                                <span class="mt-2">
                                    <strong class="badge bg-label-warning">This section has attachment</strong>
                                </span>
                                @endif

                                {{-- Hidden input to store the HTML from Quill --}}
                                <input type="hidden" name="{{ $qts->section_slug }}" id="input-{{ $qts->section_slug }}">

                                {{-- Div for the Quill editor --}}
                                <div id="{{ $qts->section_slug }}" class="form-control" style="min-height: 120px;">
                                    @if(count($qts->samples) > 0)
                                    {!! $qts->samples[0]->sample_source !!}
                                    @endif
                                </div>
                                
                            </div>
                            @endif
                            @endforeach
                        </div>

                        <div class="row g-6">
                            <div class="col-md-6 mb-6">
                                <button
                                    type="button"
                                    class="btn btn-warning waves-effect waves-light"
                                    data-bs-toggle="modal"
                                    data-bs-target="#addtemplateelement">
                                    <i class="icon-base ti tabler-plus me-1"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Add Section</span>
                                </button>
                            </div>

                            <div class="col-md-6 mb-6 text-end">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('quotationtemplates.index') }}" class="btn btn-secondary">Back</a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <!-- /Quotation Templates -->
         <!-- Form with Modal -->
        <div
            class="modal-onboarding modal fade animate__animated"
            id="addtemplateelement"
            tabindex="-1">
            <div class="modal-dialog" role="document">
                <div class="modal-content text-center">
                    <div class="modal-header border-0">
                        <a
                            class="text-body-secondary close-label"
                            href="javascript:void(0);"
                            data-bs-dismiss="modal">Skip Intro</a>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="QuotationTemplateSectionForm" action="{{ route('quotationtemplate.add-section') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body p-0">
                            <div class="onboarding-content mb-0">
                                <div class="row">
                                    <div class="mb-6 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-1">Section Name</label>
                                        <input type="text" id="form-repeater-1-1" class="form-control" name="section_name" placeholder="section name" required/>
                                    </div>

                                    <div class="mb-6 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-3">Section Type</label>
                                        <select id="form-repeater-1-3" class="form-select" name="section_type" required>
                                            <option value="textbox">Textbox</option>
                                            <option value="image_gallery">Image Gallery</option>
                                        </select>
                                    </div>
                                    <div class="mb-6 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-4">Is Required?</label>
                                        <select id="form-repeater-1-4" class="form-select" name="is_required" required>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="mb-6 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-4">Has Attachment?</label>
                                        <select id="form-repeater-1-4" class="form-select" name="has_attachment" required>
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="quotation_template_id" value="{{ $quotationTemplate->id }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" class="btn btn-success">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--/ Form with Modal -->
    </div>
</div>
@endsection