@extends('layouts.master')

@section('title', 'Hs Tools')

@section('scripts')
    @parent

    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>

    <script type="text/javascript">
        $(function() {
            var table = $('.datatables-ajax').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('hs-tools.show', ['id' => $hsTool->id]) }}",

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },

                    {
                        data: 'checkup_date',
                        name: 'checkup_date',
                    },
                    {
                        data: 'checkup_name',
                        name: 'checkup_name',
                    },
                    {
                        data: 'next_checkup_date',
                        name: 'next_checkup_date',
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
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />

    <!-- Row Group CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />

@endsection

@section('content')

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
            <div class="d-flex flex-column justify-content-center">
                <h4 class="mb-1">{{ $hsTool->tool_name }}</h4>
                <div class="text-muted">Details: {{ $hsTool->description }}</div>
            </div>
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center row-gap-4">
                <div class="d-flex gap-4">
                    <a href="{{ route('hs-tools.index') }}" class="btn btn-primary">Back</a>
                    <a href="{{ route('hs-tools.createcheck', ['id' => $hsTool->id]) }}" class="btn btn-success">Add
                        Checkup</a>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatables-ajax table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>CHECKUP DATE</th>
                            <th>TITLE</th>
                            <th>NEXT CHECKUP DATE</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- / Content -->

@endsection
