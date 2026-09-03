@extends('layouts.master')

@section('title', 'Create Certificate')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js') }}"></script>
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
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/tagify/tagify.css') }}" />

@endsection

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="app-ecommerce">

            <form action="{{ route('certificates.store') }}" method="POST">
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
                        <h4 class="mb-1">Add a new Certificate</h4>
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
                                @if (session('success'))
                                    <div class="alert alert-success mt-2">{{ session('success') }}</div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger mt-2">{{ session('error') }}</div>
                                @endif
                            </div>
                            <div class="card-body">

                                <div class="row g-6">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <label class="col-sm-2 col-form-label text-sm-end"
                                                for="certificateName">Certificate Name</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="certificateName" class="form-control"
                                                    placeholder="Certificate Name" aria-label="Certificate Name"
                                                    name="certificatename" required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <label class="col-sm-2 col-form-label text-sm-end"
                                                for="certificateDetails">Details</label>
                                            <div class="col-sm-10">
                                                <textarea id="certificateDetails" class="form-control" name="certificatedetails" rows="3"
                                                    placeholder="Enter certificate details here..."></textarea>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <label class="col-sm-2 col-form-label text-sm-end"
                                                for="certificateValidity">Validity</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="certificateValidity"
                                                    class="form-control flatpickr-basic" placeholder="1 month"
                                                    name="certificatevalidity" required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <label class="col-sm-2 col-form-label text-sm-end" for="conductedBy">Conducted
                                                By</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="conductedBy" class="form-control flatpickr-basic"
                                                    placeholder="Conducted By" name="conductedby" required />
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
    <!-- / Content -->

@endsection
