<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Fjob;
use App\Models\FjobAddress;
use App\Models\FjobAttachment;
use App\Models\FjobImage;
use App\Models\FjobNote;
use App\Models\FjobQuotation;
use App\Models\Lead;
use App\Models\LeadAttachment;
use App\Models\LeadImage;
use App\Models\LeadNote;
use App\Models\MailTemplate;
use Exception;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\ToArray;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\QuotationTemplate;

use Mail;

use App\Mail\EnquiryRejectNotify;

class LeadController extends Controller
{
    public function __construct()
    {
        // Logic to display leads
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = DB::table('leads')
                ->leftJoin('customers', 'leads.customer_id', '=', 'customers.id')
                ->leftJoin('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                ->select(
                    'leads.id',
                    'leads.slug',
                    'leads.lead_status',
                    'leads.enquiry',
                    'leads.created_at',
                    'customers.company_name',
                    'customers.contact_firstname',
                    'customers.contact_lastname',
                    'customers.contact_email',
                    'customers.contact_phone',
                    'customers.contact_mobile'
                )
                ->whereIn('leads.lead_status', ['new', 'inprogress', 'on_hold'])
                ->orderByDesc('leads.created_at');

            return Datatables::of($data)
            ->filter(function ($query) {
                if (request()->has('search') && request()->input('search.value') != '') {
                    $searchValue = request()->input('search.value');
                    $query->where('lead_name', 'like', "%{$searchValue}%")
                        ->orWhere('customers.company_name', 'like', "%{$searchValue}%")
                        ->orWhere('lead_title', 'like', "%{$searchValue}%")
                        ->orWhere('enquiry', 'like', "%{$searchValue}%")
                        ->orWhere('lead_description', 'like', "%{$searchValue}%")
                        ->orWhere('leads.id', 'like', "%{$searchValue}%")
                        ->orWhere('customers.contact_firstname', 'like', "%{$searchValue}%")
                        ->orWhere('customers.contact_lastname', 'like', "%{$searchValue}%")
                        ->orWhere('customers.contact_phone', 'like', "%{$searchValue}%")
                        ->orWhere('customers.contact_mobile', 'like', "%{$searchValue}%")
                        ->orWhere('customers.contact_email', 'like', "%{$searchValue}%")
                        ->orWhere('customer_addresses.postcode', 'like', "%{$searchValue}%")
                        ->orWhereRaw(
                            "CONCAT(customers.contact_firstname, ' ', customers.contact_lastname) LIKE ?",
                            ["%{$searchValue}%"]
                        );
                }
            }, true)
                ->addIndexColumn()
                ->addColumn('company_name', function ($row) {
                    return $row->company_name;
                })
                ->addColumn('customer_name', function ($row) {
                    return $row->contact_firstname . ' ' . $row->contact_lastname;
                })
                ->addColumn('customer_email', function ($row) {
                    return $row->contact_email;
                })
                ->addColumn('customer_phone', function ($row) {
                    return $row->contact_phone;
                })
                ->addColumn('customer_mobile', function ($row) {
                    return $row->contact_mobile;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="/leads/' . $row->slug . '" class="edit btn btn-primary btn-sm">Details</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('leads.index');
    }

    public function showCompleted(Request $request)
    {
        if ($request->ajax()) {

            $data = Lead::with(['customer', 'leadaddress'])
                ->select('*')
                ->whereIn('lead_status', ['converted_to_job'])
                ->orderByDesc('created_at');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('company_name', function ($row) {
                    return $row->customer->company_name;
                })
                ->addColumn('customer_name', function ($row) {
                    return $row->customer->contact_firstname . ' ' . $row->customer->contact_lastname;
                })
                ->addColumn('customer_email', function ($row) {
                    return $row->customer->contact_email;
                })
                ->addColumn('customer_phone', function ($row) {
                    return $row->customer->contact_phone;
                })
                ->addColumn('customer_mobile', function ($row) {
                    return $row->customer->contact_mobile;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="/leads/' . $row->slug . '" class="edit btn btn-primary btn-sm">Details</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('leads.index')->with([
            'completed' => true,
        ]);
    }

    public function showRejected(Request $request)
    {
        if ($request->ajax()) {

            $data = Lead::with(['customer', 'leadaddress'])
                ->select('*')
                ->whereIn('lead_status', ['rejected'])
                ->orderByDesc('created_at');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('company_name', function ($row) {
                    return $row->customer->company_name;
                })
                ->addColumn('customer_name', function ($row) {
                    return $row->customer->contact_firstname . ' ' . $row->customer->contact_lastname;
                })
                ->addColumn('customer_email', function ($row) {
                    return $row->customer->contact_email;
                })
                ->addColumn('customer_phone', function ($row) {
                    return $row->customer->contact_phone;
                })
                ->addColumn('customer_mobile', function ($row) {
                    return $row->customer->contact_mobile;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="/leads/' . $row->slug . '" class="edit btn btn-primary btn-sm">Details</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('leads.index')->with([
            'rejected' => true,
        ]);
    }

    public function showArchived(Request $request)
    {
        if ($request->ajax()) {

            $data = Lead::with(['customer', 'leadaddress'])
                ->select('*')
                ->whereIn('lead_status', ['archived'])
                ->orderByDesc('created_at');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('company_name', function ($row) {
                    return $row->customer->company_name;
                })
                ->addColumn('customer_name', function ($row) {
                    return $row->customer->contact_firstname . ' ' . $row->customer->contact_lastname;
                })
                ->addColumn('customer_email', function ($row) {
                    return $row->customer->contact_email;
                })
                ->addColumn('customer_phone', function ($row) {
                    return $row->customer->contact_phone;
                })
                ->addColumn('customer_mobile', function ($row) {
                    return $row->customer->contact_mobile;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="/leads/' . $row->slug . '" class="edit btn btn-primary btn-sm">Details</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('leads.index')->with([
            'archived' => true,
        ]);
    }
    public function showInprogress(Request $request)
    {
        if ($request->ajax()) {

            $data = Lead::with(['customer', 'leadaddress'])
                ->select('*')
                ->whereIn('lead_status', ['inprogress'])
                ->orderByDesc('created_at');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('company_name', function ($row) {
                    return $row->customer->company_name;
                })
                ->addColumn('customer_name', function ($row) {
                    return $row->customer->contact_firstname . ' ' . $row->customer->contact_lastname;
                })
                ->addColumn('customer_email', function ($row) {
                    return $row->customer->contact_email;
                })
                ->addColumn('customer_phone', function ($row) {
                    return $row->customer->contact_phone;
                })
                ->addColumn('customer_mobile', function ($row) {
                    return $row->customer->contact_mobile;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="/leads/' . $row->slug . '" class="edit btn btn-primary btn-sm">Details</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('leads.index')->with([
            'inprogress' => true,
        ]);
    }

    public function show($key, $viewwith=null)
    {
        $lead = Lead::with([
            'customer',
            'customer.billingaddress',
            'customer.siteaddress',
            'leadaddress',
            'leadimages',
            'leadnotes',
            'leadnotes.user',
            'leadattachments',
            'completedquotation',
        ])
            ->where('slug', $key)->first();
        // dd($lead);
        if (!$lead) {
            return redirect()->route('leads.index')->with('error', 'Lead details not found.');
        }
        $mailtemplates = MailTemplate::where('status', 'active')
            ->orderBy('id', 'desc')->get();
    
        $enquiry = Enquiry::where('id', $lead->enquiry_id)->first();

        if($viewwith=='quotations'){
            $customerdata = Customer::with(['billingaddress'])
            ->where('id', $lead->customer_id)->first();

            $quotationTemplates = QuotationTemplate::where('status', 1)
            ->orderBy('template_name', 'asc')
            ->get();
            return view('leads.show', compact('lead', 'mailtemplates', 'enquiry','viewwith','customerdata','quotationTemplates'));
        }

        return view('leads.show', compact('lead', 'mailtemplates', 'enquiry'));
    }

    public function addLeadImages(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'leadphoto'      => 'required|image|mimes:jpg,jpeg,png|max:10024',
        ]);

        $disk = env('FILESYSTEM_DISK', 'local');

        if ($request->hasFile('leadphoto')) {
            $file = $request->file('leadphoto');
            $path = $file->store('uploads/leads', $disk);

            if (!$path) {
                return response()->json(['status' => 'error', 'message' => 'Failed to store image.']);
            }

            if ($disk === 's3') {
                Storage::disk('s3')->setVisibility($path, 'public');
            }

            LeadImage::create([
                'lead_id'  => $validated['lead_id'],
                'image_path'  => $path,
                'image_name'  => $file->getClientOriginalName(),
                'image_url'   => Storage::disk($disk)->url($path),
                'source'      => $disk
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Lead image added successfully.'
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to add enquiry image.']);
    }

    public function destroyLeadImage($id)
    {
        $image = LeadImage::find($id);

        if (!$image) {
            return response()->json(['status' => 'error', 'message' => 'Image not found.']);
        }

        // Optional: delete from storage if needed
        if ($image->source !== 's3' && Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return response()->json(['status' => 'success', 'message' => 'Image deleted successfully.']);
    }

    public function leadNotes(Request $request, $leadid)
    {
        $validated = $request->validate([
            'note'       => 'required|string',
        ]);

        $lead = Lead::find($leadid);
        if (!$lead) {
            return response()->json(['status' => 'error', 'message' => 'Lead not found.']);
        }

        $enquirynote = LeadNote::create([
            'lead_id'  => $leadid,
            'note_by'     => Auth::id(),
            'note'        => $validated['note'],
            'note_type'   => 'internal',
            'note_status' => 'active',
        ]);

        if ($enquirynote) {
            return response()->json(['status' => 'success', 'message' => 'Note added successfully.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to add enquiry note.']);
    }

    public function deleteLeadNotes($id)
    {
        $leadNote = LeadNote::find($id);
        if ($leadNote) {
            $leadNote->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Lead note deleted successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Lead note not found.'
            ]);
        }
    }

    public function updateLeadDetails(Request $request, $id)
    {
        $validated = $request->validate([
            'leaddescription' => 'required|string',
        ]);
        $lead = Lead::find($id);
        if (!$lead) {
            return response()->json(['error' => 'Lead not found.'], 404);
        }

        $lead->update([
            'lead_description' => $validated['leaddescription'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Lead details updated successfully.'
        ]);
    }

    public function updateLeadAddress(Request $request, $id)
    {
        $lead = Lead::find($id);
        if (!$lead) {
            return response()->json(['message' => 'Lead not found.'], 404);
        }

        $lead->leadaddress()->update([
            'address' => $request->lead_address,
            //'address_line2' => $request->address_line2,
            //'city' => $request->city,
            'postcode' => $request->lead_postcode,
            'county' => $request->lead_county,
        ]);

        return response()->json(['message' => 'Lead address updated successfully.']);
    }

    public function uploadLeadAttachments(Request $request)
    {
        $request->validate([
            'leadid' => 'required|exists:leads,id',
            'attachment_type' => 'required|string',
            'title' => 'required|string|max:255',
            'attachment' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10024',
        ]);

        $disk = env('FILESYSTEM_DISK', 'local');

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('uploads/attachments', $disk);

            if (!$path) {
                return response()->json(['status' => 'error', 'message' => 'Failed to store image.']);
            }

            if ($disk === 's3') {
                Storage::disk('s3')->setVisibility($path, 'public');
            }

            LeadAttachment::create([
                'lead_id'  => $request->input('leadid'),
                'attachment_path'  => $path,
                'attachment_name'  => $request->input('title'),
                'description' => $request->input('attachment_type'),
                'attachment_type'  => $file->getClientOriginalExtension(),
                'attachment_url'   => Storage::disk($disk)->url($path),
                'source'      => $disk,
                'uploaded_by' => Auth::id(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Attachment added successfully.'
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function destroyLeadAttachment($id)
    {
        $attachment = LeadAttachment::find($id);

        if (!$attachment) {
            return response()->json(['status' => 'error', 'message' => 'Attachment not found.']);
        }

        // Optional: delete from storage if needed
        if ($attachment->source !== 's3' && Storage::disk('public')->exists($attachment->attachment_path)) {
            Storage::disk('public')->delete($attachment->attachment_path);
        }

        $attachment->delete();

        return response()->json(['status' => 'success', 'message' => 'Attachment deleted successfully.']);
    }

    public function updateLeadStatus(Request $request, $id)
    {
        $lead = Lead::find($id);
        if (!$lead) {
            return response()->json(['error' => 'Lead not found.'], 404);
        }

        $lead_attribute = [
            'lead_status' => $request->lead_status,
        ];

        if(!empty($request->lead_status_message)){
            $lead_attribute['lead_close_reason'] = $request->lead_status_message . '<br>' . $lead->lead_close_reason;
        }

        $updateSuccess = $lead->update($lead_attribute);

        if ($updateSuccess) {
            if($request->lead_status=='rejected'){
                return response()->json(['status' => 'success', 'message' => 'Reject successfully.']);
            }

            return response()->json(['status' => 'success', 'message' => 'Lead status updated successfully.']);
        }
        else{
            return response()->json(['status' => 'error', 'message' => 'Failed to reject.']);
        }
    }

    public function leadtoJobConversion(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'status' => 'required|string',
        ]);

        $lead = Lead::with([
            'leadaddress',
            'leadimages',
            'leadnotes',
            'leadnotes.user',
            'leadattachments',
            'completedquotation'
        ])
            ->where('id', $validated['lead_id'])->first();

        $fjob = Fjob::create([
            'enquiry_id' => $lead->enquiry_id,
            'enquiry' => $lead->enquiry,
            'lead_id' => $validated['lead_id'],
            'job_name' => $lead->lead_name,
            'slug' => $lead->slug,
            'job_title' => $lead->lead_title,
            'job_description' => $lead->lead_description,
            'customer_id' => $lead->customer_id,
            'job_type' => $lead->lead_type,
            'job_category' => $lead->lead_category,
            'job_priority' => $lead->lead_priority,
            'job_status' => 'draft'
        ]);

        $jobid = $fjob->id;

        if ($lead->leadaddress) {
            FjobAddress::create([
                'fjob_id' => $jobid,
                'contact_firstname' => $lead->leadaddress->contact_firstname,
                'contact_lastname' => $lead->leadaddress->contact_lastname,
                'contact_email' => $lead->leadaddress->contact_email,
                'contact_phone' => $lead->leadaddress->contact_phone,
                'contact_mobile' => $lead->leadaddress->contact_mobile,
                'address' => $lead->leadaddress->address,
                'county' => $lead->leadaddress->county,
                'postcode' => $lead->leadaddress->postcode,
                'country' => $lead->leadaddress->country,
                'location' => $lead->leadaddress->location
            ]);
        }

        if ($lead->leadimages) {
            foreach ($lead->leadimages as $image) {
                FjobImage::create([
                    'fjob_id' => $jobid,
                    'image_path' => $image->image_path,
                    'image_name' => $image->image_name,
                    'image_url' => $image->image_url,
                    'source' => $image->source
                ]);
            }
        }

        if ($lead->leadnotes) {
            foreach ($lead->leadnotes as $note) {
                FjobNote::create([
                    'fjob_id' => $jobid,
                    'note_by' => $note->note_by,
                    'note' => $note->note,
                    'note_type' => $note->note_type,
                    'note_status' => $note->note_status,
                ]);
            }
        }

        if ($lead->completedquotation) {
            FjobQuotation::create([
                'fjob_id' => $jobid,
                'quotation_id' => $lead->completedquotation->id,
                'quotation_number' => $lead->completedquotation->quotation_version,
                'quotation_date' => $lead->completedquotation->quotation_date,
                'valid_until' => $lead->completedquotation->valid_until,
                'total_amount' => $lead->completedquotation->total_amount,
                'status' => $lead->completedquotation->status,
            ]);
        }

        if ($lead->leadattachments) {
            foreach ($lead->leadattachments as $attachment) {
                FjobAttachment::create([
                    'fjob_id' => $jobid,
                    'attachment_name' => $attachment->attachment_name,
                    'description' => $attachment->description,
                    'attachment_path' => $attachment->attachment_path,
                    'attachment_url' => $attachment->attachment_url,
                    'attachment_type' => $attachment->attachment_type,
                    'source' => $attachment->source,
                    'uploaded_by' => Auth::id(),
                ]);
            }
        }

        $lead->lead_status = $validated['status'];
        $lead->save();

        return response()->json(['status' => 'success', 'message' => 'Lead converted to job successfully.']);
    }

    public function updateLeadOtherOptions(Request $request, $id)
    {
        $lead = Lead::find($id);
        if (!$lead) {
            return back()->with([
                'error' => 'Lead not found.'
            ]);
        }
    
        $lead->update([
            'lead_category'     => $request->input('lead_info') ? implode(',', $request->input('lead_info')) : null,
            'annual_maintenance'  => $request->input('annual_maintenance') ?? false,
            'installations'       => in_array('install', $request->input('safety_type', [])),
            'repairs'             => in_array('repairs', $request->input('safety_type', [])),
            'testing'             => in_array('testing', $request->input('safety_type', [])),
            'lead_priority' => $request->lead_priority ?? 'low',
            'lead_source' => $request->lead_source ?? 'call',
        ]);

        return back()->with([
            'success' => 'Lead options updated successfully.'
        ]);
    }
}
