@extends('layouts.master')

@section('title', 'Quotations')

@section('scripts')
    @parent

    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>


    <script type="text/javascript">
        $(function() {
            var table = $('.datatables-ajax').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('quotations.lead', $leadid) }}",
                order: [[4, 'desc']],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },

                    {
                        data: 'quotation_version',
                        name: 'quotation_version',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'job_type_id',
                        name: 'job_type_id',
                        render: function(data, type, row) {
                            return row.job_type.name;
                        },
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'quotation_date',
                        name: 'quotation_date',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
    <script>
        $('#quotationForm').on('submit', function(e) {
            e.preventDefault();

            var leadId = $('input[name="lead_id"]').val();
            var templateId = $('#templateSelect').val();

            if (templateId === 'Select..' || !templateId) {
                alert('Please select a template.');
                return;
            }

            // Construct the route dynamically
            var url = '/quotation/create/' + leadId + '/' + templateId;
            window.location.href = url;
        });
    </script>
    <script>
        $(document).on('click', '.delete', function() {
            var id = $(this).data('id');

            if (confirm('Are you sure you want to delete this item?')) {
                $.ajax({
                    url: '/quotation/destroy/' + id,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        $('.datatables-ajax').DataTable().ajax.reload(); // Reload table
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message || 'An error occurred.');
                    }
                });
            }
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
                <div class="d-flex align-items-center">
                    <div class="mb-1">
                        <a href="{{ route('leads.show', $leaddata->slug) }}"
                            class="btn btn-icon btn-label-secondary waves-effect btn-sm me-2">
                            <span class="icon-base ti tabler-corner-up-left-double icon-20px"></span>
                        </a>
                        <span class="h4">QUOTATIONS FOR LEAD #
                            <a href="{{ route('leads.show', $leaddata->slug) }}">
                                <span class="badge bg-label-success me-1 ms-2">{{ $leaddata->lead_name }}</span>
                            </a>
                        </span>
                    </div>
                </div>
            </div>
            <div class="d-flex align-content-center flex-wrap gap-4">
                <div class="d-flex gap-4">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quotationModal">
                        Create New Quotation
                    </button>
                    <div class="modal fade" id="quotationModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="quotationModalTitle">Quotation Templates</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form id="quotationForm">
                                    <input type="hidden" name="lead_id" value="{{ $leaddata->id }}">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col mb-4">
                                                <label for="templateSelect" class="form-label">Select Template</label>
                                                <select class="form-select" id="templateSelect" aria-label="Select Template"
                                                    name="template_id">
                                                    <option selected>Select..</option>
                                                    @foreach ($quotationTemplates as $template)
                                                        <option value="{{ $template->id }}">
                                                            {{ $template->job_type_name }} -
                                                            {{ $template->template_slug }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="submit" class="btn btn-success">Go</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <a class="btn btn-label-warning">Archived</a>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-datatable table-responsive p-2">
                <table class="datatables-ajax table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>QUOTATION VERSION</th>
                            <th width="20%">JOB TYPE</th>
                            <th>STATUS</th>
                            <th>DATE</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- / Content -->

@endsection
