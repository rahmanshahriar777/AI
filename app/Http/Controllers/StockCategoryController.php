<?php

namespace App\Http\Controllers;

use App\Models\StockCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StockCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = StockCategory::with('parent')->select('*');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('parent_category', function ($row) {
                    return $row->parent ? $row->parent->name : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="edit btn btn-primary btn-sm" data-id="' . $row->id . '">Edit</button>
                        <button class="delete btn btn-danger btn-sm" data-id="' . $row->id . '">Delete</button>
                    ';
                })

                ->rawColumns(['action'])
                ->make(true);
        }
        // Pass only top-level categories (parent_id = null or 0)
        $categories = StockCategory::get();

        return view('stockcategories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->merge([
            'parent_id' => $request->parent_id == 0 ? null : $request->parent_id
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:stock_categories,id',
        ]);

        StockCategory::create([
            'name' => $request->name,
            'parent_id' => $request->input('parent_id') == 0 ? null : $request->input('parent_id'),
            'description' => $request->description,
            'is_active' => true, // Convert to boolean
        ]);

        return redirect()->route('stockcategories.index')->with('success', 'Stock Category created successfully.');
    }

    public function showCategory($id)
    {
        $category = StockCategory::findOrFail($id);
        return response()->json($category);
    }

    public function updateCategory(Request $request, $id)
    {
        $request->merge([
            'parent_id' => $request->parent_id == 0 ? null : $request->parent_id
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:stock_categories,id',
        ]);

        $category = StockCategory::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'parent_id' => $request->input('parent_id') == 0 ? null : $request->input('parent_id'),
            'description' => $request->description,
            'is_active' => true, // Convert to boolean
        ]);

        return redirect()->route('stockcategories.index')->with('success', 'Stock Category updated successfully.');
    }

    public function destroyCategory($id)
    {
        $category = StockCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('stockcategories.index')->with('success', 'Stock Category deleted successfully.');
    }
}
