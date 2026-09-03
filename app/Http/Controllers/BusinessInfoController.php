<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBusinessInfoRequest;
use App\Http\Requests\UpdateBusinessInfoRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BusinessInfo;
use App\Models\Enquiry;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class BusinessInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = BusinessInfo::select('*');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $showUrl = route('businessinfo.show', $row->id); // Edit Route
                    return '
                    <a href="' . $showUrl . '" class="show btn btn-primary btn-sm">Show</a>
                    <button class="delete btn btn-danger btn-sm" data-id="' . $row->id . '">Delete</button>
                ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        else{
            //TODO auth has permission
                
            $businessinfo = BusinessInfo::get();
            $businessinfoCount = $businessinfo->count();
            if($businessinfoCount==0){
                return $this->create();
            }
            elseif($businessinfoCount==1){
                $businessinfo = BusinessInfo::first();
                // return view('businessinfo.edit')->with([
                //     'businessinfo' => $businessinfo
                // ]);
                return $this->edit($businessinfo->id);
            }
        }
        return view('businessinfo.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('businessinfo.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name'        => 'required|unique:business_info|string|max:255',
            'businessShortName'  => 'nullable|string|max:255',
            'businessDomain'   => 'nullable|string|max:255',
            'businessStartDate'       => 'nullable|date|string|max:20',
            'allowedFiletypes'  => 'nullable|string|max:255',
            'businessAddress1'  => 'nullable|string|max:255',
            'businessAddress2'  => 'nullable|string|max:255',
            'businessCity'  => 'nullable|string|max:255',
            'businessState'  => 'nullable|string|max:255',
            'businessZip'  => 'nullable|string|max:255',
            'businessCountry'      => 'nullable|string|max:255',
            'businessPhone'      => 'nullable|string|max:20',
            'businessEmail'       => 'nullable|email|max:255',
            'businessWebsite'       => 'nullable|string|max:255',
            'businessRegistrationNumber'  => 'nullable|string|max:255',
            'businessRegistrationNumber2'  => 'nullable|string|max:255',
            'businessLogo' => [
                'nullable',
                File::types(['png', 'jpg', 'jpeg']) // Example: allow specific types
                    ->max(100 * 1024), // Example: max 100MB
            ],
            'businessLogoDark' => [
                'nullable',
                File::types(['png', 'jpg', 'jpeg']) // Example: allow specific types
                    ->max(100 * 1024), // Example: max 100MB
            ],
            'businessFavicon' => [
                'nullable',
                File::types(['png', 'jpg', 'jpeg', 'ico']) // Example: allow specific types
                    ->max(100 * 1024), // Example: max 100MB
            ],
            
            
        ]);
    
        //format 
        if(!empty($validated['businessPhone'])){
            $validated['businessPhone'] = simple_format_phone($validated['businessPhone']);
        }
        if(empty($validated['allowedFiletypes'])){
            $validated['allowedFiletypes'] = 'IMG:.jpg,.jpeg,.png DOC:.pdf,.jpg,.jpeg,.png,.doc,.docx';
        }

        DB::beginTransaction();

        try {
            $businessInfo = new BusinessInfo();
            $businessInfo->business_name    = $validated['business_name'];
            $businessInfo->business_short_name    = $validated['businessShortName'] ?? null;
            $businessInfo->business_domain = $validated['businessDomain'] ?? null;
            $businessInfo->business_allowed_file_types      = $validated['allowedFiletypes'] ?? null;
            $businessInfo->business_logo  = $businessLogoPath ?? null;
            $businessInfo->business_logo_dark   = $businessLogoDarkPath ?? null;
            $businessInfo->business_favicon   = $businessFaviconPath ?? null;
            $businessInfo->business_address   = $validated['businessAddress1'] ?? null;
            $businessInfo->business_address2   = $validated['businessAddress2'] ?? null;
            $businessInfo->business_city   = $validated['businessCity'] ?? null;
            $businessInfo->business_state   = $validated['businessState'] ?? null;
            $businessInfo->business_zip   = $validated['businessZip'] ?? null;
            $businessInfo->business_country   = $validated['businessCountry'] ?? null;
            $businessInfo->business_phone   = $validated['businessPhone'] ?? null;
            $businessInfo->business_email   = $validated['businessEmail'] ?? null;
            $businessInfo->business_website   = $validated['businessWebsite'] ?? null;
            $businessInfo->business_registration_number   = $validated['businessRegistrationNumber'] ?? null;
            $businessInfo->business_registration_number2   = $validated['businessRegistrationNumber2'] ?? null;
            $businessInfo->business_start_date   = $validated['businessStartDate'] ?? null;
            $businessInfo->business_status          = 'active';
            $businessInfo->save();
            
            $id = $businessInfo->id;
            $businessinfo = BusinessInfo::findOrFail($id);

            $businessLogoPath = "";
            $businessLogoDarkPath =  "";
            $businessFaviconPath =  "";

            $fileBusinessLogo = $request->file('businessLogo');
            $fileBusinessLogoDark = $request->file('businessLogoDark');
            $fileBusinessFavicon = $request->file('businessFavicon');

            // Store the file
            $disk = env('FILESYSTEM_DISK', 'local');
            // The 'public' disk stores files in storage/app/public and can be publicly accessed via a symbolic link
            if(!empty($fileBusinessLogo)){
                $path = $fileBusinessLogo->store('businessinfo/' . $id, $disk); 
                
                if ($disk === 's3') {
                    Storage::disk('s3')->setVisibility($path, 'public');
                }
                $businessLogoPath = Storage::disk($disk)->url($path);
            }
            if(!empty($fileBusinessLogoDark)){
                $path = $fileBusinessLogoDark->store('businessinfo/' . $id, $disk); 
                if ($disk === 's3') {
                    Storage::disk('s3')->setVisibility($path, 'public');
                }
                $businessLogoDarkPath = Storage::disk($disk)->url($path);
            }
            if(!empty($fileBusinessFavicon)){
                $path = $fileBusinessFavicon->store('businessinfo/' . $id, $disk); 
                if ($disk === 's3') {
                    Storage::disk('s3')->setVisibility($path, 'public');
                }
                $businessFaviconPath = Storage::disk($disk)->url($path);
            }
            
            // You can get original name, extension, size, etc.
            //$originalName = $fileBusinessLogo->getClientOriginalName();


            $businessinfoUpdateData = array();
            if(!empty($businessLogoPath)){
                $businessinfoUpdateData['business_logo'] = $businessLogoPath;
            }
            if(!empty($businessLogoDarkPath)){
                $businessinfoUpdateData['business_logo_dark'] = $businessLogoDarkPath;
            }
            if(!empty($businessFaviconPath)){
                $businessinfoUpdateData['business_favicon'] = $businessFaviconPath;
            }
            $businessinfo->update($businessinfoUpdateData);

            DB::commit();

            return redirect()->route('businessinfo.show', $businessInfo->id)
                ->with('success', 'Business Info created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Business Info store failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the business info. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $businessInfo = BusinessInfo::findOrFail($id);
        
        return view('businessInfo.show')->with([
            'businessinfo' => $businessInfo
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id='')
    {
        $businessinfo = new BusinessInfo();
        if(!empty($id)){
            $businessinfo = BusinessInfo::findOrFail($id);
            // return response()->json($businessinfo);
        }

        $branlogoinfo = array();
        $brandlogofiles = Storage::disk('public')->files('brandlogos/' . $id);
        foreach ($brandlogofiles as $file) {
            $branlogoinfo[] = Storage::disk('public')->url($file);
            //$contents = Storage::disk('public')->get($file);
        }

        return view('businessinfo.edit')->with([
            'businessinfo' => $businessinfo,
            'branlogoinfo' => $branlogoinfo
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //dd($request->all());

        $validated = $request->validate([
            'business_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('business_info', 'business_name')->ignore($id), // Ignores the current business's name
            ],
            'businessShortName'  => 'nullable|string|max:255',
            'businessDomain'   => 'nullable|string|max:255',
            'businessStartDate'       => 'nullable|date|string|max:20',
            'allowedFiletypes'  => 'nullable|string|max:255',
            'businessAddress1'  => 'nullable|string|max:255',
            'businessAddress2'  => 'nullable|string|max:255',
            'businessCity'  => 'nullable|string|max:255',
            'businessState'  => 'nullable|string|max:255',
            'businessZip'  => 'nullable|string|max:255',
            'businessCountry'      => 'nullable|string|max:255',
            'businessPhone'      => 'nullable|string|max:20',
            'businessEmail'       => 'nullable|email|max:255',
            'businessWebsite'       => 'nullable|string|max:255',
            'businessRegistrationNumber'  => 'nullable|string|max:255',
            'businessRegistrationNumber2'  => 'nullable|string|max:255',
            'business_brand_app_name'        => 'required|string|max:255',
            'business_brand_logo_on_top_left'  => 'required',
            'business_brand_credit_on_footer_enable'  => 'required|integer|min:0|max:1',
            'business_brand_credit_on_footer'  => 'required',
            'businessLogo' => [
                'nullable',
                File::types(['png', 'jpg', 'jpeg']) // Example: allow specific types
                    ->max(100 * 1024), // Example: max 100MB
            ],
            'businessLogoDark' => [
                'nullable',
                File::types(['png', 'jpg', 'jpeg']) // Example: allow specific types
                    ->max(100 * 1024), // Example: max 100MB
            ],
            'businessFavicon' => [
                'nullable',
                File::types(['png', 'jpg', 'jpeg', 'ico']) // Example: allow specific types
                    ->max(100 * 1024), // Example: max 100MB
            ],
            'businessStatus' => 'required|string',
            
        ]);

        //format 
        if(!empty($validated['businessPhone'])){
            $validated['businessPhone'] = simple_format_phone($validated['businessPhone']);
        }

        $businessLogoPath = "";
        $businessLogoDarkPath =  "";
        $businessFaviconPath =  "";

        $fileBusinessLogo = $request->file('businessLogo');
        $fileBusinessLogoDark = $request->file('businessLogoDark');
        $fileBusinessFavicon = $request->file('businessFavicon');

        // Store the file
        $disk = env('FILESYSTEM_DISK', 'local');
        // The 'public' disk stores files in storage/app/public and can be publicly accessed via a symbolic link
        if(!empty($fileBusinessLogo)){
            $path = $fileBusinessLogo->store('businessinfo/' . $id, $disk); 
            
            if ($disk === 's3') {
                Storage::disk('s3')->setVisibility($path, 'public');
            }
            $businessLogoPath = Storage::disk($disk)->url($path);
        }
        if(!empty($fileBusinessLogoDark)){
            $path = $fileBusinessLogoDark->store('businessinfo/' . $id, $disk); 
            if ($disk === 's3') {
                Storage::disk('s3')->setVisibility($path, 'public');
            }
            $businessLogoDarkPath = Storage::disk($disk)->url($path);
        }
        if(!empty($fileBusinessFavicon)){
            $path = $fileBusinessFavicon->store('businessinfo/' . $id, $disk); 
            if ($disk === 's3') {
                Storage::disk('s3')->setVisibility($path, 'public');
            }
            $businessFaviconPath = Storage::disk($disk)->url($path);
        }

        if(empty($validated['allowedFiletypes'])){
            $validated['allowedFiletypes'] = 'IMG:.jpg,.jpeg,.png DOC:.pdf,.jpg,.jpeg,.png,.doc,.docx';
        }

        // Update 
        DB::beginTransaction();

        try {
            $businessinfo = BusinessInfo::findOrFail($id);
            $businessinfoUpdateData = [
                'business_name'   => $validated['business_name'],
                'business_short_name' => $validated['businessShortName'] ?? null,
                'business_domain' => $validated['businessDomain'] ?? null,
                'business_allowed_file_types' => $validated['allowedFiletypes'] ?? null,
                'business_address' => $validated['businessAddress1'] ?? null,
                'business_address2' => $validated['businessAddress2'] ?? null,
                'business_city' => $validated['businessCity'] ?? null,
                'business_state' => $validated['businessState'] ?? null,
                'business_zip' => $validated['businessZip'] ?? null,
                'business_country' => $validated['businessCountry'] ?? null,
                'business_phone' => $validated['businessPhone'] ?? null,
                'business_email' => $validated['businessEmail'] ?? null,
                'business_website' => $validated['businessWebsite'] ?? null,
                'business_registration_number'     => $validated['businessRegistrationNumber'] ?? null,
                'business_registration_number2'       => $validated['businessRegistrationNumber2'] ?? null,
                'business_start_date'  => $validated['businessStartDate'] ?? null,
                'business_status' => $validated['businessStatus'],
                'business_brand_app_name'   => $validated['business_brand_app_name'],
                'business_brand_logo_on_top_left' => $validated['business_brand_logo_on_top_left'],
                'business_brand_credit_on_footer_enable' => $validated['business_brand_credit_on_footer_enable'],
                'business_brand_credit_on_footer' => $validated['business_brand_credit_on_footer'],
            ];
            if(!empty($businessLogoPath)){
                $businessinfoUpdateData['business_logo'] = $businessLogoPath;
            }
            if(!empty($businessLogoDarkPath)){
                $businessinfoUpdateData['business_logo_dark'] = $businessLogoDarkPath;
            }
            if(!empty($businessFaviconPath)){
                $businessinfoUpdateData['business_favicon'] = $businessFaviconPath;
            }
            
            $businessinfo->update($businessinfoUpdateData);

            DB::commit();

            return redirect()->back()->with('success', 'Business Info updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Business Info update failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the business info. Please try again.');
        }
    }

    public function uploadBrandFiles(Request $request, string $id){
        if ($request->hasFile('brandFile')) {
            
            if ($request->file('brandFile')->isValid()) {
                //
                $validated = $request->validate([
                    'brandFile' => 'mimes:jpg,jpeg,png|max:102400', //100MB
                ]);

                $disk = env('FILESYSTEM_DISK', 'local');
                // The 'public' disk stores files in storage/app/public and can be publicly accessed via a symbolic link
                $fileBrandFile = $request->file('brandFile');
                $path = $fileBrandFile->store('brandlogos/' . $id, $disk); 
                
                if ($disk === 's3') {
                    Storage::disk('s3')->setVisibility($path, 'public');
                }
                return redirect()->back()->with('success', 'File uploaded.');
            }
            else{
                return redirect()->back()->with('error', 'File is not valid.');
            }
        }else{
            return redirect()->back()->with('error', 'File is not selected.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusinessInfo $businessInfo)
    {
        
    }
}
