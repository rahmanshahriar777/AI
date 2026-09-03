@extends('layouts.master')

@section('title', 'Jobs')

@section('scripts')
    @parent

    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>

    <script type="text/javascript">
        $(function() {
            let ajaxUrl = "{{ route('job.index') }}";
            @if (!empty($completed) && $completed === true)
                ajaxUrl = "{{ route('job.show.completed') }}";
            @elseif (!empty($rejected) && $rejected === true)
                ajaxUrl = "{{ route('job.show.rejected') }}";
            @endif

            var table = $('.datatables-ajax').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    url: ajaxUrl,
                    type: 'GET',
                    data: function (d) {
                        d['filter_jobtype'] = $('#filter_jobtype').val();
                        d['filter_roofing'] = $('#filter_roofing').val();
                        d['filter_annual_maintenance'] = $('#filter_annual_maintenance').val();
                        d['filter_safety'] = $('#filter_safety').val();
                        d['filter_priority'] = $('#filter_priority').val();
                    }
                },

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'job_type',
                        name: 'job_type',
                        render: function(data, type, row) {
                            let html = data;
                            if (row.job_type_name) {
                                html = row.job_type_name;
                            }
                            return html;
                        }   
                    },
                    {
                        data: 'job_name',
                        name: 'job_name',
                        render: function(data, type, row) {
                            let badgeClass = 'bg-label-info'; // Default

                            switch (row.job_status.toLowerCase()) {
                                case 'new':
                                case 'accepted':
                                    badgeClass = 'info';
                                    break;
                                case 'inprogress':
                                    badgeClass = 'warning';
                                    break;
                                case 'completed':
                                    badgeClass = 'success';
                                    break;
                                case 'rejected':
                                    badgeClass = 'danger';
                                    break;
                                default:
                                    badgeClass = 'secondary';
                            }

                            // 🔹 Truncate job_name safely
                            let jobName = row.job_name || '';
                            let truncatedName = jobName.length > 30 ? jobName.substr(0, 30) + '…' :
                                jobName;

                            return `
                                <div class="d-flex flex-column">
                                    <span class="emp_name text-truncate text-heading fw-medium" 
                                        title="${jobName}">
                                        ${truncatedName}
                                    </span>
                                    <small class="emp_post text-truncate">
                                        <span class="badge rounded-pill bg-label-${badgeClass}">
                                            ${row.job_status}
                                        </span>
                                        ${moment(row.created_at).format('DD/MM/YYYY')}
                                    </small>
                                </div>
                            `;
                        }
                    },

                    {
                        data: 'customer_name',
                        name: 'customer_name',
                        render: function(data, type, row) {
                            let html = '';

                            if (row.company_name) {
                                html += '<strong>' + row.company_name + '</strong><br>';
                            }

                            if (row.customer_name) {
                                html += row.customer_name + '<br>';
                            }

                            if (row.customer_email) {
                                html += '<i class="icon-base fas fa-envelope mb-2 mr-2"></i> ' + row
                                    .customer_email + '<br>';
                            }

                            if (row.customer_phone) {
                                html += '<i class="icon-base fas fa-phone mb-2 mr-2"></i> ' + row
                                    .customer_phone + '<br>';
                            }

                            if (row.customer_mobile) {
                                html += '<i class="icon-base fas fa-mobile mb-2"></i> ' + row
                                    .customer_mobile;
                            }

                            return html;
                        }
                    },
                    {
                        data: 'enquiry',
                        name: 'enquiry',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        
            $('#btn_filter_job').click(function (e){
                $('.datatables-ajax').DataTable().ajax.reload()
            });                   
        });
    </script>
@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endsection

@section('content')

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
            <div class="d-flex flex-column justify-content-center">
                <h4 class="mb-1">Jobs</h4>
            </div>
            <div class="d-flex align-content-center flex-wrap gap-4">
                <div class="d-flex gap-4">
                    <a href="{{ route('job.show.rejected') }}" class="btn btn-label-danger"
                        @if (isset($rejected) && $rejected == true) disabled @endif>Rejected</a>
                    <a href="{{ route('job.show.completed') }}" class="btn btn-label-warning"
                        @if (isset($completed) && $completed == true) disabled @endif>Completed</a>
                </div>
            </div>
        </div>

        <div class="card p-2 mb-4">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="col-form-label" for="filter_jobtype">Job Type</label>
                        <select id="filter_jobtype" class="form-select" name="filter_jobtype" >
                            <option value="">All</option>
                            @foreach ($jobtype as $type)
                                <option value="{{ $type->slug }}"
                                    >
                                    {{ ucfirst(str_replace('_', ' ', $type->name)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="col-form-label" for="filter_roofing">Roofing</label>
                        <select id="filter_roofing" class="form-select" name="filter_roofing" >
                            <option value="">All</option>
                            <option value="repairs">Repairs</option>
                            <option value="overclad">Overclad</option>
                            <option value="large_works">Large Works</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="col-form-label" for="filter_annual_maintenance">Annual Maintenance</label>
                        <select id="filter_annual_maintenance" class="form-select" name="filter_annual_maintenance" >
                            <option value="">All</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="col-form-label" for="filter_safety">Safety</label>
                        <select id="filter_safety" class="form-select" name="filter_safety" >
                            <option value="">All</option>
                            <option value="install">Install</option>
                            <option value="repairs">Repairs</option>
                            <option value="testing">Testing</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="col-form-label" for="filter_priority">Priority</label>
                        <select id="filter_priority" class="form-select" name="filter_priority" >
                            <option value="">All</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label class="col-form-label" for="btn_filter_job">&nbsp;</label>
                        <button id="btn_filter_job" class="btn btn-primary btn-sm1" style="width: 100%;">Filter</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-datatable table-responsive p-2">
                <table class="datatables-ajax table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>JOB TYPE</th>
                            <th>JOB TITLE</th>
                            <th width="20%">COMPANY INFO</th>
                            <th>ENQUIRY</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- / Content -->

@endsection
