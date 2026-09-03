<?php

namespace App\Http\Controllers;

use App\Models\Fjob;
use App\Models\FjobProgress;
use App\Models\FjobProgressTask;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JobsOverviewExport;

class FjobProgressController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = FjobProgress::with(['fjob', 'customers', 'tasks'])
                ->select('fjob_progresses.*')
                ->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('customer', function ($row) {
                    return [
                        'company_name'   => $row->customers->company_name ?? null,
                        'customer_name'  => $row->customers->contact_firstname . ' ' . $row->customers->contact_lastname ?? null,
                        'customer_email' => $row->customers->contact_email ?? null,
                        'customer_phone' => $row->customers->contact_phone ?? null,
                        'customer_mobile' => $row->customers->contact_mobile ?? null,
                    ];
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('job-overviews.show', $row->id) . '" class="edit btn btn-primary btn-sm">View</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $fjob = Fjob::whereIn('job_status', ['accepted', 'inprogress'])->orderBy('created_at', 'desc')->get();

        return view('job_overviews.index', compact('fjob'));
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $validated = $request->validate([
            'fjob_id' => 'required|exists:fjobs,id',
            'startdate' => 'required|date|before_or_equal:enddate',
            'enddate' => 'required|date|after_or_equal:startdate'
        ]);

        $fjob = Fjob::findOrFail($request->fjob_id);

        $fjobprogress = new FjobProgress();
        $fjobprogress->fjob_id = $fjob->id;
        $fjobprogress->job_title = $fjob->job_title;
        $fjobprogress->customer_id = $fjob->customer_id;
        $fjobprogress->start_date = $request->startdate;
        $fjobprogress->end_date = $request->enddate;
        $fjobprogress->priority = $request->jobpriority;
        $fjobprogress->notes = $request->note;
        $fjobprogress->progress_percent = 0;
        $fjobprogress->status = 'not_started';
        $fjobprogress->save();

        if ($request->ajax()) {
            return response()->json(['message'=> 'Job progress saved successfully.', 'redirect' => route('job-overviews.show', $fjobprogress->id) ]);
        }

        return redirect()->route('job-overviews.show', $fjobprogress->id)->with('success', 'Job progress saved successfully.');
    }

    public function jobOverviewsShow($jobprogressid)
    {
        $jobprogress = FjobProgress::with(['customers' => function ($q) {
            $q->select('id', 'company_name', 'contact_firstname', 'contact_lastname');
        }, 'tasks', 'tasks.resource'])->findOrFail($jobprogressid);
        $staffs = User::role('staff')->orderBy('name', 'asc')->get();
        // return $jobprogress;

        return view('job_overviews.show')->with([
            'jobprogress' => $jobprogress,
            'staffs' => $staffs
        ]);
    }

    public function storeTask(Request $request)
    {
        // Validate basic fields
        $request->validate([
            'tasktitle'   => 'required|string|max:255',
            'startfrom'   => 'required|date|before_or_equal:endsat',
            'endsat'      => 'required|date|after_or_equal:startfrom',
            'assignto'    => 'required|exists:users,id',
            'notes'       => 'nullable|string|max:500',
            'fjobpregressid' => 'required|exists:fjob_progresses,id',
        ]);

        // Get parent job progress
        $jobprogress = FjobProgress::findOrFail($request->fjobpregressid);

        // Ensure task dates are inside parent job date range
        if ($request->startfrom < $jobprogress->start_date || $request->endsat > $jobprogress->end_date) {
            return redirect()->back()->withErrors([
                'date' => "Task dates must be between {$jobprogress->start_date} and {$jobprogress->end_date}."
            ])->withInput();
        }

        // Calculate duration (inclusive of start & end date)
        $start = Carbon::parse($request->startfrom);
        $end   = Carbon::parse($request->endsat);
        $duration = $start->diffInDays($end) + 1; // +1 to include both days

        // Create the task
        $task = $jobprogress->tasks()->create([
            'task_name'       => $request->tasktitle,
            'start_date'  => $request->startfrom,
            'end_date'     => $request->endsat,
            'assigned_to' => $request->assignto,
            'description'       => $request->notes,
            'duration'    => $duration, // store duration
            'status' => 'in_progress'
        ]);

        if ($request->ajax()) {
            return response()->json(['message'=> 'Task created successfully.', 'redirect' => route('job-overviews.show', $jobprogress->id) ]);
        }

        return redirect()->route('job-overviews.show', $jobprogress->id)
            ->with('success', 'Task created successfully!');
    }

    public function showTask($taskid)
    {
        $showtask = FjobProgressTask::findOrFail($taskid);
        return response()->json($showtask);
    }

    public function updateTask(Request $request)
    {
        $request->validate([
            'tasktitle'   => 'required|string|max:255',
            'startfrom'   => 'required|date',
            'endsat'      => 'required|date|after_or_equal:startfrom',
            'assignto'    => 'required|exists:users,id',
            'notes'       => 'nullable|string|max:500',
            'taskid'      => 'required|exists:fjob_progress_tasks,id',
            'status'      => 'required|string', // Ensure status is provided
        ]);

        $task = FjobProgressTask::findOrFail($request->taskid);

        // Calculate duration (inclusive of start & end date)
        $start = Carbon::parse($request->startfrom);
        $end   = Carbon::parse($request->endsat);
        $duration = $start->diffInDays($end) + 1; // +1 to include both days

        $task->task_name   = $request->tasktitle;
        $task->start_date  = $request->startfrom;
        $task->end_date    = $request->endsat;
        $task->assigned_to = $request->assignto;
        $task->description = $request->notes;
        $task->duration    = $duration;
        $task->status      = $request->status;
        $task->save();

        // Update progress_percent on parent FjobProgress
        $jobProgress = $task->fjobProgress;
        if ($jobProgress) {
            $totalTasks = $jobProgress->tasks()->count();
            $completedTasks = $jobProgress->tasks()->where('status', 'completed')->count();
            $progressPercent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            $jobProgress->progress_percent = $progressPercent;
            $jobProgress->save();
        }

        return redirect()->back()->with('success', 'updated successfully');
    }

    public function exportGantt($id)
    {
        $jobprogress = FjobProgress::with('tasks.resource')->findOrFail($id);

        return Excel::download(new JobsOverviewExport($jobprogress), 'gantt_chart.xlsx');
    }
}
