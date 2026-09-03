@extends('layouts.master')

@section('title', 'RAMS Check')

@section('scripts')
    @parent

    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>

    <script type="text/javascript">
        $(function() {
            let ajaxUrl = "{{ route('fjob-hs-check.index') }}";

            // 🔹 Read jobtype from query string if exists
            function getQueryParam(param) {
                let urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(param);
            }

            var table = $('.datatables-ajax').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: ajaxUrl,
                    data: function(d) {
                        // first try query param, then fallback to select filter
                        d.jobtype = getQueryParam('jobtype') || $('#jobTypeFilter').val();
                    }
                },

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'jobtype',
                        name: 'jobtype'
                    },
                    {
                        data: 'job_title',
                        name: 'job_title'
                    },
                    {
                        data: 'hs_check_title',
                        name: 'hs_check_title'
                    },
                    {
                        data: 'checked_by_user',
                        name: 'checked_by_user'
                    },
                    {
                        data: 'status',
                        name: 'status'
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
                <h4 class="mb-1">RAMS Check</h4>
            </div>
            <div class="d-flex align-content-center flex-wrap gap-4">
                <div class="d-flex gap-4">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @hasrole(['office-manager','health-safety-manager'])

                    <a href="{{ route('fjob-hs-check.create', ['jobtype' => request('jobtype')]) }}"
                        class="btn btn-primary">
                        <i class="icon-base ti tabler-plus"></i>
                        New RAMS Check
                    </a>
                    @endhasrole
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
                            <th>CHECK TITLE</th>
                            <th>CHECKED BY</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- / Content -->



@endsection
