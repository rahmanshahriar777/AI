<?php

namespace App\Http\Controllers;

use App\Models\Fjob;
use App\Models\FjobHsCheck;
use App\Models\FjobHsCheckDetails;
use App\Models\HsChecklist;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class HSController extends Controller
{
    public function indexHsChecklists(Request $request)
    {
        if ($request->ajax()) {

            $data = HsChecklist::orderBy('slug', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                    <a href="/job/' . $row->id . '" class="edit btn btn-primary btn-sm">Details</a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('health_safety.checklist_index');
    }

    public function storeHsChecklists(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'short_details' => 'nullable|string',
            'type' => 'required|string|max:255',
        ]);

        HsChecklist::create([
            'title' => $request->title,
            'short_details' => $request->short_details,
            'type' => $request->type,
            'status' => true
        ]);

        return redirect()->route('hs-checklists.index')->with('success', 'Health & Safety Checklist created successfully.');
    }

    public function showHsChecklists($id)
    {
        $checklist = HsChecklist::findOrFail($id);
        return view('health_safety.checklist_show', compact('checklist'));
    }

    public function updateHsChecklists(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'short_details' => 'nullable|string',
            'type' => 'required|string|max:255',
            'status' => 'sometimes|boolean',
        ]);

        $checklist = HsChecklist::findOrFail($id);
        $checklist->update([
            'title' => $request->title,
            'short_details' => $request->short_details,
            'type' => $request->type,
            'status' => $request->has('status')
        ]);

        return redirect()->route('hs-checklists.index')->with('success', 'Health & Safety Checklist updated successfully.');
    }

    public function destroyHsChecklist($id)
    {
        $checklist = HsChecklist::findOrFail($id);
        $checklist->delete();

        return redirect()->route('hs-checklists.index')->with('success', 'Health & Safety Checklist deleted successfully.');
    }

    public function indexFjobHsCheck(Request $request)
    {

        if ($request->ajax()) {
            $data = FjobHsCheck::with(['job', 'job.jobtype'])
                ->orderBy('created_at', 'desc');

            if ($request->filled('jobtype')) {
                $data->whereHas('job.jobtype', function ($q) use ($request) {
                    $q->where('slug', $request->jobtype); // ✅ filtering by slug
                });
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('jobtype', function ($row) {
                    return $row->job->jobtype->name ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    return '
                <a href="/fjob-hs-check/' . $row->id . '" class="edit btn btn-primary btn-sm">Details</a>
            ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('health_safety.jobchecklist_index');
    }

    public function createFjobCheck(Request $request)
    {
        if ($request->filled('jobtype')) {
            $jobtype = $request->jobtype;
        } else {
            $jobtype = null;
        }
        $jobs = Fjob::where('job_type', $jobtype)->get();
        $checklists = HsChecklist::where('status', true)->get();
        return view('health_safety.jobchecklist_create', compact('jobs', 'checklists'));
    }

    public function storeFjobHsCheck(Request $request)
    {
        $request->validate([
            'job_id' => 'required|exists:fjobs,id',
            'hs_check_title' => 'required|string|max:255',
            'checklist' => 'required|array',
            'checklist.*.status' => 'required|in:0,1,2',
            'checklist.*.note' => 'nullable|string|max:255',
        ]);

        $fjob = Fjob::findOrFail($request->job_id);

        $fjobcheck = FjobHsCheck::create([
            'fjob_id' => $fjob->id,
            'job_title' => $fjob->job_title,
            'hs_check_title' => $request->hs_check_title ?? null,
            'checked_by_user' => auth()->user()->id,
            'status' => 'completed'
        ]);

        foreach ($request->checklist as $checklistId => $data) {
            $hschecklist = HsChecklist::findOrFail($checklistId);
            FjobHsCheckDetails::create([
                'fjob_hs_check_id' => $fjobcheck->id,
                'hs_checklist_id' => $checklistId,
                'hs_checklist_title' => $hschecklist->title,
                'value' => $data['status'],
                'remarks' => $data['note'] ?? null,
            ]);
        }

        return redirect()->route('fjob-hs-check.index')->with('success', 'Field Job Health & Safety Checklist created successfully.');
    }

    public function showFjobHsCheck($id)
    {
        $jobs = Fjob::all();
        $checklists = HsChecklist::where('status', true)->get();
        $fjobcheck = FjobHsCheck::with('checklist')->findOrFail($id);

        return view('health_safety.jobchecklist_show', compact('fjobcheck', 'jobs', 'checklists'));
    }

    public function updateFjobHsCheck(Request $request, $id)
    {
        $request->validate([
            'hs_check_title' => 'required|string|max:255',
            'checklist' => 'required|array',
            'checklist.*.status' => 'required|in:0,1,2',
            'checklist.*.note' => 'nullable|string|max:255',
        ]);

        $fjobcheck = FjobHsCheck::findOrFail($id);
        $fjobcheck->update([
            'hs_check_title' => $request->hs_check_title ?? null,
        ]);

        // Update or create checklist details
        foreach ($request->checklist as $checklistId => $data) {
            $hschecklist = HsChecklist::findOrFail($checklistId);
            FjobHsCheckDetails::updateOrCreate(
                [
                    'fjob_hs_check_id' => $fjobcheck->id,
                    'hs_checklist_id' => $checklistId,
                ],
                [
                    'hs_checklist_title' => $hschecklist->title,
                    'value' => $data['status'],
                    'remarks' => $data['note'] ?? null,
                ]
            );
        }

        return redirect()->route('fjob-hs-check.show', $fjobcheck->id)->with('success', 'Field Job Health & Safety Checklist updated successfully.');
    }
}
