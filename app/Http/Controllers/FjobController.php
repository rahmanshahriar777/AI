<?php

namespace App\Http\Controllers;

use App\Models\Fjob;
use App\Models\FjobAttachment;
use App\Models\FjobAttribute;
use App\Models\FjobAttributeDetail;
use App\Models\FjobNote;
use App\Models\JobAttribute;
use App\Models\JobAttributeDetail;
use App\Models\JobType;
use App\Models\MailTemplate;
use App\Models\FjobImage;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FjobController extends Controller
{

    public function index(Request $request)
    {
        

        if ($request->ajax()) {

            // $data = Fjob::with([
            //     'customer:id,company_name,contact_firstname,contact_lastname,contact_email,contact_phone,contact_mobile',
            // ])
            $data = DB::table('fjobs')
                ->leftJoin('job_types', 'fjobs.job_type', '=', 'job_types.slug')
                ->leftJoin('customers', 'fjobs.customer_id', '=', 'customers.id')
                ->leftJoin('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                ->select([
                    'fjobs.id',
                    'job_name',
                    'job_title',
                    'fjobs.slug',
                    'job_description',
                    'customers.company_name',
                    'customers.contact_firstname',
                    'customers.contact_lastname',
                    'customers.contact_email',
                    'customers.contact_phone',
                    'customers.contact_mobile',
                    'job_type',
                    'job_status',
                    'fjobs.enquiry',
                    'job_types.name AS job_type_name'
                ])
                ->whereIn('job_status', ['draft', 'accepted', 'new', 'inprogress', 'on_hold'])
                ->orderByDesc('fjobs.created_at');

            
            if (request()->has('filter_jobtype') && request()->input('filter_jobtype') != '') {
                $filter_jobtype = request()->input('filter_jobtype');
                $data->whereIn('job_type', [$filter_jobtype]);
            }
            if (request()->has('filter_roofing') && request()->input('filter_roofing') != '') {
                $filter_roofing = request()->input('filter_roofing');
                $data->whereIn('job_category', [$filter_roofing]);
            }
            if (request()->has('filter_annual_maintenance') && request()->input('filter_annual_maintenance') != '') {
                $filter_annual_maintenance = request()->input('filter_annual_maintenance');
                $data->where('annual_maintenance', '=', $filter_annual_maintenance == 'yes' );
            }
            if (request()->has('filter_safety') && request()->input('filter_safety') != '') {
                $filter_safety = request()->input('filter_safety');
                if($filter_safety=='install'){
                    $data->where('installations', '=', true);
                }
                else if($filter_safety=='repairs'){
                    $data->where('repairs', '=', true);
                }
                else if($filter_safety=='testing'){
                    $data->where('testing', '=', true);
                }
                
            }
            if (request()->has('filter_priority') && request()->input('filter_priority') != '') {
                $filter_priority = request()->input('filter_priority');
                $data->whereIn('job_priority', [$filter_priority]);
            }

            return DataTables::of($data)
            ->filter(function ($query) {
                if (request()->has('search') && request()->input('search.value') != '') {
                    $searchValue = request()->input('search.value');
                    $query->where(function($q) use ($searchValue) {
                        $q->where('job_name', 'like', "%{$searchValue}%")
                        ->orWhere('customers.company_name', 'like', "%{$searchValue}%")
                        ->orWhere('job_title', 'like', "%{$searchValue}%")
                        ->orWhere('fjobs.enquiry', 'like', "%{$searchValue}%")
                        ->orWhere('job_description', 'like', "%{$searchValue}%")
                        ->orWhere('fjobs.id', 'like', "%{$searchValue}%")
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
                    });
                }
            }, false)
                ->addIndexColumn()
                ->addColumn('job_name', function ($row) {
                    return ($row->job_name ?? '') . ' - ' . ($row->job_title ?? '');
                })
                ->addColumn('company_name', function ($row) {
                    return $row->company_name ?? '-';
                })
                ->addColumn('customer_name', function ($row) {
                    return $row->contact_firstname . ' ' . $row->contact_lastname;
                })
                ->addColumn('customer_email', function ($row) {
                    return $row->contact_email ?? '-';
                })
                ->addColumn('customer_phone', function ($row) {
                    return $row->contact_phone ?? '-';
                })
                ->addColumn('customer_mobile', function ($row) {
                    return $row->contact_mobile ?? '-';
                })
                ->addColumn('enquiry', function ($row) {
                    return Str::limit(strip_tags($row->enquiry), 80); // use Illuminate\Support\Str
                })
                ->addColumn('action', function ($row) {
                    return '<a href="/job/' . $row->id . '" class="edit btn btn-primary btn-sm">Details</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $jobtype = JobType::where('status', 'active')
            ->orderBy('name', 'asc')->get();
        return view('jobs.index', compact( 'jobtype'));
    }

    public function showCompletedJobs(Request $request)
    {
        if ($request->ajax()) {

            $data = Fjob::with([
                'customer:id,company_name,contact_firstname,contact_lastname,contact_email,contact_phone,contact_mobile',
            ])
                ->select([
                    'id',
                    'job_name',
                    'job_title',
                    'slug',
                    'job_description',
                    'customer_id',
                    'job_type',
                    'job_status',
                    'enquiry'
                ])
                ->whereIn('job_status', ['completed', 'archived'])
                ->orderByDesc('created_at');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('job_name', function ($row) {
                    return ($row->job_name ?? '') . ' - ' . ($row->job_title ?? '');
                })
                ->addColumn('company_name', function ($row) {
                    return $row->customer->company_name ?? '-';
                })
                ->addColumn('customer_name', function ($row) {
                    return ($row->customer->contact_firstname ?? '') . ' ' . ($row->customer->contact_lastname ?? '');
                })
                ->addColumn('customer_email', function ($row) {
                    return $row->customer->contact_email ?? '-';
                })
                ->addColumn('customer_phone', function ($row) {
                    return $row->customer->contact_phone ?? '-';
                })
                ->addColumn('customer_mobile', function ($row) {
                    return $row->customer->contact_mobile ?? '-';
                })
                ->addColumn('enquiry', function ($row) {
                    return Str::limit(strip_tags($row->enquiry), 80); // use Illuminate\Support\Str
                })
                ->addColumn('action', function ($row) {
                    return '<a href="/job/' . $row->id . '" class="edit btn btn-primary btn-sm">Details</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $jobtype = JobType::where('status', 'active')
            ->orderBy('name', 'asc')->get();
        return view('jobs.index', compact( 'jobtype'))->with([
            'completed' => true,
        ]);
        // return view('jobs.index')->with([
        //     'completed' => true,
        // ]);
    }

    public function showRejectedJobs(Request $request)
    {
        if ($request->ajax()) {

            $data = Fjob::with([
                'customer:id,company_name,contact_firstname,contact_lastname,contact_email,contact_phone,contact_mobile',
            ])
                ->select([
                    'id',
                    'job_name',
                    'job_title',
                    'slug',
                    'job_description',
                    'customer_id',
                    'job_type',
                    'job_status',
                    'enquiry'
                ])
                ->where('job_status', 'rejected')
                ->orderByDesc('created_at');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('job_name', function ($row) {
                    return ($row->job_name ?? '') . ' - ' . ($row->job_title ?? '');
                })
                ->addColumn('company_name', function ($row) {
                    return $row->customer->company_name ?? '-';
                })
                ->addColumn('customer_name', function ($row) {
                    return ($row->customer->contact_firstname ?? '') . ' ' . ($row->customer->contact_lastname ?? '');
                })
                ->addColumn('customer_email', function ($row) {
                    return $row->customer->contact_email ?? '-';
                })
                ->addColumn('customer_phone', function ($row) {
                    return $row->customer->contact_phone ?? '-';
                })
                ->addColumn('customer_mobile', function ($row) {
                    return $row->customer->contact_mobile ?? '-';
                })
                ->addColumn('enquiry', function ($row) {
                    return Str::limit(strip_tags($row->enquiry), 80); // use Illuminate\Support\Str
                })
                ->addColumn('action', function ($row) {
                    return '<a href="/job/' . $row->id . '" class="edit btn btn-primary btn-sm">Details</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $jobtype = JobType::where('status', 'active')
            ->orderBy('name', 'asc')->get();
        return view('jobs.index', compact( 'jobtype'))->with([
            'rejected' => true,
        ]);
        // return view('jobs.index')->with([
        //     'rejected' => true,
        // ]);
    }


    public function show($key)
    {
        $job = Fjob::with([
            'customer',
            'customer.billingaddress',
            'customer.siteaddress',
            'jobaddress',
            'jobimages',
            'jobnotes',
            'jobnotes.user',
            'jobattachments',
            'jobquotations',
            'jobattributes',
            'jobattributes.details'
        ])
            ->where('id', $key)->first();
        // return $job;
        if (!$job) {
            return redirect()->route('jobs.index')->with('error', 'Job details not found.');
        }

        $jobtype = JobType::where('status', 'active')
            ->orderBy('name', 'asc')->get();

        $mailtemplates = MailTemplate::where('status', 'active')
            ->orderBy('id', 'desc')->get();

        return view('jobs.show', compact('job', 'jobtype', 'mailtemplates'));
    }

    public function showCompleted($key) {}

    public function uploadJobAttachments(Request $request)
    {
        $request->validate([
            'jobid' => 'required|exists:fjobs,id',
            'attachment_type' => 'required|string',
            'title' => 'required|string|max:255',
            'attachment' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10024',
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

            FjobAttachment::create([
                'fjob_id'  => $request->input('jobid'),
                'attachment_path'  => $path,
                'attachment_name'  => $request->input('title'),
                'description' => $request->input('attachment_type'),
                'attachment_type'  => $file->getClientOriginalExtension(),
                'attachment_url'   => Storage::disk($disk)->url($path),
                'source'      => $disk,
                'uploaded_by' => Auth::id(),
                'status' => 'active',
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
        $attachment = FjobAttachment::find($id);

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

    public function updateJobStatus(Request $request, $id)
    {
        $job = Fjob::find($id);
        if (!$job) {
            return response()->json(['error' => 'Job not found.'], 404);
        }

        $job->update([
            'job_status' => $request->job_status,
        ]);

        return response()->json(['success' => 'Job status updated successfully.']);
    }

    public function updateJobTitle(Request $request, $id)
    {
        $request->validate([
            'job_title' => 'required|string|max:255',
            'job_type' => 'required|string|max:100',
        ]); 
        $job = Fjob::find($id);
        if (!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job not found.'
            ], 404);
        }

        $job->update([
            'job_title' => $request->job_title,
            'job_type' => $request->job_type,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Job title updated successfully.'
        ]);
    }

    public function updateJobType(Request $request, $id)
    {
        $job = Fjob::find($id);
        if (!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job not found.'
            ], 404);
        }

        $job->update([
            'job_type' => $request->job_type,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Job type updated successfully.'
        ]);
    }

    public function jobNotes(Request $request, $jobid)
    {
        $validated = $request->validate([
            'note'       => 'required|string',
        ]);

        $job = Fjob::find($jobid);
        if (!$job) {
            return response()->json(['status' => 'error', 'message' => 'Job not found.']);
        }

        $jobnote = FjobNote::create([
            'fjob_id'  => $jobid,
            'note_by'     => Auth::id(),
            'note'        => $validated['note'],
            'note_type'   => 'internal',
            'note_status' => 'active',
        ]);

        if ($jobnote) {
            return response()->json(['status' => 'success', 'message' => 'Note added successfully.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to add enquiry note.']);
    }

    public function jobAddAttributes($jobid)
    {
        $job = Fjob::find($jobid);
        if (!$job) {
            return redirect()->route('jobs.index')->with('error', 'Job not found.');
        }

        $jobAttributes = JobAttribute::with('attributedetails')
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        $exsistingfjobattributes = FjobAttribute::with('details')->where('fjob_id', $jobid)->get();
        $selectedAttributes = [];
        foreach ($exsistingfjobattributes as $attr) {
            $selectedAttributes[$attr->job_attribute_id] = [
                'value' => 'yes', // You saved only selected attributes
                'details' => collect($attr->details)->mapWithKeys(function ($detail) {
                    return [$detail->job_attribute_detail_id => 'yes'];
                })->toArray()
            ];
        }

        // return $jobAttributes;

        return view('jobs.add-attributes', compact('job', 'jobAttributes', 'selectedAttributes'));
    }


    public function storeJobAddAttributes(Request $request, $fjobId)
    {
        //clear all attributes value first
        FjobAttribute::where('fjob_id', $fjobId)->delete();
    
        $attributes = $request->input('attributes', []);

        foreach ($attributes as $jobAttributeId => $attributeData) {
            // Only proceed if parent is selected as 'yes'
            if (!isset($attributeData['selected']) || $attributeData['selected'] !== 'yes') {
                continue;
            }

            // Check if an FjobAttribute already exists
            $fjobAttribute = FjobAttribute::firstOrNew([
                'fjob_id' => $fjobId,
                'job_attribute_id' => $jobAttributeId
            ]);

            $fjobAttribute->attribute_value = JobAttribute::find($jobAttributeId)?->name;
            $fjobAttribute->remarks = $fjobAttribute->remarks ?? null;
            $fjobAttribute->save();

            // Handle attribute details (children)
            if (isset($attributeData['children']) && is_array($attributeData['children'])) {
                // Get all submitted detail IDs with value "yes"
                $submittedYesDetails = collect($attributeData['children'])
                    ->filter(fn($val) => $val === 'yes')
                    ->keys()
                    ->toArray();

                // Delete any previously stored details that are no longer selected
                FjobAttributeDetail::where('fjob_attribute_id', $fjobAttribute->id)
                    ->whereNotIn('job_attribute_detail_id', $submittedYesDetails)
                    ->delete();

                // Loop through submitted "yes" children and create or update
                foreach ($submittedYesDetails as $detailId) {
                    FjobAttributeDetail::updateOrCreate(
                        [
                            'fjob_attribute_id' => $fjobAttribute->id,
                            'job_attribute_detail_id' => $detailId,
                        ],
                        [
                            'detail_value' => JobAttributeDetail::find($detailId)?->value,
                            'remarks' => null,
                        ]
                    );
                }
            } else {
                // No children submitted → delete all existing children for this attribute
                FjobAttributeDetail::where('fjob_attribute_id', $fjobAttribute->id)->delete();
            }
        }

        return redirect()->route('job.show', $fjobId)->with('success', 'Job attributes saved successfully.');
    }

    public function addJobImages(Request $request)
    {
        $validated = $request->validate([
            'job_id' => 'required|exists:enquiries,id',
            'jobphoto'      => 'required|image|mimes:'.$this->mimetypes.'|max:2048',
        ]);
        

        $disk = env('FILESYSTEM_DISK', 'local');

        if ($request->hasFile('jobphoto')) {
            $file = $request->file('jobphoto');
            $path = $file->store('uploads/jobs', $disk);

            if (!$path) {
                return response()->json(['status' => 'error', 'message' => 'Failed to store image.']);
            }

            if ($disk === 's3') {
                Storage::disk('s3')->setVisibility($path, 'public');
            }

            FjobImage::create([
                'fjob_id'  => $validated['job_id'],
                'image_path'  => $path,
                'image_name'  => $file->getClientOriginalName(),
                'image_url'   => Storage::disk($disk)->url($path),
                'source'      => $disk
            ]);

            return response()->json(['status' => 'success', 'message' => 'Job image added successfully.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to add enquiry image.']);
    }

    public function destroyJobImage($id)
    {
        $image = FjobImage::find($id);

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

    public function updateJobDetails(Request $request, $id)
    {
        $validated = $request->validate([
            'job_id' => 'required|exists:enquiries,id',
            'jobdescription' => 'required|string',
        ]);

        $job = Fjob::find($id);
        if (!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job not found.'
            ]);
        }

        $job->update([
            'job_description' => $validated['jobdescription'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Job details updated successfully.'
        ]);
    }

    public function deleteJobNotes($noteid)
    {
        $note = FjobNote::findOrFail($noteid);

        if (!$note) {
            return response()->json(['status' => 'error', 'message' => 'Note not found.']);
        }

        $note->delete();

        return response()->json(['status' => 'success', 'message' => 'Note deleted successfully.']);
    }

    public function updateJobAddress(Request $request, $id)
    {
        $job = Fjob::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job not found.'], 404);
        }

        $job->jobaddress()->update([
            'address' => $request->job_address,
            //'address_line2' => $request->address_line2,
            //'city' => $request->city,
            'postcode' => $request->job_postcode,
            'county' => $request->job_county,
        ]);

        return response()->json(['message' => 'Job address updated successfully.']);
    }
}
