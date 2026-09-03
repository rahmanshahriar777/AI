<?php

namespace App\Http\Controllers;

use App\Models\StockAttribute;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StockAttributeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = StockAttribute::select('*');

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


        return view('stockattributes.index');
    }
    public function storeAttribute(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:stock_attributes,name',
        ]);

        StockAttribute::create([
            'name' => $request->name,
            'unit' => $request->unit,
        ]);

        return redirect()->route('stockattributes.index')->with('success', 'Stock Attribute created successfully.');
    }

    public function showAttribute($id)
    {
        $attribute = StockAttribute::findOrFail($id);
        return response()->json($attribute);
    }

    public function updateAttribute(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:stock_attributes,name,' . $id,
        ]);

        $attribute = StockAttribute::findOrFail($id);
        $attribute->update([
            'name' => $request->name,
            'unit' => $request->unit,
        ]);

        return redirect()->route('stockattributes.index')->with('success', 'Stock attribute updated successfully.');
    }

    public function destroyAttribute($id)
    {
        $attribute = StockAttribute::findOrFail($id);
        $attribute->delete();

        return redirect()->route('stockattributes.index')->with('success', 'Stock attribute deleted successfully.');
    }
}
