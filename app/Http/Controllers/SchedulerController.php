<?php

namespace App\Http\Controllers;

use App\Models\FakeJobSchedule;
use App\Models\FakeJobScheduleAttachment;
use App\Models\FakeJobScheduleWorker;
use App\Models\Fjob;
use App\Models\JobSchedule;
use App\Models\JobScheduleAttachment;
use App\Models\JobScheduleWorker;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Container\Attributes\Storage;
use Carbon\Carbon;
use DB;

class SchedulerController extends Controller
{

    public function index(Request $request)
    {
        $allMonths = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        $selectedMonths = $request->get('months', [now()->format('F')]);

        $data = $this->generateCalendarData($selectedMonths);
        $fakedata = $this->generateFakeCalendarData($selectedMonths);

        $jobs = Fjob::whereIn('job_status', ['accepted', 'inprogress'])->get();
        $staffs = User::role('staff')->orderBy('name', 'asc')->get();
        $data = array_merge($data, $fakedata);

        return view('scheduler.index', array_merge($data, [
            'allMonths' => $allMonths,
            'selectedMonths' => $selectedMonths,
            'jobs' => $jobs,
            'staffs' => $staffs,
        ]));
    }

    public function getSchedulerCalendarData(Request $request)
    {
        $selectedMonths = $request->get('months', []);
        $data = $this->generateCalendarData($selectedMonths);

        $html = view('partials.worker_calendar_table', $data)->render();

        return response()->json(['html' => $html]);
    }

    private function generateCalendarData(array $selectedMonths)
    {
        if (empty($selectedMonths)) return [];

        $startYear = now()->year;
        $datesByMonth = [];

        foreach ($selectedMonths as $month) {
            $start = Carbon::parse("first day of $month $startYear");
            $end = Carbon::parse("last day of $month $startYear");

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $datesByMonth[$month][] = $date->toDateString();
            }
        }

        $allDates = collect($datesByMonth)->flatten()->unique()->sort()->values();
        $workers = User::role('staff')->orderBy('name', 'asc')->get();

        $startDate = Carbon::parse("first day of " . $selectedMonths[0] . " $startYear")->startOfDay();
        $endDate = Carbon::parse("last day of " . end($selectedMonths) . " $startYear")->endOfDay();

        $assignments = JobScheduleWorker::with('jobSchedule')
            ->whereBetween('assigned_date', [$startDate, $endDate])
            ->orWhereBetween('completion_date', [$startDate, $endDate])
            ->get();

        $assignmentMap = [];

