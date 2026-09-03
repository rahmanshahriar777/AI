@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-6" style="display: none;">
        
        <!-- Earning Reports -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header pb-0 d-flex justify-content-between">
                    <div class="card-title mb-0">
                        <h5 class="mb-1">Earning Reports</h5>
                        <p class="card-subtitle">Weekly Earnings Overview</p>
                    </div>
                    <div class="dropdown">
                        <button
                            class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-2 me-n1"
                            type="button"
                            id="earningReportsId"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i class="icon-base ti tabler-dots-vertical icon-md text-body-secondary"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="earningReportsId">
                            <a class="dropdown-item" href="javascript:void(0);">View More</a>
                            <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row align-items-center g-md-8">
                        <div class="col-12 col-md-5 d-flex flex-column">
                            <div class="d-flex gap-2 align-items-center mb-3 flex-wrap">
                                <h2 class="mb-0">$0</h2>
                                <div class="badge rounded bg-label-success">+0%</div>
                            </div>
                            <small class="text-body">You informed of this week compared to last week</small>
                        </div>
                        <div class="col-12 col-md-7 ps-xl-8">
                            <div id="weeklyEarningReports"></div>
                        </div>
                    </div>
                    <div class="border rounded p-5 mt-5">
                        <div class="row gap-4 gap-sm-0">
                            <div class="col-12 col-sm-4">
                                <div class="d-flex gap-2 align-items-center">
                                    <div class="badge rounded bg-label-primary p-1">
                                        <i class="icon-base ti tabler-currency-dollar icon-18px"></i>
                                    </div>
                                    <h6 class="mb-0 fw-normal">Earnings</h6>
                                </div>
                                <h4 class="my-2">$0</h4>
                                <div class="progress w-75" style="height: 4px">
                                    <div
                                        class="progress-bar"
                                        role="progressbar"
                                        style="width: 65%"
                                        aria-valuenow="65"
                                        aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="d-flex gap-2 align-items-center">
                                    <div class="badge rounded bg-label-info p-1">
                                        <i class="icon-base ti tabler-chart-pie-2 icon-18px"></i>
                                    </div>
                                    <h6 class="mb-0 fw-normal">Profit</h6>
                                </div>
                                <h4 class="my-2">$0</h4>
                                <div class="progress w-75" style="height: 4px">
                                    <div
                                        class="progress-bar bg-info"
                                        role="progressbar"
                                        style="width: 50%"
                                        aria-valuenow="50"
                                        aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="d-flex gap-2 align-items-center">
                                    <div class="badge rounded bg-label-danger p-1">
                                        <i class="icon-base ti tabler-brand-paypal icon-18px"></i>
                                    </div>
                                    <h6 class="mb-0 fw-normal">Expense</h6>
                                </div>
                                <h4 class="my-2">$0</h4>
                                <div class="progress w-75" style="height: 4px">
                                    <div
                                        class="progress-bar bg-danger"
                                        role="progressbar"
                                        style="width: 65%"
                                        aria-valuenow="65"
                                        aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Earning Reports -->

        <!-- Support Tracker -->
        <div class="col-12 col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title mb-0">
                        <h5 class="mb-1">Support Tracker</h5>
                        <p class="card-subtitle">Last 7 Days</p>
                    </div>
                    <div class="dropdown">
                        <button
                            class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-2 me-n1"
                            type="button"
                            id="supportTrackerMenu"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i class="icon-base ti tabler-dots-vertical icon-md text-body-secondary"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="supportTrackerMenu">
                            <a class="dropdown-item" href="javascript:void(0);">View More</a>
                            <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                        </div>
                    </div>
                </div>
                <div class="card-body row">
                    <div class="col-12 col-sm-4">
                        <div class="mt-lg-4 mt-lg-2 mb-lg-6 mb-2">
                            <h2 class="mb-0">164</h2>
                            <p class="mb-0">Total Tickets</p>
                        </div>
                        <ul class="p-0 m-0">
                            <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
                                <div class="badge rounded bg-label-primary p-1_5">
                                    <i class="icon-base ti tabler-ticket icon-md"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-nowrap">New Tickets</h6>
                                    <small class="text-body-secondary">142</small>
                                </div>
                            </li>
                            <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
                                <div class="badge rounded bg-label-info p-1_5">
                                    <i class="icon-base ti tabler-circle-check icon-md"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-nowrap">Open Tickets</h6>
                                    <small class="text-body-secondary">28</small>
                                </div>
                            </li>
                            <li class="d-flex gap-4 align-items-center pb-1">
                                <div class="badge rounded bg-label-warning p-1_5">
                                    <i class="icon-base ti tabler-clock icon-md"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-nowrap">Response Time</h6>
                                    <small class="text-body-secondary">1 Day</small>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-12 col-md-8">
                        <div id="supportTracker"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Support Tracker -->
        
    </div>
</div>

@endsection