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
            @foreach ($jobprogress->tasks as $task)
                <tr>
                    <td>
                        <span class="task-edit text-decoration-underline text-primary" data-id="{{ $task->id }}"
                            style="cursor: pointer;">
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

                        $percentage = $totalDays > 0 ? round(($completedDays / $totalDays) * 100) : 0;
                    @endphp
                    <td>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%;"
                                aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $percentage }}%
                            </div>
                        </div>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($task->start_date)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($task->end_date)->format('d M Y') }}</td>

                    {{-- Generate date cells --}}
                    @php
                        $allDates = [];
                        $period = \Carbon\CarbonPeriod::create($jobprogress->start_date, $jobprogress->end_date);
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

                            // Determine cell background color (inline for Excel)
                            $status = $task->status ?? 'not_started';
                            $bgColor = '';

                            if ($status === 'completed') {
                                $bgColor = 'background-color: #28a745; color: #ffffff;'; // green
                            } elseif ($status === 'not_started') {
                                $bgColor = 'background-color: #17a2b8; color: #ffffff;'; // blue
                            } elseif ($status === 'in_progress') {
                                $bgColor = 'background-color: #ffc107; color: #000000;'; // yellow
                            } elseif ($status === 'on_hold' || $status === 'cancelled') {
                                $bgColor = 'background-color: #343a40; color: #ffffff;'; // dark
                            } elseif ($current->lt($now)) {
                                $bgColor = 'background-color: #28a745; color: #ffffff;'; // treat past days as completed
                            }
                        @endphp

                        @if ($current->between($start, $end))
                            <td style="{{ $bgColor }}"></td>
                        @else
                            <td></td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
