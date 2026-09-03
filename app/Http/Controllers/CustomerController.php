<?php

namespace App\Http\Controllers;

use App\Models\CustomerAddress;
use App\Models\CustomerDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::select('*');

            return Datatables::of($data)
            ->filter(function ($query) {
                if (request()->has('search') && request()->input('search.value') != '') {
                    $searchValue = request()->input('search.value');
                    $query->where('company_name', 'like', "%{$searchValue}%")
                        ->orWhere('id', 'like', "%{$searchValue}%")
                        ->orWhere('contact_firstname', 'like', "%{$searchValue}%")
                        ->orWhere('contact_lastname', 'like', "%{$searchValue}%")
                        ->orWhere('contact_phone', 'like', "%{$searchValue}%")
                        ->orWhere('contact_mobile', 'like', "%{$searchValue}%")
                        ->orWhere('contact_email', 'like', "%{$searchValue}%")
                        ->orWhereRaw(
                            "CONCAT(contact_firstname, ' ', contact_lastname) LIKE ?",
                            ["%{$searchValue}%"]
                        );
                }
            }, true)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $showUrl = route('customers.show', $row->id); // Edit Route
                    return '
                    <a href="' . $showUrl . '" class="show btn btn-primary btn-sm">Show</a>
                    <button class="delete btn btn-danger btn-sm" data-id="' . $row->id . '">Delete</button>
                ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('customers.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'companyName'        => 'required|string|regex:/^[a-zA-Z0-9\s&.,\-]+$/|max:255', //|unique:customers,company_name|
            'contactPersonFirstName'  => 'required|string|regex:/^[a-zA-Z\s\'\-]+$/|max:255',
            'contactPersonLastName'   => 'nullable|string|regex:/^[a-zA-Z\s\'\-]+$/|max:255',
            'contactPhone'       => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'contactEmail'       => 'required|email|max:255',
            'contactMobile'      => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
        ]);

        //regex:/^([0-9\s\-\+\(\)]*)$/|min:10

        DB::beginTransaction();

        try {
            $customer = new Customer();
            $customer->company_name    = $validated['companyName'];
            $customer->contact_firstname    = $validated['contactPersonFirstName'];
            $customer->contact_lastname = $validated['contactPersonLastName'] ?? null;
            $customer->contact_phone      = $validated['contactPhone'];
            $customer->contact_mobile  = $validated['contactMobile'] ?? null;
            $customer->contact_email   = $validated['contactEmail'];
            $customer->status          = 'active';
            $customer->save();

            DB::commit();

            return redirect()->route('customers.show', $customer->id)
                ->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Customer store failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the customer. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::with(['billingaddress', 'siteaddress', 'findetails'])->findOrFail($id);
        $useraccess = User::where('email', $customer->contact_email)->first();
        
        return view('customers.show')->with([
            'customer' => $customer,
            'useraccess' => $useraccess
        ]);
    }

    public function showArchived(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::select('*')
            ->whereIn('status', ['inactive']);

            return Datatables::of($data)
            ->filter(function ($query) {
                if (request()->has('search') && request()->input('search.value') != '') {
                    $searchValue = request()->input('search.value');
                    $query->where('company_name', 'like', "%{$searchValue}%")
                        ->orWhere('id', 'like', "%{$searchValue}%")
                        ->orWhere('contact_firstname', 'like', "%{$searchValue}%")
                        ->orWhere('contact_lastname', 'like', "%{$searchValue}%")
                        ->orWhere('contact_phone', 'like', "%{$searchValue}%")
                        ->orWhere('contact_mobile', 'like', "%{$searchValue}%")
                        ->orWhere('contact_email', 'like', "%{$searchValue}%")
                        ->orWhereRaw(
                            "CONCAT(contact_firstname, ' ', contact_lastname) LIKE ?",
                            ["%{$searchValue}%"]
                        );
                }
            }, true)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $showUrl = route('customers.show', $row->id); // Edit Route
                    return '
                    <a href="' . $showUrl . '" class="show btn btn-primary btn-sm">Show</a>
                    <button class="delete btn btn-danger btn-sm" data-id="' . $row->id . '">Delete</button>
                ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('customers.index')->with([
            'archived' => true,
        ]);
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $customer = Customer::with(['billingaddress', 'siteaddress', 'findetails'])->findOrFail($id);
        // return response()->json($customer);

        return view('customers.edit')->with([
            'customer' => $customer
        ]);
    }

    public function update(Request $request, string $id)
    {
        // dd($request->all());
        $validated = $request->validate([
            'contactPersonFirstName'   => 'required|string|max:255',
            'contactPersonLastName'   => 'required|string|max:255',
            'contactPhone'        => 'required|string|max:20',
            'contactEmail'        => 'required|email|max:255',
        ]);

        // Update Customer
        $customer = Customer::findOrFail($id);
        $customer->update([
            'contact_firstname'   => $validated['contactPersonFirstName'],
            'contact_lastname' => $validated['contactPersonLastName'],
            'contact_phone'     => $validated['contactPhone'],
            'contact_mobile'       => $validated['contactMobile'],
            'contact_email'  => $validated['contactEmail'],
        ]);

        // Update addresses
        $this->updateCustomerAddress($request, $id, 'billing');
        $this->updateCustomerAddress($request, $id, 'site');

        // Update customer financial details
        if ($request->filled('assets')) {
            $this->updateCustomerDetails($request, $id);
        }

        return redirect()->back()->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);

        if ($customer) {
            $custenquiry = Enquiry::where('customer_id', $id)->exists(); // Check if Enquiry exists

            if ($custenquiry) {
                // If enquiries exist, update customer status to 'inactive'
                $customer->update(['status' => 'inactive']);

                return response()->json(['message' => 'Customer has active enquiries and is set to inactive.']);
            } else {
                // If no enquiries exist, delete the customer
                $customer->delete();
                CustomerAddress::where('customer_id', $id)->delete();
                CustomerDetails::where('customer_id', $id)->delete();

                return response()->json(['message' => 'Customer deleted successfully.']);
            }
        }

        return response()->json(['message' => 'Customer not found.'], 404);
    }


    private function updateCustomerDetails(Request $request, $customerId)
    {
        $detailsData = [
            'company_name'       => $request->input('companyName', ''),
            'registration_number' => $request->input('registration_no', ''),
            'assets'             => $request->input('assets', 0),
            'net_assets'         => $request->input('netAssets', 0),
            'liabilities'        => $request->input('liabilities', 0),
            'cash_in_bank'       => $request->input('cashInBank', 0),
            'customer_id'        => $customerId,
        ];

        $customerDetails = CustomerDetails::firstOrNew(['customer_id' => $customerId]);
        $customerDetails->fill($detailsData)->save();
    }

    public function updateBasicInfo(Request $request, $id)
    {
        $request->validate([
            'contactPersonFirstName' => 'required|string|max:255',
            'contactPersonLastName'   => 'required|string|max:255',
            'contactEmail' => 'required|email',
            'contactPhone' => 'required_without:contactMobile|max:20',
            'contactMobile' => 'required_without:contactPhone|max:20',
        ]);

        $customer = Customer::findOrFail($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found.'], 404);
        }
        // Update the customer's basic information
        $customer->contact_firstname = $request->input('contactPersonFirstName');
        $customer->contact_lastname = $request->input('contactPersonLastName');
        $customer->contact_email = $request->input('contactEmail');
        $customer->contact_phone = $request->input('contactPhone');
        $customer->contact_mobile = $request->input('contactMobile');
        $customer->status = 'active'; // Ensure the customer is active
        // Save the updated customer
        if (!$customer->save()) {
            return response()->json(['message' => 'Failed to update customer info.'], 500);
        }
       

        return response()->json(['message' => 'Customer info updated successfully.']);
    }

    public function updateFinancialDetails(Request $request, string $id)
    {
        $validated = $request->validate([
            'registration_no' => 'nullable|string|max:255',
            'asset_details_url' => 'nullable|string|max:255',
            'assets' => 'nullable|numeric',
            'netAssets' => 'nullable|numeric',
            'liabilities' => 'nullable|numeric',
            'cashInBank' => 'nullable|numeric',
        ]);

        try {
            $customer = Customer::findOrFail($id);
            if (!$customer) {
                return response()->json(['error'=> 'error', 'message' => 'Customer not found.'], 404);
            }

            $data_array = [
                    'company_name' => $customer->company_name,
                    'registration_number' => $validated['registration_no'],
                ];
            if(array_key_exists('asset_details_url', $validated)){
                $data_array['asset_details_url'] = $validated['asset_details_url'];
            }
            if(array_key_exists('assets', $validated)){
                $data_array['assets'] = $validated['assets'];
            }
            if(array_key_exists('netAssets', $validated)){
                $data_array['net_assets'] = $validated['netAssets'];
            }
            if(array_key_exists('liabilities', $validated)){
                $data_array['liabilities'] = $validated['liabilities'];
            }
            if(array_key_exists('cashInBank', $validated)){
                $data_array['cash_in_bank'] = $validated['cashInBank'];
            }

            CustomerDetails::updateOrCreate(
                ['customer_id' => $id], // Match condition
                $data_array 
            );
            return response()->json(['success'=> 'success', 'message' => 'Customer financial details updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error'=> 'error', 'message' => $e->getMessage()], 404);
        }
    }

    public function getCustomerAddress(Request $request, string $id)
    {
        $address = CustomerAddress::findOrFail($id);
        if ($address) {
            return response()->json($address);
        }

        return response()->json(['message' => 'Customer address not found.']);
    }
    public function storeCustomerAddress(Request $request, string $id)
    {
        // return $request->all();
        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'county' => 'nullable|string|max:255',
            'postcode' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'type' => 'required|string|in:billing,site',
            'customeraddressid' => 'nullable|numeric',
        ]);

        $customeraddressid = $validated['customeraddressid'];
        
        $customerAddress = new CustomerAddress();
        
        if($customeraddressid){
            //edit
            $customerAddress = CustomerAddress::findOrFail($customeraddressid);
            if ($customerAddress) {
                //
            }
            else{
                return redirect()->back()->with('error', 'Customer address not found.');
            }
        }
        $customerAddress->customer_id = $id;
        $customerAddress->contact_firstname = $request->input('contactPersonFirstName');
        $customerAddress->contact_lastname = $request->input('contactPersonLastName');
        $customerAddress->contact_phone = $request->input('contactPhone');
        $customerAddress->contact_mobile = $request->input('contactMobile');
        $customerAddress->contact_email = $request->input('contactEmail');
        $customerAddress->address = $validated['address'];
        $customerAddress->county = $validated['county'] ?? '';
        $customerAddress->country = $validated['country'];
        $customerAddress->postcode = $validated['postcode'];
        $customerAddress->address_type = $validated['type'];
        $customerAddress->save();

        if($customeraddressid){
            return redirect()->back()->with('success', 'Customer address updated successfully.');
        }
        return redirect()->back()->with('success', 'Customer address created successfully.');
    }
    

    public function deleteCustomerAddress($id)
    {
        $address = CustomerAddress::findOrFail($id);
        if ($address) {
            $address->delete();
            //return redirect()->back()->with('success', 'Customer address deleted successfully.'); //TODO this was very harmfull by previous dev, it was deleting Customer also
            return response()->json(['message' => 'Customer address deleted successfully.']);
        }
        //return redirect()->back()->with('error', 'Customer address not found.');
        return response()->json(['message' => 'Customer address not found.']);
    }


    
}
