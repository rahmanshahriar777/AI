@extends('layouts.master')

@section('title', 'Edit Mail Template')

@section('scripts')
@parent
<script src="{{asset('assets/vendor/libs/quill/katex.js')}}"></script>
<script src="{{asset('assets/vendor/libs/quill/quill.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleave-zen/cleave-zen.js')}}"></script>
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toolbar = document.querySelector('.mailbody-toolbar');

        const quill = new Quill('#multicol-mailbody', {
            theme: 'snow',
            modules: {
                toolbar: toolbar
            }
        });

        const textarea = document.getElementById('mailbody');
        // Sync Quill content on every change
        quill.on('text-change', function() {
            const html = quill.root.innerHTML.trim();
            textarea.value = (html === '<p><br></p>') ? '' : html;
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
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Multi Column with Form Separator -->
    <div class="row mb-6 gy-6">
        <!-- Form Separator -->
        <div class="col-xxl">
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="card-title mb-0">Create Mail Template</h5>
                    <div class="card-header-elements ms-auto py-0">
                        <a class="btn btn-dark waves-effect waves-light mb-2" href="{{ route('mailtemplates.index') }}">Back</a>
                    </div>
                </div>
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
                <form class="card-body">
                    
                    <div class="row mb-6">
                        <label class="col-sm-3 col-form-label" for="multicol-name">Name</label>
                        <div class="col-sm-9">
                            <input type="text" id="multicol-name" name="name" class="form-control" placeholder="Formal Mail" value="{{ $mailTemplate->name }}"  disabled/>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-sm-3 col-form-label" for="multicol-subject">Subject</label>
                        <div class="col-sm-9">
                            <input type="text" id="multicol-subject" name="subject" class="form-control" placeholder="Subject" value="{{ $mailTemplate->subject }}" disabled />
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-sm-3 col-form-label" for="multicol-mailbody">Mail Body</label>
                        <div class="col-sm-9">
                            <div class="mailbody-toolbar border-0 border-bottom">
                                <div class="d-flex justify-content-start">
                                    <span class="ql-formats me-0">
                                        <button class="ql-bold"></button>
                                        <button class="ql-italic"></button>
                                        <button class="ql-underline"></button>
                                        <button class="ql-list" value="ordered"></button>
                                        <button class="ql-list" value="bullet"></button>
                                        <button class="ql-link"></button>
                                        <button class="ql-image"></button>
                                    </span>
                                </div>
                            </div>

                            <div class="multicol-mailbody border-0 pb-6" id="multicol-mailbody" data-name="mailbody" aria-disabled="true">{!! $mailTemplate->body !!}</div>
                        </div>
                    </div>


                    <div class="row mb-6">
                        <label class="col-sm-3 col-form-label" for="multicol-status">Status</label>
                        <div class="col-sm-9">
                            <select class="form-select" name="status" id="basicSelect" disabled>
                                <option value="active" @if ($mailTemplate->status == "active") selected @endif>Active</option>
                                <option value="inactive" @if ($mailTemplate->status == "inactive") selected @endif>Inactive</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>




</div>
@endsection