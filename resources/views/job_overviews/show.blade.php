@extends('layouts.master')

@section('title', 'Job Overview')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            $(document).on('click', '.task-edit', function() {
                let taskId = $(this).data('id');

                // Reset form
                $('#updateTaskForm')[0].reset();
                $('#taskid').val(taskId);

                // Fetch task data
                $.get("{{ url('job-overviews/show-task') }}/" + taskId, function(data) {
                    $('#tasktitleu').val(data.task_name);
                    $('#startfromu').val(data.start_date);
                    $('#endsatu').val(data.end_date);
                    $('#assigntou').val(data.assigned_to).trigger('change');
                    $('#statusu').val(data.status).trigger('change');
                    $('#notesu').val(data.description || '');

                    // Show modal
                    $('#updateAssignTasks').modal('show');
                });
            });
        });

    $(function() {
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
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endsection

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">
                            Job Progress for {{ $jobprogress->job_title }}
                        </h5>
                        <span>Customer: {{ $jobprogress->customers->company_name }}</span>
                        <div class="d-flex gap-2">

                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#assignTasks">
                                Assign Job
                            </button>

                            <a href="{{ route('job-overviews.index') }}" class="btn btn-secondary btn-sm">Back</a>
                            <a href="{{ route('job-overviews.export-gantt', $jobprogress->id) }}" class="btn btn-success btn-sm">
                                Download Excel
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive text-nowrap">

                        @php
                            use Carbon\Carbon;

                            // Range
                            $start = Carbon::parse($jobprogress->start_date);
                            $end = Carbon::parse($jobprogress->end_date);

                            // Group days by month
                            $months = [];
                            $period = new DatePeriod($start, new DateInterval('P1D'), $end->copy()->addDay());
                            foreach ($period as $date) {
                                $monthKey = $date->format('F Y');
                                $months[$monthKey][] = $date->format('d');
                            }
                        @endphp

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th rowspan="2">Task</th>
                                    <th rowspan="2">Notes</th>
                                    <th rowspan="2">Assigned To</th>
                                    <th rowspan="2">Progress</th>
                                    <th rowspan="2">Start</th>
                                    <th rowspan="2">End</th>
                                    @foreach ($months as $month => $days)
                                        <th colspan="{{ count($days) }}" class="text-center">{{ $month }}</th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach ($months as $month => $days)
                                        @foreach ($days as $day)
                                            <th>{{ $day }}</th>
                                        @endforeach
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($jobprogress->tasks )<1)
                                <tr><td colspan="{{ count($months)+6 }}">No task found</td></tr>                                
                                @endif
                                @foreach ($jobprogress->tasks as $task)
                                    <tr>
                                        <td>
                                            <span class="task-edit text-decoration-underline text-primary"
                                                data-id="{{ $task->id }}" style="cursor: pointer;">
                                                {{ $task->task_name }}
                                            </span>
                                        </td>
                                        <td>{{ $task->description ?? 'N/A' }}</td>
                                        <td>{{ optional($task->resource)->name ?? 'N/A' }}</td>
                                        @php
                                            $start = \Carbon\Carbon::parse($task->start_date);
                                            $end = \Carbon\Carbon::parse($task->end_date);
                                            $now = \Carbon\Carbon::now();

                                            $totalDays = $start->diffInDays($end) + 1;
                                            $completedDays = 0;

                                            if ($now->lt($start)) {
                                                $completedDays = 0;
                                            } elseif ($now->gt($end)) {
                                                $completedDays = $totalDays;
                                            } else {
                                                $completedDays = $start->diffInDays($now) + 1;
                                            }

                                            $percentage =
                                                $totalDays > 0 ? round(($completedDays / $totalDays) * 100) : 0;
                                        @endphp
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: {{ $percentage }}%;"
                                                    aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ $percentage }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($task->start_date)->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($task->end_date)->format('d M Y') }}</td>

                                        {{-- Generate date cells --}}

                                        @php
                                            $allDates = [];
                                            $period = \Carbon\CarbonPeriod::create(
                                                $jobprogress->start_date,
                                                $jobprogress->end_date,
                                            );
                                            foreach ($period as $date) {
                                                $allDates[] = $date->format('Y-m-d');
                                            }
                                        @endphp

                                        @foreach ($allDates as $date)
                                            @php
                                                $start = \Carbon\Carbon::parse($task->start_date);
                                                $end = \Carbon\Carbon::parse($task->end_date);
                                                $current = \Carbon\Carbon::parse($date);
                                                $now = \Carbon\Carbon::now();

                                                // Determine color class based on status and date
                                                $status = $task->status ?? 'not_started';
                                                $colorClass = '';

                                                if (
                                                    $status === 'completed' ||
                                                    ($current->lt($now) &&
                                                        $status !== 'on_hold' &&
                                                        $status !== 'cancelled')
                                                ) {
                                                    $colorClass = 'bg-success text-white';
                                                } elseif ($status === 'not_started') {
                                                    $colorClass = 'bg-info text-white';
                                                } elseif ($status === 'in_progress') {
                                                    $colorClass = 'bg-warning text-dark';
                                                } elseif ($status === 'on_hold' || $status === 'cancelled') {
                                                    $colorClass = 'bg-dark text-white';
                                                }
                                            @endphp

                                            @if ($current->between($start, $end))
                                                @if ($current->isSameDay($start))
                                                    {{-- First day of task: show name --}}
                                                    <td class="{{ $colorClass }} text-center"
                                                        title="{{ $task->task_name }}">
                                                    </td>
                                                @else
                                                    {{-- Continuation days: just fill --}}
                                                    <td class="{{ $colorClass }}"></td>
                                                @endif
                                            @else
                                                <td></td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </div>

                    {{-- create jobs modal  --}}
                    <div class="modal fade" id="assignTasks" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="assignTasksTitle">Assign Task</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form id="jobprogress" method="POST" action="{{ route('job-overviews.store-task') }}">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col mb-4">
                                                <label for="tasktitle" class="form-label">Task</label>
                                                <input type="text" id="tasktitle" class="form-control" name="tasktitle"
                                                    placeholder="Enter task" />
                                            </div>
                                        </div>
                                        <div class="row g-4">
                                            <div class="col mb-0">
                                                <label for="startfrom" class="form-label">Start From</label>
                                                <input type="date" id="startfrom" name="startfrom" class="form-control"
                                                    min="{{ $jobprogress->start_date }}"
                                                    max="{{ $jobprogress->end_date }}" />
                                            </div>
                                            <div class="col mb-0">
                                                <label for="endsat" class="form-label">Ends At</label>
                                                <input type="date" id="endsat" name="endsat" class="form-control"
                                                    min="{{ $jobprogress->start_date }}"
                                                    max="{{ $jobprogress->end_date }}" />
                                            </div>
                                        </div>

                                        <div class="row g-4">
                                            <div class="col mb-0">
                                                <label for="assignto" class="form-label">Assign To</label>
                                                <select id="assignto" class="select2 form-select" data-allow-clear="true"
                                                    name="assignto" required>
                                                    @foreach ($staffs as $staff)
                                                        <option value="{{ $staff->id }}">
                                                            {{ $staff->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col mb-0">
                                                <label for="notes" class="form-label">Notes</label>
                                                <input type="text" id="notes" name="notes"
                                                    class="form-control" />
                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="fjobpregressid" value="{{ $jobprogress->id }}" />
                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="submit" class="btn btn-primary">Create Task</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- edit jobs modal --}}
                    <div class="modal fade" id="updateAssignTasks" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateAssignTasksTitle">Update Assign Task</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form id="updateTaskForm" method="POST"
                                    action="{{ route('job-overviews.update-task') }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col mb-4">
                                                <label for="tasktitleu" class="form-label">Task</label>
                                                <input type="text" id="tasktitleu" class="form-control"
                                                    name="tasktitle" placeholder="Enter task" />
                                            </div>
                                        </div>

                                        <div class="row g-4">
                                            <div class="col mb-0">
                                                <label for="startfromu" class="form-label">Start From</label>
                                                <input type="date" id="startfromu" name="startfrom"
                                                    class="form-control" min="{{ $jobprogress->start_date }}"
                                                    max="{{ $jobprogress->end_date }}" />
                                            </div>
                                            <div class="col mb-0">
                                                <label for="endsatu" class="form-label">Ends At</label>
                                                <input type="date" id="endsatu" name="endsat" class="form-control"
                                                    min="{{ $jobprogress->start_date }}"
                                                    max="{{ $jobprogress->end_date }}" />
                                            </div>
                                        </div>

                                        <div class="row g-4">
                                            <div class="col mb-0">
                                                <label for="assigntou" class="form-label">Assign To</label>
                                                <select id="assigntou" class="select2 form-select"
                                                    data-allow-clear="true" name="assignto" required>
                                                    @foreach ($staffs as $staff)
                                                        <option value="{{ $staff->id }}">
                                                            {{ $staff->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col mb-0">
                                                <label for="notesu" class="form-label">Notes</label>
                                                <input type="text" id="notesu" name="notes"
                                                    class="form-control" />
                                            </div>
                                        </div>

                                        <div class="row g-4">
                                            <div class="col mb-0">
                                                <label for="statusu" class="form-label">Status</label>
                                                <select id="statusu" class="select2 form-select" name="status"
                                                    required>
                                                    <option value="not_started">Not Started</option>
                                                    <option value="in_progress">In Progress</option>
                                                    <option value="completed">Completed</option>
                                                    <option value="on_hold">On Hold</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <input type="hidden" name="taskid" id="taskid" value="">
                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="submit" class="btn btn-primary">Update Task</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->

@endsection
