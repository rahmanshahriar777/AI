<?php

namespace App\Http\Controllers;

use App\Models\VehicleCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VehicleCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = VehicleCategory::select('*');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <button class="edit btn btn-primary btn-sm" data-id="' . $row->id . '">Edit</button>
                        <button class="delete btn btn-danger btn-sm" data-id="' . $row->id . '">Delete</button>
                    ';
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        return view('vehiclecategory.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        VehicleCategory::create($request->all());

        return redirect()->route('vehiclecategories.index')
            ->with('success', 'Vehicle Category created successfully.');
    }

    public function show($id){
        $category = VehicleCategory::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $category = VehicleCategory::findOrFail($id);
        $category->update($request->all());

        return redirect()->route('vehiclecategories.index')
            ->with('success', 'Vehicle Category updated successfully.');
    }

    public function destroy($id)
    {
        $category = VehicleCategory::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Vehicle Category deleted successfully.']);
    }

}
