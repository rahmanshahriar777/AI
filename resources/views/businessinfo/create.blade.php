@extends('layouts.master')

@section('title', 'Create Business Info')

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
    <script>
        function previewFile(input, previewId) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.querySelector(previewId);
                    img.src = e.target.result;
                    img.classList.remove("d-none");
                };
                reader.readAsDataURL(file);
            }
        }
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

            <form action="{{ route('businessinfo.store') }}" method="POST" enctype="multipart/form-data" >
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
                        <h4 class="mb-1">Add a new Business Info</h4>
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
                                <!-- <h5 class="card-title mb-0">New Business</h5> -->
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success mt-2">{{ session('success') }}</div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger mt-2">{{ session('error') }}</div>
                                @endif
                                <div class="row g-6">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <label class="col-sm-2 col-form-label text-sm-end"
                                                for="formtabs-businessname">Business Name</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="formtabs-businessname" class="form-control"
                                                    placeholder="Business Name" aria-label="jdoe1 ltd" name="business_name"
                                                    required value="{{ old('business_name') }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <label class="col-sm-2 col-form-label text-sm-end"
                                                for="formtabs-contact-businessshortname">Business Short Name</label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control"
                                                    id="formtabs-contact-businessshortname" placeholder="Business Short Name"
                                                    name="businessShortName" aria-label="Business Short Name" value="{{ old('businessShortName') }}" />
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control"
                                                    id="formtabs-contact-businessdomain" placeholder="Business Domain"
                                                    name="businessDomain" aria-label="Business Domain" value="{{ old('businessDomain') }}" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="row">
                                            <label class="col-sm-2 col-form-label text-sm-end"for="form-business-start-date">Business Start Date</label>
                                            <div class="col-sm-5">
                                                <input type="date" class="form-control flatpickr" id="form-business-start-date"
                                                name="businessStartDate" placeholder="Business Start Date" aria-label="Business Start Date" value="{{ old('businessStartDate') }}" />
                                            </div>
                                            <div class="col-sm-5"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-3 col-form-label text-sm-end" for="businessLogo">Logo</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" type="file" name="businessLogo" id="businessLogo"  accept=".jpg,.jpeg,.png" 
                                             onchange="previewFile(this, '#previewLogo')">
                                             <img id="previewLogo" class="img-thumbnail mt-2 d-none" style="max-height: 120px;" />
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-3 col-form-label text-sm-end" for="businessLogoDark">Logo Dark</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" type="file" name="businessLogoDark" id="businessLogoDark" accept=".jpg,.jpeg,.png"  
                                             onchange="previewFile(this, '#previewLogoDark')">
                                             <img id="previewLogoDark" class="img-thumbnail mt-2 d-none" style="max-height: 120px;" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-3 col-form-label text-sm-end" for="businessFavicon">Favicon</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" type="file" name="businessFavicon" id="businessFavicon" accept=".jpg,.jpeg,.png,.ico"  
                                             onchange="previewFile(this, '#previewFavicon')">
                                             <img id="previewFavicon" class="img-thumbnail mt-2 d-none" style="max-height: 120px;" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-3 col-form-label text-sm-end"
                                                for="formtabs-allowedfiletypes">Allowed File Types</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="formtabs-allowedFiletypes" class="form-control"
                                                    placeholder="Allowed File Types" aria-label="Allowed File Types" name="allowedFiletypes"  value="{{ old('allowedFiletypes') }}"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-3 col-form-label text-sm-end"
                                                for="formtabs-address1">Address Line 1</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="formtabs-address1" class="form-control"
                                                    placeholder="Address Line 1" aria-label="Address Line 1" name="businessAddress1" value="{{ old('businessAddress1') }}"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-3 col-form-label text-sm-end"
                                                for="formtabs-address2">Address Line 2</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="formtabs-address1" class="form-control"
                                                    placeholder="Address Line 2" aria-label="Address Line 2" name="businessAddress2" value="{{ old('businessAddress2') }}"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-3 col-form-label text-sm-end"
                                                for="formtabs-city">City</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="formtabs-city" class="form-control"
                                                    placeholder="City" aria-label="City" name="businessCity" value="{{ old('businessCity') }}"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-3 col-form-label text-sm-end"
                                                for="formtabs-state">State</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="formtabs-state" class="form-control"
                                                    placeholder="State" aria-label="State" name="businessState" value="{{ old('businessState') }}"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-3 col-form-label text-sm-end"
                                                for="formtabs-zip">Zip</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="formtabs-zip" class="form-control"
                                                    placeholder="Zip" aria-label="Zip" name="businessZip" value="{{ old('businessZip') }}"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-sm-3 col-form-label text-sm-end"
                                                    for="formtabs-country">Country</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="formtabs-country" class="form-control"
                                                        placeholder="Country" aria-label="Country" name="businessCountry" value="{{ old('businessCountry', '') }}"/>
                                                </div>
                                            </div>
                                        </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-3 col-form-label text-sm-end"
                                                for="formtabs-contact-phone">Phone</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="formtabs-phone" name="businessPhone"
                                                    class="form-control" placeholder="{{ simple_format_phone('') }}" maxlength="20"
                                                    pattern="{{ simple_format_phone_pattern() }}" inputmode="numeric"
                                                    oninput="this.value = this.value.replace(/\D/g, '').slice(0, 11);" value="{{ old('businessPhone') }}"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-3 col-form-label text-sm-end"
                                                for="formtabs-business-email">Email</label>
                                            <div class="col-sm-9">
                                                <input type="email" id="formtabs-business-email" class="form-control"
                                                    placeholder="info@example.com" aria-label="info@example.com"
                                                    name="businessEmail" value="{{ old('businessEmail') }}"  />

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-3 col-form-label text-sm-end"
                                                for="formtabs-business-website">Website URL</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="formtabs-business-website" name="businessWebsite" class="form-control" placeholder="http://example.com" value="{{ old('businessWebsite') }}" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-3 col-form-label text-sm-end"
                                                for="formtabs-business-registeraton-number">BIN</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="formtabs-business-registeraton-number" class="form-control"
                                                    placeholder="Business Registration Number" aria-label="Business Registration Number" name="businessRegistrationNumber" value="{{ old('businessRegistrationNumber') }}"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-sm-3 col-form-label text-sm-end"
                                                for="formtabs-business-registeraton-number2">VAT</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="formtabs-business-registeraton-number2" class="form-control"
                                                    placeholder="Business Registration Number2" aria-label="Zip" name="businessRegistrationNumber2" value="{{ old('businessRegistrationNumber2') }}"/>
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
