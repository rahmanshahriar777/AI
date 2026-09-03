<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\CourseCertificate;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = CourseCertificate::orderBy('id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a class="btn btn-sm btn-primary edit" href="' . route('certificates.edit', $row->id) . '">Edit</a>
                            <button class="btn btn-sm btn-danger delete" data-id="' . $row->id . '">Delete</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('certificates.index');
    }

    public function create()
    {
        return view('certificates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'certificatename' => 'required|string|max:255',
            'certificatedetails' => 'nullable|string',
            'certificatevalidity' => 'required|string',
        ]);
        $certificate = new CourseCertificate();
        $certificate->certificate_name = $request->input('certificatename');
        $certificate->description = $request->input('certificatedetails');
        $certificate->authorized_by = $request->input('conductedby');
        $certificate->validity_period = $request->input('certificatevalidity');
        $certificate->status = 'active';
        $certificate->save();

        return redirect()->route('certificates.index')->with('success', 'Certificate created successfully.');
    }

    public function edit($id)
    {
        $certificate = CourseCertificate::findOrFail($id);
        return view('certificates.edit', compact('certificate'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'certificatename' => 'required|string|max:255',
            'certificatedetails' => 'nullable|string',
            'certificatevalidity' => 'required|string',
        ]);
        $certificate = CourseCertificate::findOrFail($id);
        $certificate->certificate_name = $request->input('certificatename');
        $certificate->description = $request->input('certificatedetails');
        $certificate->authorized_by = $request->input('conductedby');
        $certificate->validity_period = $request->input('certificatevalidity');
        $certificate->status = $request->input('status');
        $certificate->save();

        return redirect()->route('certificates.edit', $certificate->id)->with('success', 'Certificate updated successfully.');
    }
}
