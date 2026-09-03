@extends('layouts.master')

@section('title', 'Dashboard')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/cards-advance.css') }}" />
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
            <!-- Support Tracker -->
            <div class="col-4 col-md-4">
                <div class="card h-100">
                    
                    <div class="card-body row">
                        <div class="col-12 col-sm-4">
                            <div class="mt-lg-4 mt-lg-2 mb-lg-6 mb-2">
                                <h2 class="mb-0"></h2>
                            </div>
                            <ul class="p-0 m-0">
                                <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
                                    <div class="badge rounded bg-label-primary p-1_5">
                                        <i class="icon-base ti tabler-checkup-list icon-md"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-nowrap">RAMS Compliance</h6>
                                        <small class="text-body-secondary"></small>
                                    </div>
                                </li>
                                <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
                                    <div class="badge rounded bg-label-info p-1_5">
                                        <i class="icon-base ti tabler-brand-databricks icon-md"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-nowrap">Ongoing Trainings</h6>
                                        <small class="text-body-secondary"></small>
                                    </div>
                                </li>
                                <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
                                    <div class="badge rounded bg-label-warning p-1_5">
                                        <i class="icon-base ti tabler-tool icon-md"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-nowrap">Available Tools</h6>
                                        <small class="text-body-secondary"></small>
                                    </div>
                                </li>
                                <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
                                    <div class="badge rounded bg-label-dark p-1_5">
                                        <i class="icon-base ti tabler-shield-check icon-md"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-nowrap">Available PPE</h6>
                                        <small class="text-body-secondary"></small>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Support Tracker -->

            <!-- Jobs Under Review -->
            <div class="col-4 col-md-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0 me-2">SCHEDULER</h5>
                        <button class="btn rounded-pill btn-warning waves-effect waves-light" type="button">
                            SEE ALL >>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless border-top">
                            <thead class="border-bottom">
                                <tr>
                                    <th>TYPE</th>
                                    <th>NEXT DATE</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--/ Jobs Under Review -->

        </div>
    </div>

@endsection-
