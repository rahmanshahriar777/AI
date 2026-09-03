<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Enquiry;
use App\Models\EnquiryAddress;
use App\Models\EnquiryImage;
use App\Models\EnquiryNote;
use App\Models\Lead;
use App\Models\LeadAddress;
use App\Models\LeadImage;
use App\Models\LeadNote;
use App\Models\MailTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

use Mail;

use App\Mail\EnquiryRejectNotify;

class EnquiryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Enquiry::join('customers', 'enquiries.customer_id', '=', 'customers.id')
                ->select([
                    'enquiries.id',
                    'enquiries.slug',
                    'enquiries.enquiry_name',
                    'enquiries.enquiry',
                    'enquiries.enquiry_status',
                    'customers.company_name',
                    'customers.contact_firstname',
                    'customers.contact_lastname',
                    'customers.contact_email',
                    'customers.contact_phone',
                    'customers.contact_mobile',
                    'enquiries.enquiry_source',
                    'enquiries.created_at'
                ])
                ->whereIn('enquiry_status', ['new', 'on_hold'])
                ->orderByDesc('enquiries.created_at');

            return Datatables::of($data)
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
                ->addColumn('enquiry', function ($row) {
                    return Str::limit(strip_tags($row->enquiry), 50); // use Illuminate\Support\Str
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="/enquiries/' . $row->slug . '" class="edit btn btn-primary btn-sm">Details</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('enquiries.index');
    }

    public function showInprogress(Request $request)
    {
        if ($request->ajax()) {

            $data = Enquiry::with(['customer', 'enquiryaddress'])
                ->select('*')
                ->where('enquiry_status', 'inprogress')
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
                    $btn = '<a href="/enquiries/' . $row->slug . '" class="edit btn btn-primary btn-sm">Details</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('enquiries.index')->with([
            'inprogress' => true,
        ]);
    }

    public function showCompleted(Request $request)
    {
        if ($request->ajax()) {

            $data = Enquiry::with(['customer', 'enquiryaddress'])
                ->select('*')
                ->where('enquiry_status', 'converted_to_lead')
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
                    $btn = '<a href="/enquiries/' . $row->slug . '" class="edit btn btn-primary btn-sm">Details</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('enquiries.index')->with([
            'completed' => true,
        ]);
    }

    public function showRejected(Request $request)
    {
        if ($request->ajax()) {

            $data = Enquiry::with(['customer', 'enquiryaddress'])
                ->select('*')
                ->whereIn('enquiry_status', ['rejected', 'archived'])
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
                    $btn = '<a href="/enquiries/' . $row->slug . '" class="edit btn btn-primary btn-sm">Details</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('enquiries.index')->with([
            'rejected' => true,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::where('status', 'active')->orderBy('id', 'desc')->get();
        return view('enquiries.create')->with(['customers' => $customers]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return $request->all();
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'companyName' => 'required_without:customer_id|nullable|string|max:255',
            'contactPersonFirstName' => 'required_without:customer_id|nullable|string|max:255',
            'contactPhone'  => 'required_without_all:customer_id,contactMobile|nullable|max:20',
            'contactMobile' => 'required_without_all:customer_id,contactPhone|nullable|max:20',
            'contactEmail' => 'required_without:customer_id|nullable|email|max:255',
            'enquiry' => 'required|string',
            'enquirydescription' => 'nullable|string',
            'enquiry_postcode' => 'required|string|max:20',
            'enquiry_address' => 'required|string',
            'enquiry_county' => 'nullable|string|max:255',
            'enquiry_country' => 'nullable|string|max:255',
            'attachments.*' => 'nullable|image|mimes:jpg,jpeg,png|max:10024',
            'enquiry_priority' => 'nullable|string|in:low,medium,high',
        ]);

        //if validated then submit again
        if ($request->ajax()) {
            return response()->json(['success'=> 'success', 'redirect' => 'POST']);
        }

        DB::beginTransaction();

        try {
            $customerid = $validated['customer_id'] ?? null;

            if ($customerid) {
                $customer = Customer::find($customerid);
            } else {
                $customer = Customer::create([
                    'company_name' => $validated['companyName'],
                    'contact_firstname' => $validated['contactPersonFirstName'],
                    'contact_lastname' => $validated['contactPersonLastName'] ?? '',
                    'contact_phone' => $validated['contactPhone'] ?? '',
                    'contact_mobile' => $validated['contactMobile'] ?? '',
                    'contact_email' => $validated['contactEmail'],
                    'status' => 'active'
                ]);

                $customerid = $customer->id;

                CustomerAddress::create([
                    'customer_id' => $customerid,
                    'contact_firstname' => $validated['contactPersonFirstName'],
                    'contact_lastname' => $validated['contactPersonLastName'] ?? '',
                    'contact_phone' => $validated['contactPhone'] ?? '',
                    'contact_mobile' => $validated['contactMobile'] ?? '',
                    'contact_email' => $validated['contactEmail'],
                    'address' => $validated['enquiry_address'],
                    'county' => $validated['enquiry_county'],
                    'postcode' => $validated['enquiry_postcode'],
                    'country' => $validated['enquiry_country'],
                    'default' => 'yes',
                    'address_type' => 'site',
                ]);
            }

            $enquiry = Enquiry::create([
                'enquiry_name' => randomenquiryGenerator(),
                'customer_id' => $customerid,
                'enquiry' => $validated['enquiry'],
                'enquiry_description' => $validated['enquirydescription'],
                'enquiry_type' => 'query',
                'enquiry_category'     => $request->input('roofing_info') ? implode(',', $request->input('roofing_info')) : null,
                'annual_maintenance'  => in_array('maintenance', $request->input('annual_maintenance', [])),
                'installations'       => in_array('install', $request->input('safety', [])),
                'repairs'             => in_array('repairs', $request->input('safety', [])),
                'testing'             => in_array('testing', $request->input('safety', [])),
                'enquiry_status' => 'new',
                'enquiry_priority' => $validated['enquiry_priority'] ?? 'low',
                'enquiry_source' => $request->input('enquiry_source') ?? 'call',
            ]);

            EnquiryAddress::create([
                'enquiry_id' => $enquiry->id,
                'contact_firstname' => $validated['contactPersonFirstName'] ?? $customer->contact_firstname,
                'contact_lastname' => $validated['contactPersonLastName'] ?? $customer->contact_lastname,
                'contact_phone' => $validated['contactPhone'] ?? $customer->contact_phone,
                'contact_mobile' => $validated['contactMobile'] ?? $customer->contact_mobile,
                'contact_email' => $validated['contactEmail'] ?? $customer->contact_email,
                'address' => $validated['enquiry_address'],
                'county' => $validated['enquiry_county'],
                'postcode' => $validated['enquiry_postcode'],
                'country' => $validated['enquiry_country']
            ]);


            if ($request->hasFile('attachments')) {
                $disk = env('FILESYSTEM_DISK', 'local');

                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('uploads/enqueries', $disk);

                    if (!$path) {
                        throw new \Exception("Failed to store file: " . $file->getClientOriginalName());
                    }

                    if ($disk === 's3') {
                        Storage::disk('s3')->setVisibility($path, 'public');
                    }

                    EnquiryImage::create([
                        'enquiry_id' => $enquiry->id,
                        'image_path' => $path,
                        'image_name' => $file->getClientOriginalName(),
                        'image_url' => Storage::disk($disk)->url($path),
                        'source' => $disk,
                    ]);
                }
            }

            $enquiryNote = $request->input('enquiryNote');
            if ($enquiryNote) {
                EnquiryNote::create([
                    'enquiry_id' => $enquiry->id,
                    'note_by' => Auth::id(),
                    'note' => $enquiryNote,
                    'note_type' => 'internal',
                    'note_status' => 'active',
                ]);
            }

            DB::commit();

            return redirect()->route('enquiries.show', $enquiry->slug)
                ->with('success', 'Enquiry created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Enquiry creation failed: ' . $e->getMessage());
            return back()->withErrors('An error occurred while creating the enquiry.');
        }
    }

    public function show(string $key)
    {
        $enquiry = Enquiry::with(['customer', 'customer.billingaddress', 'customer.siteaddress', 'enquiryaddress', 'enquiryimages', 'enquirynotes', 'enquirynotes.users'])->where('slug', $key)->first();

        if (!$enquiry) {
            return redirect()->route('enquiries.index')->with('error', 'Enquiry not found.');
        }
        $mailtemplates = MailTemplate::where('status', 'active')->orderBy('id', 'desc')->get();

        $leadmanagers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['job-manager', 'lead-job-manager']);
        })->get();

        return view('enquiries.show', compact('enquiry', 'mailtemplates', 'leadmanagers'));
    }

    public function enqueryNotes(Request $request, $enquiry_id)
    {
        $validated = $request->validate([
            'note'       => 'required|string',
        ]);
        
        $safeNote = Str::markdown($validated['note'], [
            'html_input' => 'allow', // strip
            'allow_unsafe_links' => false,
        ]);
        
        $checkEnquiry = Enquiry::find($enquiry_id);
        if (!$checkEnquiry) {
            return response()->json(['status' => 'error', 'message' => 'Enquiry not found.']);
        }

        $enquirynote = EnquiryNote::create([
            'enquiry_id'  => $enquiry_id,
            'note_by'     => Auth::id(),
            'note'        => $safeNote,
            'note_type'   => 'internal',
            'note_status' => 'active',
        ]);

        if ($enquirynote) {
            return response()->json(['status' => 'success', 'message' => 'Enquiry note added successfully.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to add enquiry note.']);
    }

    public function deleteenqueryNotes($noteid)
    {
        $note = EnquiryNote::findOrFail($noteid);

        if (!$note) {
            return response()->json(['status' => 'error', 'message' => 'Note not found.']);
        }

        $note->delete();

        return response()->json(['status' => 'success', 'message' => 'Note deleted successfully.']);
    }

    public function enqueryImages(Request $request)
    {
        $validated = $request->validate([
            'enquiry_id' => 'required|exists:enquiries,id',
            'enquiryphoto'      => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        

        $disk = env('FILESYSTEM_DISK', 'local');

        if ($request->hasFile('enquiryphoto')) {
            $file = $request->file('enquiryphoto');
            $path = $file->store('uploads/enqueries', $disk);

            if (!$path) {
                return response()->json(['status' => 'error', 'message' => 'Failed to store image.']);
            }

            if ($disk === 's3') {
                Storage::disk('s3')->setVisibility($path, 'public');
            }

            EnquiryImage::create([
                'enquiry_id'  => $validated['enquiry_id'],
                'image_path'  => $path,
                'image_name'  => $file->getClientOriginalName(),
                'image_url'   => Storage::disk($disk)->url($path),
                'source'      => $disk
            ]);

            return redirect()->back()->with([
                'status' => 'success',
                'success' => 'Enquiry image added successfully.'
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to add enquiry image.']);
    }

    public function destroyEnqueryImage($id)
    {
        $image = EnquiryImage::find($id);

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

    public function updateEnquiryOtherOptions(Request $request, $id)
    {
        $enquiry = Enquiry::find($id);
        if (!$enquiry) {
            return response()->json([
                'status' => 'error',
                'message' => 'Enquiry not found.'
            ]);
        }

        $enquiry->update([
            'enquiry_category'     => $request->input('enquiry_info') ? implode(',', $request->input('enquiry_info')) : null,
            'annual_maintenance'  => $request->input('annual_maintenance') ?? false,
            'installations'       => in_array('install', $request->input('safety_type', [])),
            'repairs'             => in_array('repairs', $request->input('safety_type', [])),
            'testing'             => in_array('testing', $request->input('safety_type', [])),
            'enquiry_priority' => $request->enquiry_priority ?? 'low',
            'enquiry_source' => $request->enquiry_source ?? 'call',
        ]);

        return back()->with([
            'status' => 'success',
            'message' => 'Enquiry options updated successfully.'
        ]);
    }

    public function updateEnquiryDetails(Request $request, $id)
    {
        $validated = $request->validate([
            'enquiry_id' => 'required|exists:enquiries,id',
            'enquirydescription' => 'required|string',
        ]);

        $enquiry = Enquiry::find($id);
        if (!$enquiry) {
            return response()->json([
                'status' => 'error',
                'message' => 'Enquiry not found.'
            ]);
        }

        $enquiry->update([
            'enquiry_description' => $validated['enquirydescription'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Enquiry details updated successfully.'
        ]);
    }

    public function updateEnquiryAddress(Request $request, $id)
    {
        $validated = $request->validate([
            'enquiry_id' => 'required|exists:enquiries,id',
            'enquiry_address' => 'required|string',
            'enquiry_postcode' => 'required|string|max:20',
            'enquiry_county' => 'nullable|string|max:255',
        ]);

        $enquiry = Enquiry::find($id);
        if (!$enquiry) {
            return response()->json([
                'status' => 'error',
                'message' => 'Enquiry not found.'
            ]);
        }

        $enquiryAddress = EnquiryAddress::where('enquiry_id', $id)->first();
        if ($enquiryAddress) {
            $enquiryAddress->update([
                'address' => $validated['enquiry_address'],
                'postcode' => $validated['enquiry_postcode'],
                'county' => $validated['enquiry_county'],
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Enquiry address not found.'
            ]);
        }

        return redirect()->back()->with([
            'status' => 'success',
            'message' => 'Enquiry address updated successfully.'
        ]);
    }

    public function destroy(string $id)
    {
        $enquiry = Enquiry::find($id);
        if ($enquiry) {
            $enquiry->delete();
            return redirect()->route('enquiries.index')->with('success', 'Enquiry deleted successfully.');
        }
        return redirect()->route('enquiries.index')->with('error', 'Enquiry not found.');
    }

    public function updateEnquiryStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string',
        ]);
        $enquiry = Enquiry::find($id);
        if ($enquiry) {    
            $enquiry->update(['enquiry_status' => $validated['status']]);
            //update lead status to archived
            $lead = Lead::where('enquiry_id', '=', $enquiry->id)->first(); 
            if($lead){
                $lead->update(['lead_status' => 'archived']);
            }
            if($validated['status']=='rejected'){
                $this->emailNotify($request);
                return response()->json(['status' => 'success', 'message' => 'Enquiry status set to reject successfully.']);
            }
            return redirect()->back()->with('success', 'Enquiry updated successfully.');
        }
        return redirect()->back()->with('error', 'Enquiry not found.');
    }

    public function enquerytoLeadConversion(Request $request)
    {
        // return $request->all();
        $validated = $request->validate([
            'enquiry_id' => 'required|exists:enquiries,id',
            'status' => 'required|string',
        ]);

        $enquiry = Enquiry::find($validated['enquiry_id']);
        if (!$enquiry) {
            return response()->json(['status' => 'error', 'message' => 'Enquiry not found.']);
        }

        $enquiryaddress = EnquiryAddress::where('enquiry_id', $validated['enquiry_id'])->first();
        $enquiryimages = EnquiryImage::where('enquiry_id', $validated['enquiry_id'])->get();
        $enquirynotes = EnquiryNote::where('enquiry_id', $validated['enquiry_id'])->get();

        $lead = Lead::create([
            'enquiry_id' => $validated['enquiry_id'],
            'enquiry' => $enquiry->enquiry,
            'lead_name' => $enquiry->enquiry_name,
            'lead_description' => $enquiry->enquiry_description,
            'customer_id' => $enquiry->customer_id,
            'lead_type' => $enquiry->enquiry_type,
            'lead_category' => $enquiry->enquiry_category,
            'lead_priority' => $enquiry->enquiry_priority,
            'lead_status' => 'new',
            'annual_maintenance'  => $enquiry->annual_maintenance,
            'installations'       => $enquiry->installations,
            'repairs'             => $enquiry->repairs,
            'testing'             => $enquiry->testing,
            'assigned_to' => json_encode($request->users),
        ]);

        LeadAddress::create([
            'lead_id' => $lead->id,
            'contact_firstname' => $enquiryaddress->contact_firstname,
            'contact_lastname' => $enquiryaddress->contact_lastname,
            'contact_phone' => $enquiryaddress->contact_phone,
            'contact_mobile' => $enquiryaddress->contact_mobile,
            'contact_email' => $enquiryaddress->contact_email,
            'address' => $enquiryaddress->address,
            'county' => $enquiryaddress->county,
            'postcode' => $enquiryaddress->postcode,
            'country' => $enquiryaddress->country,
        ]);

        foreach ($enquiryimages as $image) {
            LeadImage::create([
                'lead_id' => $lead->id,
                'image_path' => $image->image_path,
                'image_name' => $image->image_name,
                'image_url' => $image->image_url,
                'source' => $image->source
            ]);
        }
        foreach ($enquirynotes as $note) {
            LeadNote::create([
                'lead_id' => $lead->id,
                'note_by' => $note->note_by,
                'note' => $note->note,
                'note_type' => $note->note_type,
                'note_status' => $note->note_status,
            ]);
        }
        $enquiry->update(['enquiry_status' => $validated['status']]);

        return $lead;

        return response()->json(['status' => 'success', 'message' => 'Enquiry converted to lead successfully.']);
    }

    public function emailNotify(Request $request){
        $validated = $request->validate([
            'sendtoemail' => 'string',
            'mailsubject' => 'string',
            'mailmessage' => 'string',
        ]);
        
        $destination = $validated['sendtoemail'];
        $subject = $validated['mailsubject'];
        $message = $validated['mailmessage'];
        
        try{
            Mail::to($destination)->queue(new EnquiryRejectNotify($subject, $message));
        }
        catch(Exception $e){
            
        }
    }
}
