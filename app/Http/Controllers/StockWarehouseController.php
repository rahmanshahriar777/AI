<?php

namespace App\Http\Controllers;

use App\Models\StockWarehouse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StockWarehouseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = StockWarehouse::select('*');

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

        return view('stockwarehouses.index');
    }

    public function storeWarehouse(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        StockWarehouse::create([
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
            'is_active' => true, // Convert to boolean
        ]);

        return redirect()->route('stockwarehouses.index')->with('success', 'Stock warehouse created successfully.');
    }

    public function showWarehouse($id)
    {
        $warehouse = StockWarehouse::findOrFail($id);
        return response()->json($warehouse);
    }

    public function updateWarehouse(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $stockwarehouse = StockWarehouse::findOrFail($id);
        $stockwarehouse->update([
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
            'is_active' => true, // Convert to boolean
        ]);

        return redirect()->route('stockwarehouses.index')->with('success', 'Stock warehouse updated successfully.');
    }

    public function destroyWarehouse($id)
    {
        $stockwarehouse = StockWarehouse::findOrFail($id);
        $stockwarehouse->delete();

        return redirect()->route('stockwarehouses.index')->with('success', 'Stock warehouse deleted successfully.');
    }
}
