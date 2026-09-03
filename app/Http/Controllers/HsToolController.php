<?php

namespace App\Http\Controllers;

use App\Models\HsTool;
use App\Models\HsChecklist;
use App\Models\HsToolCheckDetails;
use App\Models\HsToolCheckup;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class HsToolController extends Controller
{
    public function indexHsTools(Request $request)
    {
        if ($request->ajax()) {

            $data = HsTool::orderBy('slug', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                    <a href="/hs-tools/' . $row->id . '" class="show btn btn-primary btn-sm">Details</a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('hs_tools.index');
    }

    public function storeHsTools(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'toolName' => 'required|string|max:255',
            'toolDetails' => 'nullable|string',
        ]);

        // Create a new HS Tool record
        $hsTool = new HsTool();
        $hsTool->tool_name = $request->input('toolName');
        $hsTool->description = $request->input('toolDetails');
        $hsTool->status = 1; // Assuming new tools are active by default
        $hsTool->save();

        // Redirect back with a success message
        return redirect()->route('hs-tools.show', $hsTool->id)->with('success', 'HS Tool created successfully.');
    }

    public function showHsTools(Request $request, $id)
    {
        $hsTool = HsTool::with('checkups')->findOrFail($id);
        if ($request->ajax()) {

            $data = HsToolCheckup::orderBy('checkup_date', 'asc')->where('hs_tool_id', $id);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                    <a href="/hs-tools/' . $row->hs_tool_id . '/checkup/' . $row->id . '" class="show btn btn-primary btn-sm">Details</a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('hs_tools.show', compact('hsTool'));
    }

    public function createHsToolsCheck(Request $request, $id)
    {
        $hsTool = HsTool::findOrFail($id);
        $checklists = HsChecklist::where('status', true)->where('type', 'equipments')->get();
        return view('hs_tools.checkup_create', compact('hsTool', 'checklists'));
    }

    public function storeHsToolsCheck(Request $request, $id)
    {
        $request->validate([
            'hs_check_title' => 'required|string|max:255',
            'checklist' => 'required|array',
            'checklist.*.status' => 'required|in:0,1,2',
            'checklist.*.note' => 'nullable|string|max:255',
        ]);
        $hsTool = HsTool::findOrFail($id);

        $hstoolcheck = new HsToolCheckup();
        $hstoolcheck->hs_tool_id = $hsTool->id;
        $hstoolcheck->tool_name = $hsTool->tool_name;
        $hstoolcheck->checkup_name = $request->input('hs_check_title');
        $hstoolcheck->checkup_date = $request->input('hs_check_date') ?? now();
        $hstoolcheck->performed_by = auth()->user()->id;
        $hstoolcheck->remarks = $request->input('notes') ?? null;
        $hstoolcheck->status = 'completed';
        $hstoolcheck->next_checkup_date = $request->input('hs_check_date') ?? now()->addMonth();
        $hstoolcheck->save();

        foreach ($request->checklist as $checklistId => $data) {
            $hschecklist = HsChecklist::findOrFail($checklistId);
            HsToolCheckDetails::create([
                'hs_tool_checkup_id' => $hstoolcheck->id,
                'hs_checklist_id' => $checklistId,
                'hs_checklist_title' => $hschecklist->title,
                'value' => $data['status'],
                'remarks' => $data['note'] ?? null,
            ]);
        }

        return redirect()->route('hs-tools.showcheck', ['id' => $hsTool->id, 'checkup_id' => $hstoolcheck->id])->with('success', 'HS Tool checkup created successfully.');
    }

    public function showHsToolsCheck(Request $request, $id, $checkup_id)
    {
        $hsTool = HsTool::findOrFail($id);
        $hstoolcheck = HsToolCheckup::with('checklist')->findOrFail($checkup_id);
        $checklists = HsChecklist::where('status', true)->where('type', 'equipments')->get();
        return view('hs_tools.checkup_show', compact('hsTool', 'hstoolcheck', 'checklists'));
    }

    public function updateHsToolsCheck(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'hs_check_title' => 'required|string|max:255',
            'checklist' => 'required|array',
            'checklist.*.status' => 'required|in:0,1,2',
            'checklist.*.note' => 'nullable|string|max:255',
        ]);


        $hstoolcheck = HsToolCheckup::find($id);

        $hstoolcheck->checkup_name = $request->input('hs_check_title');
        $hstoolcheck->checkup_date = $request->input('hs_check_date') ?? now();
        $hstoolcheck->performed_by = auth()->user()->id;
        $hstoolcheck->remarks = $request->input('notes') ?? null;
        $hstoolcheck->next_checkup_date = $request->input('hs_check_date') ?? now()->addMonth();
        $hstoolcheck->save();

        // Update or create checklist details
        foreach ($request->checklist as $checklistId => $data) {
            $hschecklist = HsChecklist::findOrFail($checklistId);
            HsToolCheckDetails::updateOrCreate(
                [
                    'hs_tool_checkup_id' => $hstoolcheck->id,
                    'hs_checklist_id' => $checklistId,
                ],
                [
                    'hs_checklist_title' => $hschecklist->title,
                    'value' => $data['status'],
                    'remarks' => $data['note'] ?? null,
                ]
            );
        }


        // Redirect back with a success message
        return redirect()->route('hs-tools.showcheck', $hstoolcheck->hs_tool_id, $hstoolcheck->id)->with('success', 'HS Tool checkup created successfully.');
    }
}
