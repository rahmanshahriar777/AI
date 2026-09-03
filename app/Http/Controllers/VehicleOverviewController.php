<?php

namespace App\Http\Controllers;

use App\Models\VehicleCheckCategory;
use App\Models\VehicleDetail;
use App\Models\VehicleOverview;
use App\Models\VehicleOverviewDetail;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VehicleOverviewExport;

class VehicleOverviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($vehicleId)
    {
        $vehicle = VehicleDetail::findOrFail($vehicleId);
        $vehiclechecklists = VehicleCheckCategory::with([
            'child',
            'child.checklists',
            'child.checklists.attributes'
        ])->where('is_parent', true)->get();

        $vehicleoverviews = VehicleOverview::where('vehicle_id', $vehicleId)
            ->with(['details'])
            ->orderBy('overview_date', 'asc')
            ->get();


        // return $vehiclechecklists;

        return view('vehicle_overviews.index', [
            'vehiclechecklists' => $vehiclechecklists,
            'vehicleoverviews' => $vehicleoverviews,
            'vehicle' => $vehicle
        ]);
    }

    public function vehicleOverviewShow($vehicleoverviewId)
    {
        $vehicleoverviews = VehicleOverview::with(['details'])->findOrFail($vehicleoverviewId);

        if (!$vehicleoverviews) {
            return redirect()->back()->with('error', 'Vehicle overview not found.');
        }

        $detailsMap = [];
        foreach ($vehicleoverviews->details as $detail) {
            $key = $detail->vehicle_check_checklists_id;
            $attr = $detail->vehicle_check_checklist_attributes_id ?? 'no_attr';
            $detailsMap[$key][$attr] = $detail;
        }


        $vehicle = VehicleDetail::findOrFail($vehicleoverviews->vehicle_id);
        $categories = VehicleCheckCategory::with([
            'child',
            'child.checklists',
            'child.checklists.attributes'
        ])->where('is_parent', true)->get();

        return view('vehicle_overviews.show', [
            'vehicleoverviews' => $vehicleoverviews,
            'detailsMap' => $detailsMap,
            'vehicle' => $vehicle,
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($vehicleId)
    {
        $vehicle = VehicleDetail::findOrFail($vehicleId);
        if (!$vehicle) {
            return redirect()->back()->with('error', 'Vehicle not found.');
        }

        $categories = VehicleCheckCategory::with([
            'child',
            'child.checklists',
            'child.checklists.attributes'
        ])->where('is_parent', true)->get();

        // return $categories;
        return view('vehicle_overviews.create', ['vehicle' => $vehicle, 'categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return $request->all();
        $request->validate([
            'overview_date' => 'required|date',
            'mileage' => 'required',
        ]);

        $overview = new VehicleOverview();
        $overview->vehicle_id = $request->vehicle_id;
        $overview->overview_date = $request->overview_date;
        $overview->mileage = $request->mileage;
        $overview->notes = $request->notes ?? null; // fixed: request key was "notes"
        $overview->save();

        foreach ($request->checklist as $checklistId => $checklistData) {
            // Case 1: checklist has attributes
            if (isset($checklistData['attributes'])) {
                foreach ($checklistData['attributes'] as $attributeId => $attributeData) {
                    $detail = new VehicleOverviewDetail();
                    $detail->vehicle_overview_id = $overview->id;
                    $detail->vehicle_check_categories_id = $checklistData['parent_category_id'] ?? null;
                    $detail->vehicle_check_categories_child_id = $checklistData['category_id'] ?? null;
                    $detail->vehicle_check_checklists_id = $checklistId;
                    $detail->has_attribute = true;
                    $detail->vehicle_check_checklist_attributes_id = $attributeId;
                    $detail->overview_value = $attributeData['status'] ?? 0; // ✅ default N/A (0)
                    $detail->notes = $attributeData['note'] ?? null;
                    $detail->save();
                }
            }
            // Case 2: checklist without attributes
            else {
                $detail = new VehicleOverviewDetail();
                $detail->vehicle_overview_id = $overview->id;
                $detail->vehicle_check_categories_id = $checklistData['parent_category_id'] ?? null;
                $detail->vehicle_check_categories_child_id = $checklistData['category_id'] ?? null;
                $detail->vehicle_check_checklists_id = $checklistId;
                $detail->has_attribute = false;
                $detail->vehicle_check_checklist_attributes_id = null;
                $detail->overview_value = $checklistData['status'] ?? 0; // ✅ default N/A (0)
                $detail->notes = $checklistData['note'] ?? null;
                $detail->save();
            }
        }


        return redirect()->route('vehicle-overviews.index', $request->vehicle_id)
            ->with('success', 'Vehicle overview created successfully.');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $updateoverview = VehicleOverview::findOrFail($id);
        if (!$updateoverview) {
            return redirect()->back()->with('error', 'Vehicle overview not found.');
        }
        $updateoverview->overview_date = $request->overview_date;
        $updateoverview->mileage = $request->mileage;
        $updateoverview->notes = $request->notes ?? null; // fixed: request key was "notes"
        $updateoverview->save();

        // Delete existing details
        VehicleOverviewDetail::where('vehicle_overview_id', $id)->delete();
        // Re-insert details
        foreach ($request->checklist as $checklistId => $checklistData) {
            // Case 1: checklist has attributes
            if (isset($checklistData['attributes'])) {
                foreach ($checklistData['attributes'] as $attributeId => $attributeData) {
                    $detail = new VehicleOverviewDetail();
                    $detail->vehicle_overview_id = $updateoverview->id;
                    $detail->vehicle_check_categories_id = $checklistData['parent_category_id'] ?? null;
                    $detail->vehicle_check_categories_child_id = $checklistData['category_id'] ?? null;
                    $detail->vehicle_check_checklists_id = $checklistId;
                    $detail->has_attribute = true;
                    $detail->vehicle_check_checklist_attributes_id = $attributeId;
                    $detail->overview_value = $attributeData['status'] ?? 0; // ✅ default N/A (0)
                    $detail->notes = $attributeData['note'] ?? null;
                    $detail->save();
                }
            }
            // Case 2: checklist without attributes
            else {
                $detail = new VehicleOverviewDetail();
                $detail->vehicle_overview_id = $updateoverview->id;
                $detail->vehicle_check_categories_id = $checklistData['parent_category_id'] ?? null;
                $detail->vehicle_check_categories_child_id = $checklistData['category_id'] ?? null;
                $detail->vehicle_check_checklists_id = $checklistId;
                $detail->has_attribute = false;
                $detail->vehicle_check_checklist_attributes_id = null;
                $detail->overview_value = $checklistData['status'] ?? 0; // ✅ default N/A (0)
                $detail->notes = $checklistData['note'] ?? null;
                $detail->save();
            }
        }

        return redirect()->route('vehicle-overviews.show', $updateoverview->id)
            ->with('success', 'Vehicle overview updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function export($vehicleId)
    {
        $vehicle = VehicleDetail::findOrFail($vehicleId);
        if (!$vehicle) {
            return redirect()->back()->with('error', 'Vehicle not found.');
        }
        $vehiclechecklists = VehicleCheckCategory::with([
            'child',
            'child.checklists',
            'child.checklists.attributes'
        ])->where('is_parent', true)->get();

        $vehicleoverviews = VehicleOverview::where('vehicle_id', $vehicleId)->with(['details'])->get();

        $overviewDates = $vehicleoverviews->pluck('overview_date')->unique();

        $overviewMap = [];

        foreach ($vehicleoverviews as $overview) {
            foreach ($overview->details as $detail) {
                $key =
                    $detail->vehicle_check_categories_id .
                    '-' .
                    $detail->vehicle_check_categories_child_id .
                    '-' .
                    $detail->vehicle_check_checklists_id .
                    '-' .
                    ($detail->vehicle_check_checklist_attributes_id ?? 0);

                $overviewMap[$key][$overview->overview_date] = $detail;
            }
            // dd($overviewMap);
        }

        return Excel::download(
            new VehicleOverviewExport($vehicle, $vehiclechecklists, $overviewDates, $vehicleoverviews, $overviewMap),
            'vehicle_overview.xlsx'
        );
    }
}
