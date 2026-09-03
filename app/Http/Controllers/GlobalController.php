<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enquiry;
use App\Models\Customer;
use App\Models\EnquiryAddress;
use App\Models\EnquiryImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class GlobalController extends Controller
{
    public function loginAsUser($id='')
    {
        $user = null;
        if(!empty($id)){
            $user = User::findOrFail($id);
        }
        else{
            $roleName = 'super_admin';
            $user = User::whereHas('roles', function ($query) use ($roleName) {
                $query->where('name', $roleName);
            })->orderBy('id', 'asc')->first();  
        }

        if($user){
            Auth::login($user);
        }

        return redirect('/'); // Redirect to the user's dashboard
    }
    public function queryTest(){
        return view('externals.querytest');
    }
    public function query()
    {
        if(isset($_GET['s'])){
            session()->flash('success', 'Your query has been submitted successfully!');
        }
        
        return view('externals.query');
    }

    public function checkPostCode($key)
    {
        $postcode = str_replace(' ', '', $key);
        $postcode = strtoupper($postcode);

        $check = lookup_postcode($postcode);
        return response()->json(['address' => $check]);
    }

    public function checkPostTown($key)
    {
        $posttown = str_replace(' ', '', $key);
        $posttown = strtoupper($posttown);

        $check = lookup_places($posttown);

        $companyName = '';
        if(isset($_GET['a'])){
            $address = $_GET['a'];
            $addressPart = explode(',',$address);
            $firstPart = trim($addressPart[0]);
            if (!preg_match('/\d/', $firstPart)) {
                //$companyName = $firstPart;
            }
            if (
                preg_match('/[A-Za-z]/', $firstPart) &&
                !preg_match('/\b\d+\b/', $firstPart) &&
                !preg_match('/(Unit|Road|Street|Close|Avenue|Lane|Way|Hall|House|Flat)\b/i', $firstPart)
            ) {
                $companyName = $firstPart;
            }
            // if(count($addressPart)==4){
            //     $companyName = $addressPart[0];
            // }
        }

        return response()->json(['address' => $check, 'company_name' => $companyName ]);
    }

    public function storeQuery(Request $request)
    {
        Session::forget('captcha');

        $validated = $request->validate([
            'g-recaptcha-response' => 'required|captcha',
            'firstname'        => 'required|string|max:255',
            'lastname'         => 'nullable|string|max:255',
            'company'          => 'required|string|max:255',
            'phone'  => 'required_without_all:mobile|max:20',
            'mobile' => 'required_without_all:phone|max:20',
            'email'            => 'required|email|max:200',
            'jobdescription'   => 'required',
            'postcode'         => 'required|string|max:20',
            'address'          => 'required|string',
            'county'           => 'nullable|string|max:100',
            'country'          => 'nullable|string|max:100',
            'photo1'           => 'required|image|mimes:jpg,jpeg,png|max:10240',
            'photo2'           => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'photo3'           => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'enquiry_source'   => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Create customer
            $customer = Customer::firstOrCreate(
                [
                    'company_name'   => $validated['company'],
                    'contact_email'  => $validated['email'],
                ],
                [ // Only used if creating a new customer
                    'contact_firstname' => $validated['firstname'],
                    'contact_lastname'  => $validated['lastname'],
                    'contact_phone'     => $validated['phone'],
                    'contact_mobile'    => $validated['mobile'],
                    'status'            => 'active',
                ]
            );

            $customerId = $customer->id;

            // Create enquiry
            $enquiry = Enquiry::create([
                'enquiry_name'        => randomenquiryGenerator(),
                'customer_id'         => $customerId,
                'enquiry' => $validated['jobdescription'],
                'enquiry_type'        => 'query',
                'enquiry_source'      => 'website',
                'enquiry_status'      => 'new',

            ]);

            // Save address
            EnquiryAddress::create([
                'enquiry_id'       => $enquiry->id,
                'contact_firstname' => $validated['firstname'],
                'contact_lastname'  => $validated['lastname'],
                'contact_phone'     => $validated['phone'] ?? '',
                'contact_mobile'    => $validated['mobile'] ?? '',
                'contact_email'     => $validated['email'],
                'address'           => $validated['address'],
                'county'            => $validated['county'],
                'postcode'          => $validated['postcode'],
                'country'           => $validated['country'],
            ]);

            // Handle image upload
            $this->uploadImages($request, $enquiry->id);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Your query has been submitted successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Query Submission Failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while submitting your query.',
            ], 500);
        }
    }
    protected function uploadImages(Request $request, $enquiryid)
    {
        $disk = env('FILESYSTEM_DISK', 'local');

        foreach (['photo1', 'photo2', 'photo3'] as $photoField) {
            if (!$request->hasFile($photoField)) continue;

            $file = $request->file($photoField);
            $originalName = $file->getClientOriginalName();

            if ($disk === 's3') {
                $path = $file->store('uploads', 's3');
                Storage::disk('s3')->setVisibility($path, 'public');
                $url = Storage::disk('s3')->url($path);
            } else {

                $path = $file->store('uploads/enqueries', $disk);

                // $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                // $destination = public_path('uploads/enqueries');

                // if (!file_exists($destination)) {
                //     mkdir($destination, 0755, true);
                // }

                // $file->move($destination, $filename);
                // $path = 'uploads/enqueries/' . $filename;
                // $url = asset($path);

                $url = Storage::disk($disk)->url($path);
            }

            EnquiryImage::create([
                'enquiry_id' => $enquiryid,
                'image_path' => $path,
                'image_name' => $originalName,
                'image_url'  => $url,
                'source'     => $disk,
            ]);
        }
    }
}
