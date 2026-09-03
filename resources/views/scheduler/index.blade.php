@extends('layouts.master')

@section('title', 'Scheduler')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bloodhound/bloodhound.js') }}"></script>
    <script src="{{ asset('assets/js/forms-pickers.js') }}"></script>
    <script src="{{ asset('assets/js/forms-tagify.js') }}"></script>
    <script src="{{ asset('assets/js/scheduler.js') }}"></script>
    
@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />

    <style>
        .calendar-table th,
        .calendar-table td {
            font-size: 12px;
            white-space: nowrap;
            text-align: center;
            padding: 3px;
        }

        .sticky-col {
            position: sticky;
            left: 0;
            background-color: #fff;
            z-index: 1;
        }

        .calendar-wrapper {
            overflow-x: auto;
            max-width: 100%;
        }

        .form-check-input:checked {
            background-color: #7367f0;
            border-color: #7367f0;
        }

        .form-check-input {
            width: 1.25rem;
        }

        .table.calendar-table {
            table-layout: fixed;
        }
    </style>
@endsection

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Real Scheduler -->
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
            <div class="d-flex flex-column justify-content-center">
                <h5 class="mb-1">Scheduler</h5>
            </div>
            <div class="d-flex align-content-center flex-wrap gap-4">
                <h5 class="mb-3">Worker Calendar - {{ now()->year }}</h5>
            </div>
        </div>

        <div class="card mb-4">
            <div
                class="card-header py-3 px-4 bg-white shadow-sm rounded d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <!-- Month Filter Form -->
                <form id="realMonthFilterForm" class="d-flex flex-wrap align-items-center gap-3">
                    <div class="d-flex align-items-center me-3">
                        <i class="icon-base ti tabler-calendar-week fs-4 me-2 text-primary"></i>
                        <h6 class="mb-0 fw-bold">Months</h6>
                    </div>

                    @foreach ($allMonths as $month)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input rounded" type="checkbox" name="months[]"
                                value="{{ $month }}" id="real_month_{{ $month }}"
                                {{ in_array($month, $selectedMonths) ? 'checked' : '' }}>
                            <label class="form-check-label"
                                for="real_month_{{ $month }}">{{ $month }}</label>
                        </div>
                    @endforeach

                    <button type="submit" class="btn btn-sm btn-primary d-flex align-items-center px-3 py-2">
                        <i class="icon-base ti tabler-adjustments-alt"></i> Filter
                    </button>
                </form>

                <!-- Schedule Button -->
                <div class="mt-3 mt-md-0">
                    <button type="button" class="btn btn-warning btn-md d-flex align-items-center px-3 py-2"
                        data-bs-toggle="modal" data-bs-target="#scheduleModal">
                        <i class="icon-base ti tabler-browser-plus"></i> Schedule
                    </button>
                </div>
            </div>

            <div class="card-body calendar-wrapper">
                <div id="real-calendar-table">
                    @include('partials.worker_calendar_table', [
                        'datesByMonth' => $datesByMonth,
                        'workers' => $workers,
                        'assignments' => $assignments,
                        'assignmentMap' => $assignmentMap,
                        'allDates' => $allDates,
                    ])
                </div>
            </div>
        </div>

        <!-- Real Schedule Modal -->
        <div class="modal fade" id="scheduleModal" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog">
                <form class="modal-content" id="scheduleForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Schedule Job</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-4">
                                <label class="form-label" for="multicol-job">Jobs</label>
                                <select id="multicol-job" class="select2 form-select" name="job_id">
                                    <option value="">Select</option>
                                    @foreach ($jobs as $job)
                                        <option value="{{ $job->id }}">{{$job->job_title}} ({{ $job->job_name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col mb-0">
                                <label for="select2Workers" class="form-label">staffs</label>
                                <select id="select2Workers" class="select2 form-select" name="workers[]" multiple>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col mb-0">
                                <label for="flatpickr-range" class="form-label">Date Range</label>
                                <input type="text" class="form-control" id="flatpickr-range" name="daterange"
                                    placeholder="YYYY-MM-DD to YYYY-MM-DD" />
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col mb-0">
                                <label for="instruction" class="form-label">Instructions</label>
                                <div class="form-control p-0">
                                    <div class="border-0 pb-6" id="instruction"></div>
                                </div>
                                <input type="hidden" name="instructions" id="instruction_input">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveScheduleBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Real Schedule Modal -->

        <hr class="my-5">

        <!-- Fake Scheduler -->
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
            <div class="d-flex flex-column justify-content-center">
                <h5 class="mb-1">Fake Scheduler</h5>
            </div>
            <div class="d-flex align-content-center flex-wrap gap-4">
                <h5 class="mb-3">Worker Calendar - {{ now()->year }}</h5>
            </div>
        </div>

        <div class="card mb-4">
            <div
                class="card-header py-3 px-4 bg-white shadow-sm rounded d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <form id="fakeMonthFilterForm" class="d-flex flex-wrap align-items-center gap-4">
                    <div class="d-flex align-items-center me-3">
                        <i class="icon-base ti tabler-calendar-week fs-4 me-2 text-primary"></i>
                        <h6 class="mb-0 fw-bold">Months</h6>
                    </div>

                    @foreach ($allMonths as $month)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input rounded" type="checkbox" name="months[]"
                                value="{{ $month }}" id="fake_month_{{ $month }}"
                                {{ in_array($month, $selectedMonths) ? 'checked' : '' }}>
                            <label class="form-check-label"
                                for="fake_month_{{ $month }}">{{ $month }}</label>
                        </div>
                    @endforeach

                    <button type="submit" class="btn btn-sm btn-primary d-flex align-items-center px-3 py-2">
                        <i class="icon-base ti tabler-adjustments-alt"></i> Filter
                    </button>
                </form>

                <!-- Schedule Button -->
                <div class="mt-3 mt-md-0">
                    <button type="button" class="btn btn-warning btn-sm d-flex align-items-center px-2 py-2"
                        data-bs-toggle="modal" data-bs-target="#fakeScheduleModal">
                        <i class="icon-base ti tabler-browser-plus"></i> Fake Schedule
                    </button>
                </div>
            </div>

            <div class="card-body calendar-wrapper">
                <div id="fake-calendar-table">
                    @include('partials.fake_worker_calender_table', [
                        'datesByMonthFake' => $datesByMonthFake,
                        'fakeworkers' => $fakeworkers,
                        'assignmentsFake' => $assignmentsFake,
                        'assignmentMapFake' => $assignmentMapFake,
                        'allDatesFake' => $allDatesFake,
                    ])
                </div>
            </div>
        </div>

        <!-- Fake schedule Modal -->
        <div class="modal fade" id="fakeScheduleModal" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog">
                <form class="modal-content" id="fakeScheduleForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Schedule Fake Job</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-4">
                                <label class="form-label" for="multicol-job">Jobs</label>
                                <select id="multicol-job" class="select2 form-select" name="fakejob_id">
                                    <option value="">Select</option>
                                    @foreach ($jobs as $job)
                                        <option value="{{ $job->id }}">{{$job->job_title}} ({{ $job->job_name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col mb-0">
                                <label for="select2Fakeworkers" class="form-label">staffs</label>
                                <select id="select2Fakeworkers" class="select2 form-select" name="fakeworkers[]" multiple>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col mb-0">
                                <label for="flatpickr-range-fake" class="form-label">Date Range</label>
                                <input type="text" class="form-control" id="flatpickr-range-fake" name="fakedaterange"
                                    placeholder="YYYY-MM-DD to YYYY-MM-DD" />
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col mb-0">
                                <label for="instruction" class="form-label">Instructions</label>
                                <div class="form-control p-0">
                                    <div class="border-0 pb-6" id="fakeinstruction"></div>
                                </div>
                                <input type="hidden" name="instructions" id="fakeinstruction_input">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveFakeScheduleBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Fake schedule Modal -->

    </div>
    <!-- / Content -->
@endsection
