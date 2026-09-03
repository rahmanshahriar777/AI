@extends('layouts.master')

@section('title', 'Create Customer')

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
        $('.phone-mask').inputmask({
            mask: '+9 (999) 999-99-99'
        });
        $('.select2').select2();
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
        <form action="{{ route('customers.update', $customer->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
                <div class="d-flex flex-column justify-content-center">
                    <h4 class="mb-1">Customer Details</h4>
                </div>
                <div class="d-flex align-content-center flex-wrap gap-4">
                    <div class="d-flex gap-4">
                        <button type="submit" class="btn btn-label-success">UPDATE</button>
                        <button class="btn btn-label-secondary">Discard</button>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-12">
                    <div class="card mb-6">
                        <div class="card-header">
                            <!-- <h5 class="card-title mb-0">New Customer</h5> -->
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
                                                    data-bs-target="#form-tabs-company"
                                                    aria-controls="form-tabs-company"
                                                    role="tab"
                                                    aria-selected="true">
                                                    <span class="icon-base ti tabler-user icon-lg d-sm-none"></span>
                                                    <span class="d-none d-sm-block">Company Info</span>
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
                                                    <span class="d-none d-sm-block">Billing Address</span>
                                                </button>
                                            </li>
                                            <li class="nav-item">
                                                <button
                                                    type="button"
                                                    class="nav-link"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#form-tabs-site-address"
                                                    aria-controls="form-tabs-site-address"
                                                    role="tab"
                                                    aria-selected="false">
                                                    <span class="icon-base ti tabler-link icon-lg d-sm-none"></span>
                                                    <span class="d-none d-sm-block">Site Address</span>
                                                </button>
                                            </li>
                                            <li class="nav-item">
                                                <button
                                                    type="button"
                                                    class="nav-link"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#form-tabs-account"
                                                    aria-controls="form-tabs-account"
                                                    role="tab"
                                                    aria-selected="false">
                                                    <span class="icon-base ti tabler-user-cog icon-lg d-sm-none"></span>
                                                    <span class="d-none d-sm-block">Account Details</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="tab-content p-0">
                                        <!-- Compnay Info -->
                                        <div class="tab-pane fade active show" id="form-tabs-company" role="tabpanel">
                                            <div class="row g-6">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-user-company">Company</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                id="formtabs-user-company"
                                                                class="form-control"
                                                                placeholder="Company Name"
                                                                aria-label="jdoe1 ltd"
                                                                name="companyName"
                                                                value="{{ $customer->company_name }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <label class="col-sm-2 col-form-label text-sm-end" for="formtabs-contact-personname">Contact Person</label>
                                                        <div class="col-sm-5">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="formtabs-contact-personfirstname"
                                                                placeholder="First Name"
                                                                name="contactPersonFirstName"
                                                                aria-label="First Name"
                                                                value="{{ $customer->contact_firstname }}" />
                                                        </div>
                                                        <div class="col-sm-5">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="formtabs-contact-personlastname"
                                                                placeholder="Last Name"
                                                                name="contactPersonLastName"
                                                                aria-label="Last Name"
                                                                value="{{ $customer->contact_lastname }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-contact-email">Contact email</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                id="formtabs-contact-email"
                                                                class="form-control"
                                                                placeholder="john.doe@example.com"
                                                                aria-label="john.doe@example.com"
                                                                name="contactEmail"
                                                                value="{{ $customer->contact_email }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-contact-phone">Contact Phone</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                id="formtabs-contact-phone"
                                                                class="form-control phone-mask"
                                                                placeholder="+1 (609) 988-44-11"
                                                                aria-label="+1 (609) 988-44-11"
                                                                name="contactPhone"
                                                                value="{{ $customer->contact_phone }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-contact-phone">Contact Mobile</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                id="formtabs-contact-phone"
                                                                class="form-control phone-mask"
                                                                placeholder="+1 (609) 988-44-11"
                                                                aria-label="+1 (609) 988-44-11"
                                                                name="contactPhone"
                                                                value="{{ $customer->contact_mobile }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Billing Address -->
                                        <div class="tab-pane fade" id="form-tabs-billing-address" role="tabpanel">
                                            <div class="row g-6">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <label class="col-sm-2 col-form-label text-sm-end" for="formtabs-billing-personname">Contact Person</label>
                                                        <div class="col-sm-5">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="formtabs-billing-personfirstname"
                                                                placeholder="First Name"
                                                                name="billingPersonFirstName"
                                                                aria-label="First Name"
                                                                value="{{ $customer->billingaddress->contact_firstname ?? '' }}" />
                                                        </div>
                                                        <div class="col-sm-5">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="formtabs-billing-personlastname"
                                                                placeholder="Last Name"
                                                                name="billingPersonLastName"
                                                                aria-label="Last Name"
                                                                value="{{ $customer->billingaddress->contact_lastname ?? '' }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-billing-phone">Contact Mobile</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                id="formtabs-billing-mobile"
                                                                class="form-control phone-mask"
                                                                placeholder="+1 (609) 988-44-11"
                                                                aria-label="+1 (609) 988-44-11"
                                                                name="billingMobile"
                                                                value="{{ $customer->billingaddress->contact_mobile ?? '' }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-billing-phone">Contact Phone</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                id="formtabs-billing-phone"
                                                                class="form-control phone-mask"
                                                                placeholder="+1 (609) 988-44-11"
                                                                aria-label="+1 (609) 988-44-11"
                                                                name="billingPhone"
                                                                value="{{ $customer->billingaddress->contact_phone ?? ''}}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-billing-email">Contact email</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                id="formtabs-billing-email"
                                                                class="form-control"
                                                                placeholder="john.doe@example.com"
                                                                aria-label="john.doe@example.com"
                                                                name="billingEmail"
                                                                value="{{ $customer->billingaddress->contact_email ?? ''}}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-billing-address">Billing Address</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="formtabs-billing-address"
                                                                placeholder="30H Burdi Street"
                                                                name="billingAddress"
                                                                aria-label="30H Burdi Street"
                                                                value="{{ $customer->billingaddress->address ?? ''}}" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-billing-county">County</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="formtabs-billing-county"
                                                                placeholder="Birmingham"
                                                                name="billingCounty"
                                                                aria-label="Birmingham"
                                                                value="{{ $customer->billingaddress->county ?? ''}}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-billing-postcode">Postcode</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="formtabs-billing-postcode"
                                                                placeholder="A1 1AA"
                                                                name="billingPostcode"
                                                                aria-label="A1 1AA"
                                                                value="{{ $customer->billingaddress->postcode ?? ''}}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-billing-country">Country</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="formtabs-billing-country"
                                                                value="United Kingdom" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Site Address -->
                                        <div class="tab-pane fade" id="form-tabs-site-address" role="tabpanel">
                                            <div class="row g-6">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <label class="col-sm-2 col-form-label text-sm-end" for="formtabs-site-personname">Contact Person</label>
                                                        <div class="col-sm-5">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="formtabs-site-personfirstname"
                                                                placeholder="First Name"
                                                                name="sitePersonFirstName"
                                                                aria-label="First Name"
                                                                value="{{ $customer->siteaddress->contact_firstname ?? '' }}" />
                                                        </div>
                                                        <div class="col-sm-5">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="formtabs-site-personlastname"
                                                                placeholder="Last Name"
                                                                name="sitePersonLastName"
                                                                aria-label="Last Name"
                                                                value="{{ $customer->siteaddress->contact_lastname ?? '' }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-site-phone">Contact Mobile</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                id="formtabs-site-mobile"
                                                                class="form-control phone-mask"
                                                                placeholder="+1 (609) 988-44-11"
                                                                aria-label="+1 (609) 988-44-11"
                                                                name="siteMobile"
                                                                value="{{ $customer->siteaddress->contact_mobile ?? '' }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-site-phone">Contact Phone</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                id="formtabs-site-phone"
                                                                class="form-control phone-mask"
                                                                placeholder="+1 (609) 988-44-11"
                                                                aria-label="+1 (609) 988-44-11"
                                                                name="sitePhone"
                                                                value="{{ $customer->siteaddress->contact_phone ?? '' }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-site-email">Contact email</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                id="formtabs-site-email"
                                                                class="form-control"
                                                                placeholder="john.doe@example.com"
                                                                aria-label="john.doe@example.com"
                                                                name="siteEmail"
                                                                value="{{ $customer->siteaddress->contact_email ?? '' }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-site-address">Site Address</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="formtabs-site-address"
                                                                placeholder="30H Burdi Street"
                                                                name="siteAddress"
                                                                aria-label="30H Burdi Street"
                                                                value="{{ $customer->siteaddress->address ?? '' }}" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-site-county">County</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="formtabs-site-county"
                                                                placeholder="Birmingham"
                                                                name="siteCounty"
                                                                aria-label="Birmingham"
                                                                value="{{ $customer->siteaddress->county ?? '' }}" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-site-postcode">Postcode</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="formtabs-site-postcode"
                                                                placeholder="A1 1AA"
                                                                name="sitePostcode"
                                                                aria-label="A1 1AA"
                                                                value="{{ $customer->siteaddress->postcode ?? '' }}" />
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-site-country">Country</label>
                                                        <div class="col-sm-9">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                value="{{ $customer->siteaddress->country ?? '' }}" />
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <!-- Account Details -->
                                        <div class="tab-pane fade" id="form-tabs-account" role="tabpanel">
                                            <div class="row g-6">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <label class="col-sm-2 col-form-label text-sm-end" for="formtabs-contact-company">Company Name</label>
                                                        <div class="col-sm-5">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                value="{{ $customer->company_name }}" readonly />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <label class="col-sm-2 col-form-label text-sm-end" for="formtabs-assets">Reg. Number</label>
                                                        <div class="col-sm-10">
                                                            <input type="text"
                                                                id="formtabs-assets"
                                                                name="registration_no"
                                                                class="form-control"
                                                                placeholder="registration number"
                                                                value="{{ $customer->findetails->registration_number ?? '' }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-assets">Assets</label>
                                                        <div class="col-sm-9">
                                                            <input type="number"
                                                                min="0"
                                                                id="formtabs-assets"
                                                                name="assets"
                                                                class="form-control"
                                                                placeholder="0.00"
                                                                value="{{ $customer->findetails->assets ?? 0 }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-net-assets">Net Assets</label>
                                                        <div class="col-sm-9">
                                                            <input type="number"
                                                                min="0"
                                                                id="formtabs-net-assets"
                                                                name="netAssets"
                                                                class="form-control"
                                                                placeholder="0.00"
                                                                value="{{ $customer->findetails->net_assets ?? 0 }}" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-liabilities">Liabilities</label>
                                                        <div class="col-sm-9">
                                                            <input type="number"
                                                                min="0"
                                                                id="formtabs-liabilities"
                                                                name="liabilities"
                                                                class="form-control"
                                                                placeholder="0.00"
                                                                value="{{ $customer->findetails->liabilities ?? 0 }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-cash-in-bank">Cash in Bank</label>
                                                        <div class="col-sm-9">
                                                            <input type="number"
                                                                min="0"
                                                                id="formtabs-cash-in-bank"
                                                                name="cashInBank"
                                                                class="form-control"
                                                                placeholder="0.00"
                                                                value="{{ $customer->findetails->cash_in_bank ?? 0 }}" />
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

@endsection