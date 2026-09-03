<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleCheckCategory;
use App\Models\VehicleCheckChecklist;
use App\Models\VehicleCheckChecklistAttribute;
use Yajra\DataTables\Facades\DataTables;

class VehicleCheckingController extends Controller
{
    public function indexVehicleCheckingCategories(Request $request)
    {
        if ($request->ajax()) {
            $data = VehicleCheckCategory::select('*')->with(['parent']);

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('parent', function ($row) {
                    return $row->is_parent ? 'N/A' : $row->parent->name;
                })
                ->addColumn('status', function ($row) {
                    return $row->status ? 'Active' : 'Inactive';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a class="btn btn-info btn-sm" href="vehicle-checkings/checklists/' . $row->id . '" >Checklists</a>
                        <button class="edit btn btn-primary btn-sm" data-id="' . $row->id . '">Edit</button>
                        <button class="delete btn btn-danger btn-sm" data-id="' . $row->id . '">Delete</button>
                    ';
                })

                ->rawColumns(['action'])
                ->make(true);
        }
        $parentCategories = VehicleCheckCategory::where('is_parent', true)->where('status', true)->get();


        return view('vehiclecheckcategories.index', compact('parentCategories'));
    }

    public function storeVehicleCheckingCategories(Request $request)
    {
        // Normalize booleans first
        $isParent = filter_var($request->is_parent, FILTER_VALIDATE_BOOLEAN);

        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:vehicle_check_categories,id',
            'description' => 'nullable|string',
        ]);

        // Create category
        $category = VehicleCheckCategory::create([
            'name'        => $request->name,
            'is_parent'   => $isParent,
            'parent_id'   => $isParent ? null : $request->parent_id,
            'description' => $request->description,
            'status'      => true, // Default to active
        ]);

        return redirect()
            ->route('vehicle-checkings.index-categories')
            ->with('success', 'Vehicle Check Category created successfully.');
    }

    public function indexVehicleCheckingChecklists(Request $request, $vehicle_checking_category_id)
    {
        $category = VehicleCheckCategory::findOrFail($vehicle_checking_category_id);

        if ($request->ajax()) {
            $data = VehicleCheckChecklist::select('*')->with(['attributes'])->where('vehicle_check_category_id', $vehicle_checking_category_id);

            return Datatables::of($data)
                ->addIndexColumn()

                ->addColumn('action', function ($row) {
                    return '
                        <button class="attribute btn btn-info btn-sm" data-id="' . $row->id . '">Attribute</button>
                        <button class="edit btn btn-primary btn-sm" data-id="' . $row->id . '">Edit</button>
                        <button class="delete btn btn-danger btn-sm" data-id="' . $row->id . '">Delete</button>
                    ';
                })

                ->rawColumns(['action'])
                ->make(true);
        }


        // For now, just return the category details
        return view('vehiclecheckcategories.checklists', compact('category'));
    }

    public function storeVehicleCheckingChecklists(Request $request)
    {
        // Validation
        $request->validate([
            'vehicle_check_category_id' => 'required|exists:vehicle_check_categories,id',
            'title' => 'required|string|max:255',
        ]);

        // Create checklist
        $checklist = VehicleCheckChecklist::create([
            'vehicle_check_category_id' => $request->vehicle_check_category_id,
            'title'                      => $request->title,
            'status'                    => true, // Default to active
        ]);

        return redirect()
            ->route('vehicle-checkings.index-checklists', ['vehicle_checking_category_id' => $request->vehicle_check_category_id])
            ->with('success', 'Vehicle Check Checklist created successfully.');
    }

    public function showVehicleCheckingChecklists($id)
    {
        $checklist = VehicleCheckChecklist::findOrFail($id);
        return response()->json($checklist);
    }

    public function updateVehicleCheckingChecklists(Request $request, $id)
    {
        // Find the checklist
        $checklist = VehicleCheckChecklist::findOrFail($id);

        // Validation
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        // Update checklist
        $checklist->update([
            'title' => $request->title,
        ]);

        return redirect()
            ->route('vehicle-checkings.index-checklists', ['vehicle_checking_category_id' => $checklist->vehicle_check_category_id])
            ->with('success', 'Vehicle Check Checklist updated successfully.');
    }

    public function destroyVehicleCheckingChecklists($id)
    {
        // Find the checklist
        $checklist = VehicleCheckChecklist::findOrFail($id);
        $categoryId = $checklist->vehicle_check_category_id;

        // Delete checklist
        $checklist->delete();

        return redirect()
            ->route('vehicle-checkings.index-checklists', ['vehicle_checking_category_id' => $categoryId])
            ->with('success', 'Vehicle Check Checklist deleted successfully.');
    }

    public function storeVehicleCheckingChecklistAttributes(Request $request)
    {
        // Validation
        $request->validate([
            'vehicle_check_checklist_id' => 'required|exists:vehicle_check_checklists,id',
            'attribute_name' => 'required|string|max:255',
        ]);

        // Create checklist attribute
        $attribute = VehicleCheckChecklistAttribute::create([
            'vehicle_check_checklist_id' => $request->vehicle_check_checklist_id,
            'attribute_name'              => $request->attribute_name,
        ]);

        $updatechecklist = VehicleCheckChecklist::find($request->vehicle_check_checklist_id);
        $updatechecklist->has_attributes = true;
        $updatechecklist->save();

        return redirect()
            ->route('vehicle-checkings.index-checklists', ['vehicle_checking_category_id' => $updatechecklist->vehicle_check_category_id])
            ->with('success', 'Vehicle Check Checklist Attribute created successfully.');
    }

    public function showVehicleCheckingChecklistAttributes($id)
    {
        $attribute = VehicleCheckChecklistAttribute::findOrFail($id);
        return response()->json($attribute);
    }

    public function updateVehicleCheckingChecklistAttributes(Request $request, $id)
    {
        // Find the attribute
        $attribute = VehicleCheckChecklistAttribute::findOrFail($id);
        $checklist = $attribute->checklist;

        // Validation
        $request->validate([
            'attribute_name' => 'required|string|max:255',
        ]);

        // Update attribute
        $attribute->update([
            'attribute_name' => $request->attribute_name,
        ]);

        return redirect()
            ->route('vehicle-checkings.index-checklists', ['vehicle_checking_category_id' => $checklist->vehicle_check_category_id])
            ->with('success', 'Vehicle Check Checklist Attribute updated successfully.');
    }

    public function destroyVehicleCheckingChecklistAttributes($id)
    {
        // Find the attribute
        $attribute = VehicleCheckChecklistAttribute::findOrFail($id);
        $checklist = $attribute->checklist;
        $categoryId = $checklist->vehicle_check_category_id;

        // Delete attribute
        $attribute->delete();

        return redirect()
            ->route('vehicle-checkings.index-checklists', ['vehicle_checking_category_id' => $categoryId])
            ->with('success', 'Vehicle Check Checklist Attribute deleted successfully.');
    }
}
