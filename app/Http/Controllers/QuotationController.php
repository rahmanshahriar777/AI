<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Quotation;
use App\Models\QuotationSection;
use App\Models\QuotationSectionAttachment;
use App\Models\QuotationSectionContent;
use App\Models\QuotationTemplate;
use App\Models\QuotationTemplateSection;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class QuotationController extends Controller
{
    public function index(Request $request, $leadid)
    {
        $leaddata = Lead::where('id', $leadid)->first();
        if (!$leaddata) {
            return redirect()->route('leads.index')->with('error', 'Lead not found.');
        }

        $customerdata = Customer::with(['billingaddress'])
            ->where('id', $leaddata->customer_id)->first();

        $quotationTemplates = QuotationTemplate::where('status', 1)
            ->orderBy('template_name', 'asc')
            ->get();

        if ($request->ajax()) {
            $data = Quotation::select('*')->with(['jobType'])->where('lead_id', $leadid)->orderByDesc('quotation_date');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('quotation_date', function ($row) {
                    return $row->quotation_date
                        ? \Carbon\Carbon::parse($row->quotation_date)->format('Y-m-d')
                        : '';
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('quotations.show', $row->id);
                    $actions = '<a href="' . $showUrl . '" class="shoq"><i style="padding: 10px;" class="icon-base ti tabler-eye btn btn-success"></i></a>';

                    if ($row->status !== 'accepted') {
                        $actions .= ' <a href="' . route('quotations.edit', $row->id) . '" class="edit"><i style="padding: 10px;" class="icon-base ti tabler-edit btn btn-primary"></i></a>';
                        $actions .= ' <a class="delete" data-id="' . $row->id . '"><i style="padding: 10px;" class="icon-base ti tabler-trash btn btn-danger"></i></a>';
                    }

                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('quotations.index')->with([
            'leaddata' => $leaddata,
            'customerdata' => $customerdata,
            'leadid' => $leadid,
            'quotationTemplates' => $quotationTemplates,
        ]);
    }

    public function create($lead_id, $template_id)
    {
        // return $request->all();
        $leaddata = Lead::with(['leadaddress'])->where('id', $lead_id)->first();
        // return $leaddata;
        if (!$leaddata) {
            return redirect()->route('leads.index')->with('error', 'Lead not found.');
        }

        $customerdata = Customer::where('id', $leaddata->customer_id)->first();

        $quotationTemplate = QuotationTemplate::with(['sections', 'sections.samples'])
            ->where('id', $template_id)->first();

        return view('quotations.create')->with([
            'leaddata' => $leaddata,
            'customerdata' => $customerdata,
            'leadid' => $lead_id,
            'quotationTemplate' => $quotationTemplate,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'customer_id' => 'required|exists:customers,id',
            'job_type_id' => 'required|exists:job_types,id',
            'quotation_template_id' => 'required|exists:quotation_templates,id',
            'quotation_version' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Create the quotation
            $quotation = Quotation::create([
                'lead_id' => $request->lead_id,
                'customer_id' => $request->customer_id,
                'job_type_id' => $request->job_type_id,
                'quotation_template_id' => $request->quotation_template_id,
                'valid_until' => $request->quotation_date,
                'quotation_version' => $request->quotation_version,
                'cover_letter' => $request->cover_letter,
                'quotation_date' => now(),
                'total_amount' => $request->quotation_amount,
                'remarks' => $request->remarks,
                'status' => 'draft',
            ]);

            // Copy template sections to quotation sections
            $templateSections = QuotationTemplateSection::where('quotation_template_id', $request->quotation_template_id)
                ->orderBy('order')
                ->get();

            foreach ($templateSections as $templateSection) {
                // Generate slug or use from template
                $slug = $templateSection->section_slug ?? Str::slug($templateSection->section_name);

                // Create quotation section
                $quotationSection = $quotation->sections()->create([
                    'section_name' => $templateSection->section_name,
                    'section_type' => $templateSection->section_type,
                    'has_attachments' => $templateSection->has_attachments,
                    'order' => $templateSection->order,
                ]);

                // Insert section content if available
                if ($request->filled($slug)) {
                    QuotationSectionContent::updateOrCreate(
                        ['quotation_section_id' => $quotationSection->id],
                        [
                            'sample_type' => $templateSection->section_type,
                            'sample_source' => $request->input($slug),
                        ]
                    );
                }

                // Handle file attachments if applicable
                if ($templateSection->has_attachments && $request->hasFile("attachments.$slug")) {

                    foreach ($request->file("attachments.$slug") as $file) {

                        $disk = env('FILESYSTEM_DISK', 'local');
                        $path = $file->store('uploads/quotations', $disk);

                        if (!$path) {
                            return response()->json(['status' => 'error', 'message' => 'Failed to store image.']);
                        }

                        if ($disk === 's3') {
                            Storage::disk('s3')->setVisibility($path, 'public');
                        }

                        QuotationSectionAttachment::create([
                            'quotation_section_id'  => $quotationSection->id,
                            'attachment_path'  => $path,
                            'attachment_name'  => $file->getClientOriginalName(),
                            'attachment_url'   => Storage::disk($disk)->url($path),
                            'source'      => $disk
                        ]);
                    }
                }
            }

            DB::commit();
            
            if ($request->ajax()) {
                return response()->json(['success'=>true, 'message' => 'Quotation created successfully.']);
            }

            return redirect()->route('quotations.show', $quotation->id)
                ->with('success', 'Quotation created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json(['error'=>true, 'message' => 'Failed to create quotation: ' . $e->getMessage()]);
            }

            return back()->with('error', 'Failed to create quotation: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $quotation = Quotation::with(['lead', 'customer', 'customer.billingaddress', 'sections' => function ($q) {
            $q->orderBy('order', 'asc');
        }, 'sections.content', 'sections.attachments'])
            ->where('id', $id)->first();

        if (!$quotation) {
            return redirect()->route('quotations.index')->with('error', 'Quotation not found.');
        }
        // return $quotation;

        return view('quotations.show')->with([
            'quotation' => $quotation,
        ]);
    }

    public function edit($id)
    {
        $quotation = Quotation::with(['lead', 'quotationTemplate', 'customer', 'customer.billingaddress', 'sections' => function ($q) {
            $q->orderBy('order', 'asc');
        }, 'sections.content', 'sections.attachments'])
            ->where('id', $id)->first();

        if (!$quotation) {
            return redirect()->route('quotations.index')->with('error', 'Quotation not found.');
        }

        // return $quotation;

        return view('quotations.edit')->with([
            'quotation' => $quotation,
        ]);
    }

    public function downloadQuotationPdf($quotationId)
    {
        $quotation = Quotation::with(['lead', 'customer', 'customer.billingaddress', 'sections' => function ($q) {
            $q->orderBy('order', 'asc');
        }, 'sections.content', 'sections.attachments'])
            ->findOrFail($quotationId);
        $pdf = Pdf::loadView('pdf.quotaions', compact('quotation'));

        return $pdf->download('quotation_' . $quotation->id . '.pdf');
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $quotation = Quotation::with('sections.attachments', 'sections.content')->findOrFail($id);
            foreach ($quotation->sections as $section) {
                // Delete attachments
                $section->attachments()->delete();
                // Delete content
                $section->content()->delete();
            }

            // Delete sections
            $quotation->sections()->delete();
            // Delete the quotation
            $quotation->delete();
            DB::commit();

            return response()->json(['message' => 'Quotation deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Failed to delete quotation. ' . $e->getMessage()], 500);
        }
    }

    public function updateQuotationStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:accepted,rejected,pending',
            'remarks' => 'required_if:status,accepted|nullable|string|max:255',
        ]);

        $quotation = Quotation::find($id);

        if (!$quotation) {
            return response()->json(['error' => 'Quotation not found.'], 404);
        }

        $lead_id = $quotation->lead_id;

        if ($request->status === 'accepted') {
            // Cancel all other quotations under the same lead
            Quotation::where('lead_id', $lead_id)
                ->where('id', '!=', $quotation->id)
                ->update(['status' => 'cancelled']);

            // Accept current quotation
            $quotation->update([
                'status' => 'accepted',
                'remarks' => $request->remarks,
            ]);
        } else {
            // Just update the current quotation
            $quotation->update([
                'status' => $request->status,
                'remarks' => $request->remarks,
            ]);
        }

        return response()->json(['success' => 'Quotation status updated successfully.']);
    }


    public function reorderQuotationSections(Request $request)
    {
        $order = $request->input('order');
        foreach ($order as $item) {
            QuotationSection::where('id', $item['id'])->update(['order' => $item['position']]);
        }

        return response()->json(['status' => 'success', 'message' => 'Section order updated successfully.']);
    }

    public function updateQuotationSectionsContent(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
            'has_attachments' => 'nullable|boolean',
        ]);

        $section = QuotationSection::findOrFail($id);

        // Update section content
        $section->content()->updateOrCreate(
            ['quotation_section_id' => $section->id],
            [
                'sample_type' => 'textbox',
                'sample_source' => $request->content
            ]
        );

        // Update has_attachments flag
        $section->has_attachments = $request->has_attachments ?? 0;
        $section->save();

        return response()->json(['status' => 'success', 'message' => 'Section updated successfully.']);
    }


    public function uploadQuotationSectionsAttachments(Request $request, $id)
    {
        $request->validate([
            'attachments.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10248',
        ]);

        $section = QuotationSection::findOrFail($id);

        if (!$section->has_attachments) {
            return response()->json([
                'status' => 'error',
                'message' => 'This section does not support attachments.'
            ], 400);
        }

        $disk = env('FILESYSTEM_DISK', 'local'); // 'local' or 's3'

        foreach ($request->file('attachments', []) as $file) {
            $path = $file->store('uploads/quotations', $disk);

            if (!$path) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to store image.'
                ]);
            }

            // Make public if stored in S3
            if ($disk === 's3') {
                Storage::disk('s3')->setVisibility($path, 'public');
            }

            $section->attachments()->create([
                'attachment_name' => $file->getClientOriginalName(),
                'attachment_path' => $path,
                'attachment_url'  => Storage::disk($disk)->url($path),
                'source'          => $disk,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Images uploaded successfully.',
        ]);
    }

    public function destroyQuotationSectionsAttachments($id)
    {
        $attachment = QuotationSectionAttachment::findOrFail($id);

        if (!$attachment) {
            return response()->json(['status' => 'error', 'message' => 'Image not found.']);
        }

        if ($attachment->source !== 's3' && Storage::disk('public')->exists($attachment->image_path)) {
            Storage::disk('public')->delete($attachment->image_path);
        }

        $attachment->delete();

        return response()->json(['status' => 'success', 'message' => 'Attachment deleted successfully.']);
    }
}
