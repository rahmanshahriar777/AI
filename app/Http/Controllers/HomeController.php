<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Fjob;
use App\Models\Lead;
use App\Models\StockItemVariant;
use App\Models\StockWarehouse;
use App\Models\VehicleAssignDetail;
use App\Models\VehicleDetail;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (auth()->user()->hasRole('super_admin|admin')) {
            return $this->adminDashboard();
        } elseif (auth()->user()->hasRole('enquiry-manager')) {
            return $this->enquiryManagerDashboard();
        } elseif (auth()->user()->hasRole('lead-manager')) {
            return $this->leadManagerDashboard();
        } elseif (auth()->user()->hasRole('accounts-manager')) {
            return $this->accountsManagerDashboard();
        } elseif (auth()->user()->hasRole('job-manager')) {
            return $this->jobManagerDashboard();
        } elseif (auth()->user()->hasRole('inventory-manager')) {
            return $this->inventoryManagerDashboard();
        } elseif (auth()->user()->hasRole('fleet-manager')) {
            return $this->fleetManagerDashboard();
        } elseif (auth()->user()->hasRole('health-safety-manager')) {
            return $this->healthSafetyDashboard();
        } elseif (auth()->user()->hasRole('office-manager')) {
            return $this->officerManagerDashboard();
        } elseif (auth()->user()->hasRole('lead-job-manager')) {
            return $this->leadJobManagerDashboard();
        } elseif (auth()->user()->hasRole('staff')) {
            return $this->staffDashboard();
        } else {
            abort(403, 'CAN NOT ACCESS TO ROLE');
        }
    }

    public function adminDashboard()
    {
        return view('dashboards.admin');
    }
    public function enquiryManagerDashboard()
    {
        $newenquiriescount  = Enquiry::where('enquiry_status', 'new')->count();
        $canceledenquiriescount  = Enquiry::where('enquiry_status', 'cancel')->count();
        $totalenquiriescount = Enquiry::count();
        $totalcustomerscount = Enquiry::distinct('customer_id')->count('customer_id');

        $startOfThisWeek = Carbon::now()->startOfWeek(); // Monday
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        // Count for this week
        $thisWeekCount = Enquiry::where('enquiry_status', 'new')
            ->where('created_at', '>=', $startOfThisWeek)
            ->count();

        // Count for last week
        $lastWeekCount = Enquiry::where('enquiry_status', 'new')
            ->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])
            ->count();

        if ($lastWeekCount > 0) {
            $change = (($thisWeekCount - $lastWeekCount) / $lastWeekCount) * 100;
        } else {
            $change = $thisWeekCount > 0 ? 100 : 0; // handle divide by zero
        }

        $percentageChange = round($change, 2); // e.g. 25.00

        if ($percentageChange > 0) {
            $trend = 'increase';
        } elseif ($percentageChange < 0) {
            $trend = 'decrease';
        } else {
            $trend = 'no change';
        }

        $newEnquiries = Enquiry::with(['enquiryaddress', 'customer'])
            ->where('enquiry_status', 'new')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $leadsonprogress = Lead::with(['leadaddress', 'customer'])
            ->where('lead_status', 'inprogress')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboards.enquiry_manager')->with([
            'newEnquiries' => $newEnquiries,
            'this_week' => $thisWeekCount,
            'last_week' => $lastWeekCount,
            'change_percent' => $percentageChange,
            'trend' => $trend,
            'newenquiriescount' => $newenquiriescount,
            'canceledenquiriescount' => $canceledenquiriescount,
            'totalenquiriescount' => $totalenquiriescount,
            'totalcustomerscount' => $totalcustomerscount,
            'leadsonprogress' => $leadsonprogress,
        ]);
    }

    public function leadManagerDashboard()
    {
        $newleadscount  = Lead::where('lead_status', 'new')->count();
        $ongoingleadscount  = Lead::where('lead_status', 'inprogress')->count();
        $cancelledleadscount  = Lead::where('lead_status', 'cancelled')->count();
        $totalcustomerscount = Lead::distinct('customer_id')->count('customer_id');

        $startOfThisWeek = Carbon::now()->startOfWeek(); // Monday
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        // Count for this week
        $thisWeekCount = Lead::where('lead_status', 'new')
            ->where('created_at', '>=', $startOfThisWeek)
            ->count();

        // Count for last week
        $lastWeekCount = Lead::where('lead_status', 'new')
            ->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])
            ->count();

        if ($lastWeekCount > 0) {
            $change = (($thisWeekCount - $lastWeekCount) / $lastWeekCount) * 100;
        } else {
            $change = $thisWeekCount > 0 ? 100 : 0; // handle divide by zero
        }

        $percentageChange = round($change, 2); // e.g. 25.00

        if ($percentageChange > 0) {
            $trend = 'increase';
        } elseif ($percentageChange < 0) {
            $trend = 'decrease';
        } else {
            $trend = 'no change';
        }

        $newLeads = Lead::with(['leadaddress', 'customer'])
            ->where('lead_status', 'new')->orderByDesc('created_at')->limit(5)->get();

        $ongoingLeads = Lead::with(['leadaddress', 'customer'])
            ->where('lead_status', 'inprogress')->orderByDesc('created_at')->limit(5)->get();


        return view('dashboards.lead_manager')->with([
            'newLeads' => $newLeads,
            'this_week' => $thisWeekCount,
            'last_week' => $lastWeekCount,
            'percentageChange' => $percentageChange,
            'trend' => $trend,
            'newleadscount' => $newleadscount,
            'ongoingleadscount' => $ongoingleadscount,
            'cancelledleadscount' => $cancelledleadscount,
            'totalcustomerscount' => $totalcustomerscount,
            'ongoingLeads' => $ongoingLeads,
        ]);
    }

    public function accountsManagerDashboard()
    {
        $newjobscount  = Fjob::where('job_status', 'new')->count();
        $ongoingjobscount  = Fjob::where('job_status', 'inprogress')->count();
        $cancelledjobscount  = Fjob::where('job_status', 'cancelled')->count();
        $totalcustomerscount = Fjob::distinct('customer_id')->count('customer_id');

        $startOfThisWeek = Carbon::now()->startOfWeek(); // Monday
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        // Count for this week
        $thisWeekCount = Fjob::where('job_status', 'new')
            ->where('created_at', '>=', $startOfThisWeek)
            ->count();

        // Count for last week
        $lastWeekCount = Fjob::where('job_status', 'new')
            ->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])
            ->count();

        if ($lastWeekCount > 0) {
            $change = (($thisWeekCount - $lastWeekCount) / $lastWeekCount) * 100;
        } else {
            $change = $thisWeekCount > 0 ? 100 : 0; // handle divide by zero
        }

        $percentageChange = round($change, 2); // e.g. 25.00

        if ($percentageChange > 0) {
            $trend = 'increase';
        } elseif ($percentageChange < 0) {
            $trend = 'decrease';
        } else {
            $trend = 'no change';
        }

        $draftjobs = Fjob::with(['jobaddress', 'customer'])
            ->where('job_status', 'draft')->orderByDesc('created_at')->limit(5)->get();

        $ongoingjobs = Fjob::with(['jobaddress', 'customer'])
            ->where('job_status', 'inprogress')->orderByDesc('created_at')->limit(5)->get();


        return view('dashboards.accounts_manager')->with([
            'draftjobs' => $draftjobs,
            'this_week' => $thisWeekCount,
            'last_week' => $lastWeekCount,
            'percentageChange' => $percentageChange,
            'trend' => $trend,
            'newjobscount' => $newjobscount,
            'ongoingjobscount' => $ongoingjobscount,
            'cancelledjobscount' => $cancelledjobscount,
            'totalcustomerscount' => $totalcustomerscount,
            'ongoingjobs' => $ongoingjobs,
        ]);
    }

    public function jobManagerDashboard()
    {
        $newjobscount  = Fjob::where('job_status', 'new')->count();
        $ongoingjobscount  = Fjob::where('job_status', 'inprogress')->count();
        $cancelledjobscount  = Fjob::where('job_status', 'cancelled')->count();
        $totalcustomerscount = Fjob::distinct('customer_id')->count('customer_id');

        $startOfThisWeek = Carbon::now()->startOfWeek(); // Monday
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        // Count for this week
        $thisWeekCount = Fjob::where('job_status', 'new')
            ->where('created_at', '>=', $startOfThisWeek)
            ->count();

        // Count for last week
        $lastWeekCount = Fjob::where('job_status', 'new')
            ->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])
            ->count();

        if ($lastWeekCount > 0) {
            $change = (($thisWeekCount - $lastWeekCount) / $lastWeekCount) * 100;
        } else {
            $change = $thisWeekCount > 0 ? 100 : 0; // handle divide by zero
        }

        $percentageChange = round($change, 2); // e.g. 25.00

        if ($percentageChange > 0) {
            $trend = 'increase';
        } elseif ($percentageChange < 0) {
            $trend = 'decrease';
        } else {
            $trend = 'no change';
        }

        $draftjobs = Fjob::with(['jobaddress', 'customer'])
            ->whereIn('job_status', ['accepted', 'new'])->orderByDesc('created_at')->limit(5)->get();

        $ongoingjobs = Fjob::with(['jobaddress', 'customer'])
            ->where('job_status', 'ongoing')->orderByDesc('created_at')->limit(5)->get();


        return view('dashboards.job_manager')->with([
            'draftjobs' => $draftjobs,
            'this_week' => $thisWeekCount,
            'last_week' => $lastWeekCount,
            'percentageChange' => $percentageChange,
            'trend' => $trend,
            'newjobscount' => $newjobscount,
            'ongoingjobscount' => $ongoingjobscount,
            'cancelledjobscount' => $cancelledjobscount,
            'totalcustomerscount' => $totalcustomerscount,
            'ongoingjobs' => $ongoingjobs,
        ]);
    }

    public function inventoryManagerDashboard()
    {
        $totalWarehouse  = StockWarehouse::where('is_active', true)->count();
        $typeOfProducts  = StockItemVariant::where('is_active', true)->count();
        $cancelledjobscount  = Fjob::where('job_status', 'cancelled')->count();
        $totalcustomerscount = Fjob::distinct('customer_id')->count('customer_id');

        $startOfThisWeek = Carbon::now()->startOfWeek(); // Monday
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        // Count for this week
        $thisWeekCount = Fjob::where('job_status', 'new')
            ->where('created_at', '>=', $startOfThisWeek)
            ->count();

        // Count for last week
        $lastWeekCount = Fjob::where('job_status', 'new')
            ->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])
            ->count();

        if ($lastWeekCount > 0) {
            $change = (($thisWeekCount - $lastWeekCount) / $lastWeekCount) * 100;
        } else {
            $change = $thisWeekCount > 0 ? 100 : 0; // handle divide by zero
        }

        $percentageChange = round($change, 2); // e.g. 25.00

        if ($percentageChange > 0) {
            $trend = 'increase';
        } elseif ($percentageChange < 0) {
            $trend = 'decrease';
        } else {
            $trend = 'no change';
        }

        $draftjobs = Fjob::with(['jobaddress', 'customer'])
            ->whereIn('job_status', ['accepted', 'new'])->orderByDesc('created_at')->limit(5)->get();

        $ongoingjobs = Fjob::with(['jobaddress', 'customer'])
            ->where('job_status', 'ongoing')->orderByDesc('created_at')->limit(5)->get();


        return view('dashboards.inventory_manager')->with([
            'draftjobs' => $draftjobs,
            'this_week' => $thisWeekCount,
            'last_week' => $lastWeekCount,
            'percentageChange' => $percentageChange,
            'trend' => $trend,
            'totalWarehouse' => $totalWarehouse,
            'typeOfProducts' => $typeOfProducts,
            'cancelledjobscount' => $cancelledjobscount,
            'totalcustomerscount' => $totalcustomerscount,
            'ongoingjobs' => $ongoingjobs,
        ]);
    }

    public function fleetManagerDashboard()
    {
        $totalVehicles = VehicleDetail::count();
        $assignedVehicles = VehicleDetail::where('status', 'Assigned')->count();
        $underMaintenanceVehicles = VehicleDetail::where('status', 'Under Maintenance')->count();
        $availableVehicles = VehicleDetail::where('status', 'Available')->count();
        $inactiveVehicles = VehicleDetail::where('status', 'Inactive')->count();

        $assignedVehiclesList = VehicleAssignDetail::with(['vehicle', 'driver'])->where('status', 'active')->limit(10)->get();


        return view('dashboards.fleet_manager')->with([
            'totalVehicles' => $totalVehicles,
            'assignedVehicles' => $assignedVehicles,
            'underMaintenanceVehicles' => $underMaintenanceVehicles,
            'availableVehicles' => $availableVehicles,
            'inactiveVehicles' => $inactiveVehicles,
            'assignedVehiclesList' => $assignedVehiclesList
        ]);
    }

    public function officerManagerDashboard()
    {
        $newjobscount  = Fjob::where('job_status', 'new')->count();
        $ongoingjobscount  = Fjob::where('job_status', 'inprogress')->count();
        $cancelledjobscount  = Fjob::where('job_status', 'cancelled')->count();
        $totalcustomerscount = Fjob::distinct('customer_id')->count('customer_id');

        $startOfThisWeek = Carbon::now()->startOfWeek(); // Monday
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        // Count for this week
        $thisWeekCount = Fjob::where('job_status', 'new')
            ->where('created_at', '>=', $startOfThisWeek)
            ->count();

        // Count for last week
        $lastWeekCount = Fjob::where('job_status', 'new')
            ->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])
            ->count();

        if ($lastWeekCount > 0) {
            $change = (($thisWeekCount - $lastWeekCount) / $lastWeekCount) * 100;
        } else {
            $change = $thisWeekCount > 0 ? 100 : 0; // handle divide by zero
        }

        $percentageChange = round($change, 2); // e.g. 25.00

        if ($percentageChange > 0) {
            $trend = 'increase';
        } elseif ($percentageChange < 0) {
            $trend = 'decrease';
        } else {
            $trend = 'no change';
        }

        $draftjobs = Fjob::with(['jobaddress', 'customer'])
            ->whereIn('job_status', ['accepted', 'new'])->orderByDesc('created_at')->limit(5)->get();

        $ongoingjobs = Fjob::with(['jobaddress', 'customer'])
            ->where('job_status', 'ongoing')->orderByDesc('created_at')->limit(5)->get();


        return view('dashboards.office_manager')->with([
            'draftjobs' => $draftjobs,
            'this_week' => $thisWeekCount,
            'last_week' => $lastWeekCount,
            'percentageChange' => $percentageChange,
            'trend' => $trend,
            'newjobscount' => $newjobscount,
            'ongoingjobscount' => $ongoingjobscount,
            'cancelledjobscount' => $cancelledjobscount,
            'totalcustomerscount' => $totalcustomerscount,
            'ongoingjobs' => $ongoingjobs,
        ]);
    }

    public function leadJobManagerDashboard()
    {
        $newjobscount  = Fjob::where('job_status', 'new')->count();
        $ongoingjobscount  = Fjob::where('job_status', 'inprogress')->count();
        $cancelledjobscount  = Fjob::where('job_status', 'cancelled')->count();
        $totalcustomerscount = Fjob::distinct('customer_id')->count('customer_id');

        $startOfThisWeek = Carbon::now()->startOfWeek(); // Monday
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        // Count for this week
        $thisWeekCount = Fjob::where('job_status', 'new')
            ->where('created_at', '>=', $startOfThisWeek)
            ->count();

        // Count for last week
        $lastWeekCount = Fjob::where('job_status', 'new')
            ->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])
            ->count();

        if ($lastWeekCount > 0) {
            $change = (($thisWeekCount - $lastWeekCount) / $lastWeekCount) * 100;
        } else {
            $change = $thisWeekCount > 0 ? 100 : 0; // handle divide by zero
        }

        $percentageChange = round($change, 2); // e.g. 25.00

        if ($percentageChange > 0) {
            $trend = 'increase';
        } elseif ($percentageChange < 0) {
            $trend = 'decrease';
        } else {
            $trend = 'no change';
        }

        $draftjobs = Fjob::with(['jobaddress', 'customer'])
            ->whereIn('job_status', ['accepted', 'new'])->orderByDesc('created_at')->limit(5)->get();

        $ongoingjobs = Fjob::with(['jobaddress', 'customer'])
            ->where('job_status', 'ongoing')->orderByDesc('created_at')->limit(5)->get();


        return view('dashboards.lead_job_manager')->with([
            'draftjobs' => $draftjobs,
            'this_week' => $thisWeekCount,
            'last_week' => $lastWeekCount,
            'percentageChange' => $percentageChange,
            'trend' => $trend,
            'newjobscount' => $newjobscount,
            'ongoingjobscount' => $ongoingjobscount,
            'cancelledjobscount' => $cancelledjobscount,
            'totalcustomerscount' => $totalcustomerscount,
            'ongoingjobs' => $ongoingjobs,
        ]);
    }

    public function staffDashboard()
    {
        $activetasks = Fjob::where('job_status', 'inprogress')->get();
        return view('dashboards.staffs')->with([
            'activetasks' => $activetasks,
        ]);
    }

    public function healthSafetyDashboard()
    {
        return view('dashboards.health_safety_manager');
    }
}
