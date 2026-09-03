@extends('layouts.master')

@section('title', 'Create Quotation Template')

@section('scripts')
@parent
<script src="{{asset('assets/vendor/libs/cleave-zen/cleave-zen.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>

<script src="{{ asset('assets/js/forms-extras.js?v='.filemtime(public_path('assets/js/forms-extras.js'))) }}"></script>
<!-- Page JS -->
<script>
</script>
@endsection

@section('styles')
@parent

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
                    <form class="form-repeater" action="{{ route('quotationtemplates.store') }}" method="POST" enctype="multipart/form-data">
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
                        <div class="row g-6">
                            <div class="col-md-6 mb-6">
                                <label class="form-label" for="multicol-jobtype">Job Type</label>
                                <select id="multicol-jobtype" class="select2 form-select" data-allow-clear="true" name="jobtype" required>
                                    <option value="">Select</option>
                                    @foreach ($jobtype as $jt)
                                    <option value="{{ $jt->slug }}">{{ $jt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-6">
                                <label class="form-label" for="basic-default-templatename">Template Name</label>
                                <input type="text" class="form-control" id="basic-default-templatename" placeholder="" name="template_name" required />
                            </div>
                        </div>
                        <div data-repeater-list="group-a">
                            <div data-repeater-item>
                                <div class="row">
                                    <div class="mb-6 col-lg-6 col-xl-3 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-1">Section Name</label>
                                        <input type="text" id="form-repeater-1-1" class="form-control" name="section_name" placeholder="section name" required />
                                    </div>

                                    <div class="mb-6 col-lg-6 col-xl-2 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-2">Section Type</label>
                                        <select id="form-repeater-1-2" class="form-select" name="section_type" required>
                                            <option value="textbox">Textbox</option>
                                            <option value="image_gallery">Image Gallery</option>
                                        </select>
                                    </div>
                                    <div class="mb-6 col-lg-6 col-xl-2 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-3">Is Required?</label>
                                        <select id="form-repeater-1-3" class="form-select" name="is_required" required>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="mb-6 col-lg-6 col-xl-2 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-4">Has Attachment?</label>
                                        <select id="form-repeater-1-4" class="form-select" name="has_attachment" required>
                                            <option value="1">Yes</option>
                                            <option value="0" selected>No</option>
                                        </select>
                                    </div>

                                    <div class="mb-6 col-lg-12 col-xl-2 col-12 d-flex align-items-center mb-0">
                                        <button type="button" class="btn btn-label-danger mt-xl-6" data-repeater-delete>
                                            <i class="icon-base ti tabler-x me-1"></i>
                                            <span class="align-middle">Delete</span>
                                        </button>
                                    </div>
                                </div>
                                <hr />
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-between">
                            <button type="button" class="btn btn-label-primary btn-prev waves-effect" data-repeater-create>
                                <i class="icon-base ti tabler-plus me-1"></i>
                                <span class="align-middle d-sm-inline-block d-none">Add</span>
                            </button>
                            <button class="btn btn-success btn-next waves-effect waves-light" type="submit">
                                <span class="align-middle d-sm-inline-block d-none me-sm-2">Submit</span>
                                <i class="icon-base ti tabler-arrow-right icon-xs"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <!-- /Quotation Templates -->
    </div>
</div>
@endsection