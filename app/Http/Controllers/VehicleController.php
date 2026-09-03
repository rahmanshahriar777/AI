<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleCategory;
use App\Models\VehicleDetail;
use Yajra\DataTables\Facades\DataTables;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = VehicleDetail::select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('make_model_engine', function ($row) {
                    return $row->manufacturer . ' / ' . $row->model . ' / ' . $row->engine;
                })
                ->addColumn('tax_due', function ($row) {
                    return $row->tax_expiry_date ? \Carbon\Carbon::parse($row->tax_expiry_date)->format('d-m-Y') : '-';
                })
                ->addColumn('loler_due', function ($row) {
                    return $row->loler_expire_date ? \Carbon\Carbon::parse($row->loler_expire_date)->format('d-m-Y') : '-';
                })
                ->addColumn('mot_due', function ($row) {
                    return $row->mot_expiry_date ? \Carbon\Carbon::parse($row->mot_expiry_date)->format('d-m-Y') : '-';
                })
                ->addColumn('service_due', function ($row) {
                    return $row->next_service_date ? \Carbon\Carbon::parse($row->next_service_date)->format('d-m-Y') : '-';
                })
                ->addColumn('tyre_sizes', function ($row) {
                    return $row->tyre_size_front . ' / ' . $row->tyre_size_rear;
                })
                ->addColumn('vehicle_codes', function ($row) {
                    return implode(', ', array_filter([
                        $row->mechanical_code,
                        $row->electronic_code,
                        $row->radio_code
                    ]));
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('vehicles.show', $row->id);
                    return '
                        <a href="' . route('vehicle-overviews.index', $row->id) . '" class="btn btn-sm btn-info">Overview</a>
                        <a href="' . $showUrl . '" class="btn btn-sm btn-primary">Show</a>
                        <button class="btn btn-sm btn-danger delete" data-id="' . $row->id . '">Delete</button>
                    ';
                })

                ->make(true);
        }

        return view('vehicledetails.index');
    }

    public function create()
    {
        $vehicleCategories = VehicleCategory::where('is_active', true)->get();
        return view('vehicledetails.create')->with('vehicleCategories', $vehicleCategories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:vehicle_categories,id',
            'registration_no' => 'required|string|max:255|unique:vehicle_details,registration_no',
            'serial_no' => 'nullable|string|max:255',
            'vin_number' => 'nullable|string|max:255|unique:vehicle_details,vin_number',
            'mechanical_code' => 'nullable|string|max:255',
            'electronic_code' => 'nullable|string|max:255',
            'radio_code' => 'nullable|string|max:255',
            'deadlock_key_duplication_codes' => 'nullable|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'engine' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:50',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'fuel_type' => 'nullable|string|max:50',
            'mileage' => 'nullable|numeric|min:0',
            'tyre_size_front' => 'nullable|string|max:50',
            'tyre_size_rear' => 'nullable|string|max:50',
            'mot_expiry_date' => 'nullable|date',
            'tax_expiry_date' => 'nullable|date',
            'insurance_expiry_date' => 'nullable|date',
            'loler_expire_date' => 'nullable|date',
            'last_service_date' => 'nullable|date',
            'next_service_date' => 'nullable|date',
        ]);

        $vwchileDetail = new VehicleDetail();
        $vwchileDetail->fill($request->all());
        $vwchileDetail->status = 'Available'; // Default status
        $vwchileDetail->save();

        return redirect()->route('vehicles.index')->with('success', 'Vehicle created successfully.');
    }

    public function show($id)
    {
        $vehicle = VehicleDetail::with(['category'])->findOrFail($id);
        return view('vehicledetails.show')->with('vehicle', $vehicle);
    }

    public function edit($id)
    {
        $vehicle = VehicleDetail::with(['category'])->findOrFail($id);
        $vehicleCategories = VehicleCategory::where('is_active', true)->get();
        return view('vehicledetails.edit')->with(['vehicle' => $vehicle, 'vehicleCategories' => $vehicleCategories]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:vehicle_categories,id',
            'registration_no' => 'required|string|max:255|unique:vehicle_details,registration_no,' . $id,
            'vin_number' => 'nullable|string|max:255|unique:vehicle_details,vin_number,' . $id,
            'serial_no' => 'nullable|string|max:255',
            'mechanical_code' => 'nullable|string|max:255',
            'electronic_code' => 'nullable|string|max:255',
            'radio_code' => 'nullable|string|max:255',
            'deadlock_key_duplication_codes' => 'nullable|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'engine' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:50',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'fuel_type' => 'nullable|string|max:50',
            'mileage' => 'nullable|numeric|min:0',
            'tyre_size_front' => 'nullable|string|max:50',
            'tyre_size_rear' => 'nullable|string|max:50',
            'mot_expiry_date' => 'nullable|date',
            'tax_expiry_date' => 'nullable|date',
            'insurance_expiry_date' => 'nullable|date',
            'loler_expire_date' => 'nullable|date',
            'last_service_date' => 'nullable|date',
            'next_service_date' => 'nullable|date',
            'status' => 'string|max:50',
        ]);

        $vehicle = VehicleDetail::findOrFail($id);
        $vehicle->fill($request->all());
        $vehicle->save();

        return redirect()->route('vehicles.show', $id)->with('success', 'Vehicle updated successfully.');
    }

    public function destroy($id)
    {
        $vehicle = VehicleDetail::findOrFail($id);
        $vehicle->delete();

        return response()->json(['success' => 'Vehicle deleted successfully.']);
    }
}
