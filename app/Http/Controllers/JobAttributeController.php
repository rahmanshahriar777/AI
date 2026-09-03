<?php

namespace App\Http\Controllers;

use App\Models\JobAttribute;
use App\Models\JobAttributeDetail;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class JobAttributeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = JobAttribute::with(['attributedetails' => function ($query) {
                $query->orderBy('value', 'asc');
            }])
                ->select('*')
                ->orderBy('name', 'asc');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-sm btn-warning edit-attribute-btn" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#updateJobAttributesModal">Edit</button>';
                    $btn .= ' <button class="btn btn-sm btn-success add-detail-btn" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#addDetailModal">Add Detail</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('job_attributes.index');
    }

    public function storeJobAttribute(Request $request)
    {
        $request->validate([
            'attributename' => 'required|string|max:255',
            'attributedescription' => 'nullable|string',
        ]);

        $jobAttribute = new JobAttribute();
        $jobAttribute->name = $request->attributename;
        $jobAttribute->description = $request->attributedescription;
        $jobAttribute->is_active = true;
        $jobAttribute->save();

        return response()->json([
            'success' => true,
            'message' => 'Job Attribute created successfully.',
        ]);
    }

    public function editJobAttribute($id)
    {
        $attribute = JobAttribute::findOrFail($id);
        return response()->json($attribute);
    }


    public function updateJobAttribute(Request $request, $id)
    {
        $request->validate([
            'attributename' => 'required|string|max:255',
            'attributedescription' => 'nullable|string',
        ]);

        $jobAttribute = JobAttribute::findOrFail($id);
        $jobAttribute->name = $request->attributename;
        $jobAttribute->description = $request->attributedescription;
        $jobAttribute->save();

        return response()->json([
            'success' => true,
            'message' => 'Job Attribute updated successfully.',
        ]);
    }

    public function storeAttributeDetails(Request $request, $id)
    {
        $request->validate([
            'detailValue' => 'required|string|max:255',
            'detailDescription' => 'nullable|string|max:255',
        ]);

        $jobAttribute = JobAttribute::findOrFail($id);
        $jobAttributeDetails = new JobAttributeDetail();
        $jobAttributeDetails->job_attribute_id = $jobAttribute->id;
        $jobAttributeDetails->value = $request->detailValue;
        $jobAttributeDetails->description = $request->detailDescription;
        $jobAttributeDetails->is_active = true;
        $jobAttributeDetails->save();

        return response()->json([
            'success' => true,
            'message' => 'Job Attribute created successfully.',
        ]);
    }

    public function updateAttributeDetails(Request $request, $id)
    {
        $request->validate([
            'value' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $jobAttributeDetails = JobAttributeDetail::findOrFail($id);
        $jobAttributeDetails->value = $request->value;
        $jobAttributeDetails->description = $request->description;
        $jobAttributeDetails->save();

        return response()->json([
            'success' => true,
            'message' => 'Job Attribute updated successfully.',
        ]);
    }
}
