@extends('layouts.master')

@section('title', 'Dashboard')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/cards-advance.css') }}" />
@endsection

@section('content')
    @role('enquiry-manager')
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row g-6">
                <!-- Card Border Shadow -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-border-shadow-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-primary"><i
                                            class="icon-base ti tabler-truck icon-28px"></i></span>
                                </div>
                                <h4 class="mb-0">{{ $newenquiriescount }}</h4>
                            </div>
                            <p class="mb-1">New Enquiries</p>
                            <p class="mb-0">
                                <span class="text-heading fw-medium me-2">{{ $change_percent }}%</span>
                                <small class="text-body-secondary">{{ $trend }} than last week</small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-border-shadow-warning h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-warning"><i
                                            class="icon-base ti tabler-alert-triangle icon-28px"></i></span>
                                </div>
                                <h4 class="mb-0">{{ $canceledenquiriescount }}</h4>
                            </div>
                            <p class="mb-1">Rejected Enquiries</p>
                            <p class="mb-0">
                                <span class="text-heading fw-medium me-2">{{ $change_percent }}%</span>
                                <small class="text-body-secondary">{{ $trend }} than last week</small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-border-shadow-danger h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-danger"><i
                                            class="icon-base ti tabler-git-fork icon-28px"></i></span>
                                </div>
                                <h4 class="mb-0">0</h4>
                            </div>
                            <p class="mb-1">Completed enquiries <br> <small>(finished but awaiting payment)</small></p>
                            <p class="mb-0">
                                <span class="text-heading fw-medium me-2">{{ $totalcustomerscount }}</span>
                                <small class="text-body-secondary">total customers enrolled</small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-border-shadow-info h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-info">
                                        <i class="icon-base ti tabler-clock icon-28px"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">0</h4>
                            </div>
                            <p class="mb-1">Completed enquiries <br> <small>(Finished and paid)</small></p>
                            <p class="mb-0">
                                <span class="text-heading fw-medium me-2">0%</span>
                                <small class="text-body-secondary">than last week</small>
                            </p>
                        </div>
                    </div>
                </div>
                <!--/ Card Border Shadow -->

                <!-- New Enquiries -->
                <div class="col-md-12 col-12">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title m-0 me-2">New Enquiries</h5>

                            <a href="{{ route('enquiries.index') }}"
                                class="btn rounded-pill btn-primary waves-effect waves-light">
                                SEE ALL >>
                            </a>

                        </div>
                        <div class="table-responsive">
                            <table class="table table-borderless border-top">
                                <thead class="border-bottom">
                                    <tr>
                                        <th>ENQUIRY #</th>
                                        <th>LEAD FROM</th>
                                        <th>COMPANY</th>
                                        <th>SITE ADDRESS</th>
                                        <th>CONTACT</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($newEnquiries as $neq)
                                        <tr>
                                            <td class="pt-5">
                                                <div class="d-flex flex-column">
                                                    <strong class="text-body text-nowrap">{{ $neq->slug }}</strong>
                                                    <small
                                                        class="text-body text-nowrap">{{ \Carbon\Carbon::parse($neq->created_at)->format('j F Y') }}</small>
                                                </div>
                                            </td>
                                            <td class="pt-5"><span class="badge bg-label-success">{{ $neq->enquiry_source }}
                                                </span></td>

                                            <td class="pt-5">
                                                <p class="mb-0 text-heading">{{ $neq->customer->company_name }} </p>
                                            </td>
                                            <td class="pt-5">
                                                <p class="mb-0 text-heading">{{ $neq->enquiryaddress->address ?? '' }},
                                                    {{ $neq->enquiryaddress->county ?? '' }}
                                                    {{ $neq->enquiryaddress->postcode ?? '' }}</p>
                                            </td>
                                            <td class="pt-5">
                                                <p class="mb-0 text-heading">{{ $neq->customer->contact_firstname }}
                                                    {{ $neq->customer->contact_lastname }}</p>
                                                <p class="mb-0 text-heading"> <i class="fa-solid fa-phone"></i>
                                                    {{ $neq->customer->contact_phone }}</p>
                                            </td>
                                            <td class="pt-5">
                                                <a href="{{ route('enquiries.show', $neq->slug) }}"
                                                    class="btn btn-sm btn-dark waves-effect waves-light">REVIEW</a>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--/ New Enquiries -->

                <!-- Leads Under Review -->
                <div class="col-md-12 col-12">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title m-0 me-2">Leads Under Review</h5>
                            <!-- <button class="btn rounded-pill btn-primary waves-effect waves-light" type="button">
                                SEE ALL >>
                            </button> -->
                            <a href="{{ route('lead.show.inprogress') }}"
                                class="btn rounded-pill btn-primary waves-effect waves-light">
                                SEE ALL >>
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-borderless border-top">
                                <thead class="border-bottom">
                                    <tr>
                                        <th>LEAD #</th>
                                        <th>COMPANY</th>
                                        <th>SITE ADDRESS</th>
                                        <th>CONTACT</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($leadsonprogress as $nld)
                                        <tr>
                                            <td class="pt-5">
                                                <div class="d-flex flex-column">
                                                    <strong class="text-body text-nowrap">{{ $nld->slug }}</strong>
                                                    <small class="text-body text-nowrap">
                                                        {{ \Carbon\Carbon::parse($nld->created_at)->format('j F Y') }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td class="pt-5">
                                                <p class="mb-0 text-heading">{{ $nld->customer->company_name }} </p>
                                            </td>
                                            <td class="pt-5">
                                                <p class="mb-0 text-heading">{{ $nld->leadaddress->address ?? '' }},
                                                    {{ $nld->leadaddress->county ?? '' }}
                                                    {{ $nld->leadaddress->postcode ?? '' }}</p>
                                            </td>
                                            <td class="pt-5">
                                                <p class="mb-0 text-heading">{{ $nld->customer->contact_firstname }}
                                                    {{ $nld->customer->contact_lastname }}</p>
                                                <p class="mb-0 text-heading"> <i class="fa-solid fa-phone"></i>
                                                    {{ $nld->customer->contact_phone }}</p>
                                            </td>
                                            <td class="pt-5">
                                                <a href="{{ route('enquiries.show', $nld->slug) }}"
                                                    class="btn btn-sm btn-dark waves-effect waves-light">REVIEW</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--/ Leads Under Review -->





            </div>
        </div>
    @endrole
@endsection
