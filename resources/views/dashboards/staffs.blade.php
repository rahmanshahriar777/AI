@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
            <!-- Assigned Tasks -->
            <div class="col-md-8 col-12">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0 me-2">Assigned Tasks</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless border-top">
                            <thead class="border-bottom">
                                <tr>
                                    <th>Date</th>
                                    <th>Task</th>
                                    <th>Status</th>
                                    <th>Job</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="pt-5">
                                        <div class="d-flex flex-column">
                                            <small class="text-body text-nowrap">17 Mar 2022</small>
                                        </div>
                                    </td>
                                    <td class="pt-5">
                                        <div class="d-flex justify-content-start align-items-center">
                                            <h6 class="mb-0">Create new project for client</h6>
                                        </div>
                                    </td>
                                    <td class="pt-5"><span class="badge bg-label-success">Verified</span></td>
                                    <td class="pt-5">
                                        <p class="mb-0 text-heading">+$1,678</p>
                                    </td>
                                </tr>
                               
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--/ Last Transaction -->

            <!-- Activity Timeline -->
            <div class="col-xxl-4 col-md-4 col-12">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title m-0 me-2 pt-1 mb-2 d-flex align-items-center">
                            <i class="icon-base ti tabler-list-details me-3"></i> Activity Timeline
                        </h5>
                    </div>
                    <div class="card-body pb-xxl-0">
                        <ul class="timeline mb-0">
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-primary"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-3">
                                        <h6 class="mb-0">12 Invoices have been paid</h6>
                                        <small class="text-body-secondary">12 min ago</small>
                                    </div>
                                    <p class="mb-2">Invoices have been paid to the company</p>
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="badge bg-lighter rounded-3">
                                            <img src="../../assets//img/icons/misc/pdf.png" alt="img" width="15"
                                                class="me-2" />
                                            <span class="h6 mb-0 text-body">invoices.pdf</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            
                            
                        </ul>
                    </div>
                </div>
            </div>
            <!--/ Activity Timeline -->
        </div>
    </div>
    <!-- / Content -->

@endsection
