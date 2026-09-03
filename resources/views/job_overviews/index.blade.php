@extends('layouts.master')

@section('title', 'Job Overviews')

@section('scripts')
    @parent

    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>

    <script type="text/javascript">
        $(function() {
            let ajaxUrl = "{{ route('job-overviews.index') }}";

            var table = $('.datatables-ajax').DataTable({
                processing: true,
                serverSide: true,
                ajax: ajaxUrl,

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'job_title',
                        name: 'job_title',
                        render: function(data, type, row) {
                            let html = '';
                            if (row.fjob && row.fjob.job_type) {
                                html += ' <span class="badge rounded-pill bg-info text-dark" style="font-size: 0.8em;">' + row.fjob.job_type + '</span><br/>';
                            }
                            if (row.job_title) {
                                html += row.job_title;
                            }
                            return html;
                        }
                    },
                    {
                        data: 'customer',
                        name: 'customer',
                        render: function(data, type, row) {
                            let html = '';

                            if (data.company_name) {
                                html += '<strong>' + data.company_name + '</strong><br>';
                            }

                            if (data.customer_name) {
                                html += data.customer_name + '<br>';
                            }

                            if (data.customer_email) {
                                html += '<i class="icon-base fas fa-envelope mb-2 mr-2"></i> ' +
                                    data.customer_email + '<br>';
                            }

                            if (data.customer_phone) {
                                html += '<i class="icon-base fas fa-phone mb-2 mr-2"></i> ' + data
                                    .customer_phone + '<br>';
                            }

                            if (data.customer_mobile) {
                                html += '<i class="icon-base fas fa-mobile mb-2"></i> ' + data
                                    .customer_mobile;
                            }

                            return html;
                        }
                    },
                    {
                        data: 'progress_percent',
                        name: 'progress_percent',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        
            
            // Handle form submission for editing user
            $('#jobprogress').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                const form = $(this);
                const formData = form.serialize();

                $.ajax({
                    url: $(this).attr('action'), // Update to your route
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    beforeSend: function() {
                        // Optional: show loader or disable submit button
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        $('#jobprogress').modal('hide');
                        setTimeout(function(){
                            //window.location.reload(); // This will reload the entire page
                            window.location.href = response.redirect;
                        },1000);
                    },
                    error: function(xhr) {
                        // Handle errors
                        let errors = xhr.responseJSON.errors;
                        let message = 'Update failed.';

                        if (errors) {
                            message = Object.values(errors).flat().join('\n');
                        }
                        toastr.error(message);
                    }
                });
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#modalJobProgress">
                        Start Job Tracking
                    </button>
                    <a href="{{ route('job.show.completed') }}" class="btn btn-label-warning"
                        @if (isset($completed) && $completed == true) disabled @endif>Completed</a>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-datatable table-responsive p-2">
                <table class="datatables-ajax table table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th>JOB TITLE</th>
                            <th>COMPANY INFO</th>
                            <th>PROGRESS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>


        <!-- Create Job Progress -->
        <div class="modal fade" id="modalJobProgress" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalJobProgressTitle">Job Progress </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="jobprogress" method="POST" action="{{ route('job-overviews.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-4">
                                    <label for="select2job" class="form-label">Select Job</label>
                                    <select id="select2job" class="select2 form-select" data-allow-clear="true"
                                        name="fjob_id" required>
                                        @foreach ($fjob as $job)
                                            <option value="{{ $job->id }}">
                                                {{ $job->job_name }} - {{ $job->job_title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col mb-0">
                                    <label for="startdate" class="form-label">Start Date</label>
                                    <input type="date" id="startdate" class="form-control" name="startdate" required />
                                </div>
                                <div class="col mb-0">
                                    <label for="enddate" class="form-label">End Date</label>
                                    <input type="date" id="enddate" class="form-control" name="enddate" required />
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col mb-0">
                                    <label for="jobpriority" class="form-label">Priority</label>
                                    <select id="jobpriority" name="jobpriority" class="form-select" data-allow-clear="true"
                                        required>
                                        <option value="high">High</option>
                                        <option value="medium">Medium</option>
                                        <option value="low">Low</option>
                                    </select>
                                </div>
                                <div class="col mb-0">
                                    <label for="notes" class="form-label">Note</label>
                                    <textarea class="form-control" name="note"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <!-- / Content -->

@endsection