        foreach ($assignments as $assignment) {
            $start = Carbon::parse($assignment->assigned_date);
            $end = Carbon::parse($assignment->completion_date);

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $assignmentMap[$assignment->user_id][$date->toDateString()][] = $assignment;
            }
        }

        return compact('datesByMonth', 'workers', 'assignments', 'assignmentMap', 'allDates');
    }

    public function getFakeSchedulerCalendarData(Request $request)
    {
        $selectedMonths = $request->get('months', []);
        $data = $this->generateFakeCalendarData($selectedMonths);

        $htmlf = view('partials.fake_worker_calendar_table', $data)->render();

        return response()->json(['htmlf' => $htmlf]);
    }

    private function generateFakeCalendarData(array $selectedMonths)
    {
        if (empty($selectedMonths)) return [];

        $startYear = now()->year;
        $datesByMonthFake = [];

        foreach ($selectedMonths as $month) {
            $start = Carbon::parse("first day of $month $startYear");
            $end = Carbon::parse("last day of $month $startYear");

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $datesByMonthFake[$month][] = $date->toDateString();
            }
        }

        $allDatesFake = collect($datesByMonthFake)->flatten()->unique()->sort()->values();
        $fakeworkers = User::role('staff')->orderBy('name', 'asc')->get();

        $startDate = Carbon::parse("first day of " . $selectedMonths[0] . " $startYear")->startOfDay();
        $endDate = Carbon::parse("last day of " . end($selectedMonths) . " $startYear")->endOfDay();

        $assignmentsFake = FakeJobScheduleWorker::with('fakeJobSchedule')
            ->whereBetween('assigned_date', [$startDate, $endDate])
            ->orWhereBetween('completion_date', [$startDate, $endDate])
            ->get();

        $assignmentMapFake = [];

        foreach ($assignmentsFake as $assignment) {
            $start = Carbon::parse($assignment->assigned_date);
            $end = Carbon::parse($assignment->completion_date);

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $assignmentMapFake[$assignment->user_id][$date->toDateString()][] = $assignment;
            }
        }

        return compact('datesByMonthFake', 'fakeworkers', 'assignmentsFake', 'assignmentMapFake', 'allDatesFake');
    }

    public function showSchedule($id)
    {
        $schedule = JobSchedule::with([
            'job' => function ($q) {
                $q->select('id', 'job_description', 'customer_id', 'job_type', 'job_status');
            },
            'job.jobaddress' => function ($q) {
                $q->select('id', 'fjob_id', 'address', 'country', 'county', 'contact_firstname', 'contact_lastname', 'contact_phone', 'contact_mobile', 'contact_email');
            },
            'job.customer' => function ($q) {
                $q->select('id', 'company_name');
            },
            'workers'
        ])->findOrFail($id);

        $workerslist = User::role('staff')->orderBy('name', 'asc')->get();

        return response()->json([
            'schedule' => $schedule,
            'workers' => $schedule->workers,
            'job' => $schedule->job,
            'customer' => $schedule->job->customer,
            'jobAddress' => $schedule->job->jobaddress,
            'workerslist' => $workerslist,
        ]);
    }

    public function storeJobSchedule(Request $request)
    {
        $validated = $request->validate([
            'job_id'      => 'required|exists:fjobs,id',
            'daterange'   => 'required|string',
            'workers'     => 'array',
            'workers.*'   => 'exists:users,id',
            'instructions' => 'nullable|string',
        ]);

        // Extract start and end dates
        $dateParts = explode(' to ', $validated['daterange']);
        $startDate = Carbon::parse($dateParts[0])->toDateString();
        $endDate = isset($dateParts[1])
            ? Carbon::parse($dateParts[1])->toDateString()
            : $startDate; // Single date

        // Validate date logic
        if ($startDate > $endDate) {
            return response()->json(['error' => 'Start date must be before end date'], 422);
        }



        // ✅ Check for duplicate job schedule with same job_id, workers, and date range
        $workerIds = $validated['workers'] ?? [];

        $existing = JobSchedule::where('job_id', $validated['job_id'])
            ->whereDate('start_date', $startDate)
            ->whereDate('end_date', $endDate)
            ->whereHas('workers', function ($query) use ($workerIds) {
                $query->whereIn('user_id', $workerIds);
            })
            ->whereRaw("
            (
                SELECT COUNT(*) 
                FROM job_schedule_workers 
                WHERE job_schedule_id = job_schedules.id 
                AND user_id IN (" . implode(',', array_fill(0, count($workerIds), '?')) . ")
            ) = ?
            ", array_merge($workerIds, [count($workerIds)]))
            ->first();
        // dd($existing);
        if ($existing) {
            return response()->json(['error' => 'Duplicate job schedule already exists'], 409);
        }

        // ✅ Create job schedule
        $jobSchedule = JobSchedule::create([
            'job_id'       => $validated['job_id'],
            'job_name'     => Fjob::find($validated['job_id'])->job_name,
            'job_title'     => Fjob::find($validated['job_id'])->job_title,
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'instructions' => $validated['instructions'],
            'status'       => 'assigned',
        ]);

        // ✅ Assign workers
        foreach ($workerIds as $workerId) {
            JobScheduleWorker::create([
                'job_schedule_id' => $jobSchedule->id,
                'user_id'         => $workerId,
                'assigned_date'   => $startDate,
                'completion_date' => $endDate,
                'status'          => 'assigned',
            ]);
        }

        return response()->json([
            'message'      => 'Job schedule created successfully',
            'job_schedule' => $jobSchedule
        ], 201);
    }

    public function updateJobSchedule(Request $request, $id)
    {
        // dd($request->all());
        $validated = $request->validate([
            'daterange'   => 'required|string',
            'workers'     => 'array',
            'workers.*'   => 'exists:users,id',
            'instructions' => 'nullable|string',
            'status'      => 'nullable|in:assigned,inprogress,completed,cancelled',
        ]);

        // Extract start and end dates
        [$startRaw, $endRaw] = explode(' to ', $validated['daterange']);
        $startDate = Carbon::parse($startRaw)->toDateString();
        $endDate   = Carbon::parse($endRaw)->toDateString();

        // Validate date range
        if ($startDate >= $endDate) {
            return response()->json(['error' => 'Start date must be before end date'], 422);
        }

        // Find the Job Schedule
        $jobSchedule = JobSchedule::findOrFail($id);
        $jobSchedule->update([
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'instructions' => $validated['instructions'],
            'status'      => $validated['status'] ?? 'assigned',
        ]);

        // Update workers
        JobScheduleWorker::where('job_schedule_id', $id)->delete();
        foreach ($validated['workers'] ?? [] as $workerId) {
            JobScheduleWorker::create([
                'job_schedule_id' => $jobSchedule->id,
                'user_id'       => $workerId,
                'assigned_date'   => $startDate,
                'completion_date' => $endDate,
                'status'          => 'assigned',
            ]);
        }

        return response()->json([
            'message'      => 'Job schedule updated successfully',
        ]);
    }

    public function deleteSchedule(Request $request, $id)
    {
        $schedule = JobSchedule::find($id);
        if (!$schedule) {
            return response()->json(['error' => 'Schedule not found'], 404);
        }

        // Delete associated workers
        JobScheduleWorker::where('job_schedule_id', $id)->delete();

        // Handle attachments
        $attachments = JobScheduleAttachment::where('job_schedule_id', $id)->get();
        foreach ($attachments as $attachment) {
            $disk = $attachment->source === 's3' ? 's3' : 'public';
            $path = $attachment->attachment_path;

            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }

            $attachment->delete();
        }

        // Finally, delete the schedule
        $schedule->delete();

        return response()->json(['message' => 'Schedule deleted successfully']);
    }

    /* Fake Job Schedule Methods */
    public function showFakeSchedule($id)
    {
        $schedule = FakeJobSchedule::with([
            'job' => function ($q) {
                $q->select('id', 'job_description', 'customer_id', 'job_type', 'job_status');
            },
            'job.jobaddress' => function ($q) {
                $q->select('id', 'fjob_id', 'address', 'country', 'county', 'contact_firstname', 'contact_lastname', 'contact_phone', 'contact_mobile', 'contact_email');
            },
            'job.customer' => function ($q) {
                $q->select('id', 'company_name');
            },
            'workers'
        ])->findOrFail($id);

        $workerslist = User::role('staff')->orderBy('name', 'asc')->get();

        return response()->json([
            'schedule' => $schedule,
            'workers' => $schedule->workers,
            'job' => $schedule->job,
            'customer' => $schedule->job->customer,
            'jobAddress' => $schedule->job->jobaddress,
            'workerslist' => $workerslist,
        ]);
    }

    public function storeFakeJobSchedule(Request $request)
    {
        $validated = $request->validate([
            'fakejob_id'        => 'required|exists:fjobs,id',
            'fakedaterange'     => 'required|string',
            'fakeworkers'       => 'array',
            'fakeworkers.*'     => 'exists:users,id',
            'instructions'  => 'nullable|string',
        ]);

        // Parse start and end dates
        $dateParts = explode(' to ', $validated['fakedaterange']);
        $startDate = Carbon::parse($dateParts[0])->toDateString();
        $endDate = isset($dateParts[1]) ? Carbon::parse($dateParts[1])->toDateString() : $startDate;

        if ($startDate > $endDate) {
            return response()->json(['error' => 'Start date must be before end date'], 422);
        }

        $workerIds = $validated['fakeworkers'] ?? [];

        // Check for existing identical assignments
        $existing = FakeJobSchedule::where('job_id', $validated['fakejob_id'])
            ->whereDate('start_date', $startDate)
            ->whereDate('end_date', $endDate)
            ->whereHas('workers', function ($query) use ($workerIds) {
                $query->whereIn('user_id', $workerIds);
            })
            ->whereRaw("
            (
                SELECT COUNT(*) 
                FROM fake_job_schedule_workers 
                WHERE fake_job_schedule_id = fake_job_schedules.id 
                AND user_id IN (" . implode(',', array_fill(0, count($workerIds), '?')) . ")
            ) = ?
        ", array_merge($workerIds, [count($workerIds)]))
            ->first();

        if ($existing) {
            return response()->json(['error' => 'Duplicate job schedule already exists'], 409);
        }

        $fjob = Fjob::findOrFail($validated['fakejob_id']);

        // Create the fake job schedule
        $jobSchedule = FakeJobSchedule::create([
            'job_id'       => $fjob->id,
            'job_name'     => $fjob->job_name,
            'job_title'    => $fjob->job_title,
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'instructions' => $validated['instructions'],
            'status'       => 'pending',
        ]);

        // Create worker assignments
        foreach ($workerIds as $workerId) {
            FakeJobScheduleWorker::create([
                'fake_job_schedule_id' => $jobSchedule->id,
                'user_id'              => $workerId,
                'assigned_date'        => $startDate,
                'completion_date'      => $endDate,
                'status'               => 'assigned',
            ]);
        }

        return response()->json([
            'message'      => 'Fake job schedule created successfully.',
            'job_schedule' => $jobSchedule,
        ], 201);
    }

    public function updateFakeJobSchedule(Request $request, $id)
    {
        // dd($request->all());
        $validated = $request->validate([
            'daterange'   => 'required|string',
            'workers'     => 'array',
            'workers.*'   => 'exists:users,id',
            'instructionsf' => 'nullable|string',
        ]);

        // Extract start and end dates
        [$startRaw, $endRaw] = explode(' to ', $validated['daterange']);
        $startDate = Carbon::parse($startRaw)->toDateString();
        $endDate   = Carbon::parse($endRaw)->toDateString();

        // Validate date range
        if ($startDate >= $endDate) {
            return response()->json(['error' => 'Start date must be before end date'], 422);
        }

        // Find the Job Schedule
        $jobSchedule = FakeJobSchedule::findOrFail($id);

        $jobSchedule->update([
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'instructions' => $validated['instructionsf'],
            'status'      => 'pending',
        ]);

        // Update workers
        FakeJobScheduleWorker::where('fake_job_schedule_id', $id)->delete();
        foreach ($validated['workers'] ?? [] as $workerId) {
            FakeJobScheduleWorker::create([
                'fake_job_schedule_id' => $jobSchedule->id,
                'user_id'       => $workerId,
                'assigned_date'   => $startDate,
                'completion_date' => $endDate,
                'status'          => 'assigned',
            ]);
        }

        return response()->json([
            'message'      => 'Job schedule updated successfully',
        ]);
    }

    public function deleteFakeSchedule(Request $request, $id)
    {
        $schedule = FakeJobSchedule::find($id);

        if (!$schedule) {
            return response()->json(['error' => 'Schedule not found'], 404);
        }

        // Delete associated workers
        FakeJobScheduleWorker::where('fake_job_schedule_id', $id)->delete();

        // Handle attachments
        $attachments = FakeJobScheduleAttachment::where('fake_job_schedule_id', $id)->get();
        foreach ($attachments as $attachment) {
            $disk = $attachment->source === 's3' ? 's3' : 'public';
            $path = $attachment->attachment_path;

            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }

            $attachment->delete();
        }

        // Finally, delete the schedule
        $schedule->delete();

        return response()->json(['message' => 'Schedule deleted successfully']);
    }
}
