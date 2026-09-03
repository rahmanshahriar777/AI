<?php

namespace App\Http\Controllers;

use App\Models\JobType;
use App\Models\Quotation;
use App\Models\QuotationTemplate;
use App\Models\QuotationTemplateSection;
use App\Models\QuotationTemplateSectionSample;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class QuotationTemplateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = QuotationTemplate::select('*');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $showUrl = route('quotationtemplates.show', $row->id); // Edit Route
                    
                    return '
                    <a href="' . $showUrl . '" class="shoq btn btn-primary btn-sm">Show</a>
                    <button class="delete btn btn-danger btn-sm" data-id="' . $row->id . '">Delete</button>
                ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('quotationtemplates.index');
    }

    public function create()
    {
        $jobtype = JobType::orderBy('name', 'asc')->get();
        return view('quotationtemplates.create')->with(['jobtype' => $jobtype]);
    }

    public function store(Request $request)
    {
        // return $request->all();
        $validation = $request->validate([
            'jobtype' => 'required|exists:job_types,slug',
            'template_name' => 'required|string',
        ]);

        $quotationTemplate = new QuotationTemplate();
        $quotationTemplate->job_type_id = JobType::where('slug', $validation['jobtype'])->first()->id;
        $quotationTemplate->job_type_name = $validation['jobtype'];
        $quotationTemplate->template_name = $validation['template_name'];
        $quotationTemplate->status = 1; // Assuming status is 1 for active
        $quotationTemplate->save();

        $quotationTemplateid = $quotationTemplate->id;

        // Save each section from the repeater
        foreach ($request->input('group-a', []) as $index => $section) {
            QuotationTemplateSection::create([
                'quotation_template_id' => $quotationTemplateid,
                'section_name'         => $section['section_name'],
                'section_type'         => $section['section_type'],
                'is_required'          => $section['is_required'],
                'has_attachments'      => $section['has_attachment'], // match key name
                'order'                => $index + 1,
            ]);
        }

        return redirect()->route('quotationtemplates.show', $quotationTemplateid)->with('success', 'Quotation template created successfully.');
    }

    public function addSection(Request $request)
    {
        $validation = $request->validate([
            'section_name' => 'required|string|max:255',
            'section_type' => 'required|in:textbox,image_gallery',
            'is_required' => 'required|boolean',
            'has_attachment' => 'required|boolean',
            'quotation_template_id' => 'required|exists:quotation_templates,id',
        ]);

        // Create a new section
        $section = new QuotationTemplateSection();
        $section->quotation_template_id = $validation['quotation_template_id'];
        $section->section_name = $validation['section_name'];
        $section->section_type = $validation['section_type'];
        $section->is_required = $validation['is_required'];
        $section->has_attachments = $validation['has_attachment'];
        $section->save();

        return response()->json(['status' => 'success', 'message' => 'Section added successfully.']);
    }
    public function deleteSection($id)
    {
        DB::beginTransaction();

        try {
            $quotationtemplatesection = QuotationTemplateSection::findOrFail($id);

            // Delete content
            QuotationTemplateSectionSample::where('quotation_template_section_id', $id)->delete();

            // Delete the quotation
            $quotationtemplatesection->delete();
            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Quotation deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['status' => 'error', 'message' => 'Failed to delete quotation. ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $quotationTemplate = QuotationTemplate::with(['sections', 'sections.samples'])->findOrFail($id);
        // return $quotationTemplate;
        return view('quotationtemplates.show', compact('quotationTemplate'));
    }

    public function edit($id)
    {
        return view('quotationtemplate.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Update the quotation template
        return redirect()->route('quotationtemplate.index')->with('success', 'Quotation template updated successfully.');
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $quotation = QuotationTemplate::with('sections.samples')->findOrFail($id);

            foreach ($quotation->sections as $section) {
                // Delete attachments
                // $section->attachments()->delete();
                // Delete content
                $section->samples()->delete();
            }

            // Delete sections
            $quotation->sections()->delete();
            // Delete the quotation
            $quotation->delete();
            DB::commit();

            return response()->json(['success' => 'Quotation deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Failed to delete quotation. ' . $e->getMessage()], 500);
        }
    }

    public function populateSections(Request $request)
    {
        // return $request->all();
        $validation = $request->validate([
            'quotation_template_id' => 'required|exists:quotation_templates,id',
        ]);
        $template = QuotationTemplate::with('sections')->findOrFail($validation['quotation_template_id']);

        foreach ($template->sections as $section) {
            $slug = $section->section_slug;

            if ($request->has($slug)) {
                $content = $request->input($slug);

                QuotationTemplateSectionSample::updateOrCreate(
                    ['quotation_template_section_id' => $section->id],
                    [
                        'sample_type' => $section->section_type, // or dynamic if needed
                        'sample_source' => $content,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Samples saved successfully.');
    }
}
