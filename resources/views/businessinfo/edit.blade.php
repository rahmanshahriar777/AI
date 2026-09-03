@extends('layouts.master')

@section('title', 'Edit Business Info')

@section('scripts')
@parent
<script src="{{asset('assets/vendor/libs/quill/katex.js')}}"></script>
<script src="{{asset('assets/vendor/libs/quill/quill.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/tagify/tagify.js')}}"></script>
<!-- Page JS -->
<script>
    $(function() {
        // $('.phone-mask').inputmask({
        //     mask: '+9 (999) 999-99-99'
        // });
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
<script type="text/javascript">
    async function copyToClipboard(textToCopy) {
    // Navigator clipboard api needs a secure context (https)
    if (navigator.clipboard && window.isSecureContext) {
        await navigator.clipboard.writeText(textToCopy);
    } else {
        // Use the 'out of viewport hidden text area' trick
        const textArea = document.createElement("textarea");
        textArea.value = textToCopy;
            
        // Move textarea out of the viewport so it's not visible
        textArea.style.position = "absolute";
        textArea.style.left = "-999999px";
            
        document.body.prepend(textArea);
        textArea.select();

        try {
            document.execCommand('copy');
            toastr.info("<strong>Image src Copied!</strong><br><br>" + textToCopy);
        } catch (error) {
            console.error(error);
        } finally {
            textArea.remove();
        }
    }
}
$(function() {
    
    $('#brandfiles a').click(function(e) {
        e.preventDefault();
        var imageSrc = $(this).find('img').attr('src');
        try {
            copyToClipboard(imageSrc);
        } catch(error) {
            
        }
    });
    $('#addbrandfileform1').on('submit', function(e){
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            enctype: 'multipart/form-data',
            data: $('#addbrandfileform'),
            success: function (response) {
                console.log(response)
                $('#addbrandfile').modal('hide')
                alert("Brand file added Successfully");
                
            },
            error: function(error){
                console.log(error)
                alert("Brand file not added");
            }
        });
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
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/tagify/tagify.css')}}" />

@endsection

@section('content')
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="app-ecommerce">
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <form action="{{ route('businessinfo.update', $businessinfo->id) }}" method="POST"  enctype="multipart/form-data">
            @csrf
            @method('PUT')
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
                    <h4 class="mb-1">Business Info Details</h4>
                </div>
                <div class="d-flex align-content-center flex-wrap gap-4">
                    <div class="d-flex gap-4">
                        <button type="submit" class="btn btn-label-success">UPDATE</button>
                        <!-- <button class="btn btn-label-secondary">Discard</button> -->
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-12">
                    <div class="card mb-6">
                        <div class="card-header">
                            <!-- <h5 class="card-title mb-0">New Business Info</h5> -->
                        </div>
                        <div class="card-body">
                            <div class="card mb-6">
                                <div class="card-header px-0 pt-0">
                                    <div class="nav-align-top">
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item">
                                                <button
                                                    type="button"
                                                    class="nav-link active"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#form-tabs-businessinfo"
                                                    aria-controls="form-tabs-businessinfo"
                                                    role="tab"
                                                    aria-selected="true">
                                                    <span class="icon-base ti tabler-user icon-lg d-sm-none"></span>
                                                    <span class="d-none d-sm-block">Business Info</span>
                                                </button>
                                            </li>
                                            <li class="nav-item">
                                                <button
                                                    type="button"
                                                    class="nav-link"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#form-tabs-billing-address"
                                                    aria-controls="form-tabs-billing-address"
                                                    role="tab"
                                                    aria-selected="false">
                                                    <span class="icon-base ti tabler-link icon-lg d-sm-none"></span>
                                                    <span class="d-none d-sm-block">Brand Info</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="tab-content p-0">
                                        <!-- Compnay Info -->
                                        <div class="tab-pane fade active show" id="form-tabs-businessinfo" role="tabpanel">
                                        
                                        <div class="row g-6">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <label class="col-sm-2 col-form-label text-sm-end"
                                                        for="formtabs-businessname">Business Name</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" id="formtabs-businessname" class="form-control"
                                                            placeholder="Business Name" aria-label="jdoe1 ltd" name="business_name"
                                                            required value="{{ $businessinfo->business_name }}" />
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
                                                            name="businessShortName" aria-label="Business Short Name" value="{{ $businessinfo->business_short_name }}" />
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" class="form-control"
                                                            id="formtabs-contact-businessdomain" placeholder="Business Domain"
                                                            name="businessDomain" aria-label="Business Domain" value="{{ $businessinfo->business_domain }}" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <label class="col-sm-2 col-form-label text-sm-end"for="form-business-start-date">Business Start Date</label>
                                                    <div class="col-sm-5">
                                                        <input type="date" class="form-control flatpickr" id="form-business-start-date"
                                                        name="businessStartDate" placeholder="Business Start Date" aria-label="Business Start Date" value="{{ $businessinfo->business_start_date }}" />
                                                    </div>
                                                    <div class="col-md-5">
                                                        <select id="businessStatus" class="form-select" name="businessStatus" required>
                                                                <option value="Active"
                                                                    {{ old('businessStatus', $businessinfo->business_status) == 'Active' ? 'selected' : '' }}>
                                                                    Active</option>
                                                                <option value="Inactive"
                                                                    {{ old('businessStatus', $businessinfo->business_status) == 'Inactive' ? 'selected' : '' }}>
                                                                    Inactive</option>
                                                            </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-sm-3 col-form-label text-sm-end" for="businessLogo">Logo</label>
                                                    <div class="col-sm-9">
                                                        <input class="form-control" type="file" name="businessLogo" id="businessLogo"  accept=".jpg,.jpeg,.png" 
                                                    onchange="previewFile(this, '#previewLogo')">
                                                    <img id="previewLogo" class="img-thumbnail mt-2 d-none1" style="max-height: 120px;" src="{{ $businessinfo->business_logo }}" />
                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-sm-3 col-form-label text-sm-end" for="businessLogoDark">Logo Dark</label>
                                                    <div class="col-sm-9">
                                                        <input class="form-control" type="file" name="businessLogoDark" id="businessLogoDark" accept=".jpg,.jpeg,.png"  
                                                    onchange="previewFile(this, '#previewLogoDark')">
                                                    <img id="previewLogoDark" class="img-thumbnail mt-2 d-none1" style="max-height: 120px;"  src="{{ $businessinfo->business_logo_dark }}" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-sm-3 col-form-label text-sm-end" for="businessFavicon">Favicon</label>
                                                    <div class="col-sm-9">
                                                        <input class="form-control" type="file" name="businessFavicon" id="businessFavicon" accept=".jpg,.jpeg,.png,.ico"  
                                                    onchange="previewFile(this, '#previewFavicon')">
                                                    <img id="previewFavicon" class="img-thumbnail mt-2 d-none1" style="max-height: 120px;"  src="{{ $businessinfo->business_favicon }}" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-sm-3 col-form-label text-sm-end"
                                                        for="formtabs-allowedfiletypes">Allowed File Types</label>
                                                    <div class="col-sm-9">
                                                        <textarea name ="allowedFiletypes" id="formtabs-allowedFiletypes" rows="3" class="form-control" placeholder="Allowed File Types">{{$businessinfo->business_allowed_file_types }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-sm-3 col-form-label text-sm-end"
                                                        for="formtabs-address1">Address Line 1</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" id="formtabs-address1" class="form-control"
                                                            placeholder="Address Line 1" aria-label="Address Line 1" name="businessAddress1" value="{{ $businessinfo->business_address }}"/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-sm-3 col-form-label text-sm-end"
                                                        for="formtabs-address2">Address Line 2</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" id="formtabs-address1" class="form-control"
                                                            placeholder="Address Line 2" aria-label="Address Line 2" name="businessAddress2" value="{{ $businessinfo->business_address2 }}"/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-sm-3 col-form-label text-sm-end"
                                                        for="formtabs-city">City</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" id="formtabs-city" class="form-control"
                                                            placeholder="City" aria-label="City" name="businessCity" value="{{ $businessinfo->business_city }}"/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-sm-3 col-form-label text-sm-end"
                                                        for="formtabs-state">State</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" id="formtabs-state" class="form-control"
                                                            placeholder="State" aria-label="State" name="businessState" value="{{ $businessinfo->business_state }}"/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-sm-3 col-form-label text-sm-end"
                                                        for="formtabs-zip">Zip</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" id="formtabs-zip" class="form-control"
                                                            placeholder="Zip" aria-label="Zip" name="businessZip" value="{{ $businessinfo->business_zip }}"/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-sm-3 col-form-label text-sm-end"
                                                        for="formtabs-country">Country</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" id="formtabs-country" class="form-control"
                                                            placeholder="Country" aria-label="Country" name="businessCountry" value="{{ $businessinfo->business_country }}"/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-sm-3 col-form-label text-sm-end"
                                                        for="formtabs-contact-phone">Phone</label>
                                                    <div class="col-sm-9">
                                                        <input type="tel" id="formtabs-phone" name="businessPhone"
                                                            class="form-control" placeholder="{{ simple_format_phone('') }}" maxlength="20"
                                                            pattern="{{ simple_format_phone_pattern() }}" inputmode1="numeric"
                                                            oninput1="this.value = this.value.replace(/\D/g, '').slice(0, 11);" value="{{ $businessinfo->business_phone }}"/>
                                                            
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
                                                            name="businessEmail" value="{{ $businessinfo->business_email }}"  />

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-sm-3 col-form-label text-sm-end"
                                                        for="formtabs-business-website">Website URL</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" id="formtabs-business-website" name="businessWebsite" class="form-control" placeholder="http://example.com" value="{{ $businessinfo->business_website }}" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-sm-3 col-form-label text-sm-end"
                                                        for="formtabs-business-registeraton-number">BIN</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" id="formtabs-business-registeraton-number" class="form-control"
                                                            placeholder="Business Registration Number" aria-label="Business Registration Number" name="businessRegistrationNumber" value="{{ $businessinfo->business_registration_number }}"/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-sm-3 col-form-label text-sm-end"
                                                        for="formtabs-business-registeraton-number2">VAT</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" id="formtabs-business-registeraton-number2" class="form-control"
                                                            placeholder="Business Registration Number2" aria-label="Business Registration Number2" name="businessRegistrationNumber2" value="{{ $businessinfo->business_registration_number2 }}"/>
                                                    </div>
                                                </div>
                                            </div>
                                         </div>

                                        </div>

                                        <!-- Billing Address -->
                                        <div class="tab-pane fade" id="form-tabs-billing-address" role="tabpanel">
                                            <div class="row g-6">
                                                <div class="col-md-9">
                                                    <div class="row">
                                                        <div class="col-md-12"><div class="row">Brand App Name</div>  </div>
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <label class="col-sm-3 col-form-label text-sm-end"
                                                                    for="formtabs-brand-app-name">Content</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" id="formtabs-brand-app-name" class="form-control"
                                                                        placeholder="Brand App Name" aria-label="Brand App Name" name="business_brand_app_name" value="{{ $businessinfo->business_brand_app_name }}"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12"><div class="row">Brand Logo On Top</div>  </div>
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-billing-personname">Content</label>
                                                                <div class="col-sm-9">
                                                                    <textarea name ="business_brand_logo_on_top_left" id="business_brand_logo_on_top_left" rows="5" class="form-control">{{$businessinfo->business_brand_logo_on_top_left }} </textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12"><div class="row">Brand Credit On Footer</div>  </div>
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-billing-personname">Enable?</label>
                                                                <div class="col-sm-9">
                                                                    <select id="business_brand_credit_on_footer_enable" class="form-select" name="business_brand_credit_on_footer_enable" required>
                                                                        <option value="1"
                                                                            {{ old('business_brand_credit_on_footer_enable', $businessinfo->business_brand_credit_on_footer_enable) == '1' ? 'selected' : '' }}>
                                                                            Yes</option>
                                                                        <option value="0"
                                                                            {{ old('business_brand_credit_on_footer_enable', $businessinfo->business_brand_credit_on_footer_enable) == '0' ? 'selected' : '' }}>
                                                                            No</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-billing-personname">Content</label>
                                                                <div class="col-sm-9">
                                                                    <textarea name ="business_brand_credit_on_footer" id="business_brand_credit_on_footer" rows="5" class="form-control">{{$businessinfo->business_brand_credit_on_footer }} </textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="row">
                                                        <a href="javascript:;" class="btn btn-xs btn-primary waves-effect waves-light" data-bs-target="#addbrandfile" data-bs-toggle="modal"> <span class="icon-base ti tabler-plus icon-xs me-1"></span>Upload Files</a>

                                                        <div id="brandfiles" style="display:inline-block; width: 100%;    padding: 10px 0px;">
                                                            @foreach ($branlogoinfo as $item)
                                                                <a href="#"><img src="{{ $item }}"  style="max-width: 100px; max-height: 100px;" title="Click to copy image url." /></a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
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
<!-- / Content -->


<!-- Modal -->
<div class="modal fade" id="addbrandfile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2">Brand File</h4>
                </div>
                <form id="addbrandfileform" class="row g-6" onsubmit="return true" action="{{ route('businessinfo.uploadbrandfiles', $businessinfo->id) }}" enctype="multipart/form-data" method="POST" >
                    @csrf
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="brandFile"></label>
                        <input class="form-control" type="file" name="brandFile" id="brandFile"  accept=".jpg,.jpeg,.png" onchange="previewFile(this, '#previewBrandFile')">
                        <img id="previewBrandFile" class="img-thumbnail mt-2 d-none1" style="max-height: 120px;" src="" />
                    </div>

                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary me-3">Submit</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                            aria-label="Close">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

@endsection