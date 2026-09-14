<?php

namespace Database\Seeders;

use App\Models\AiUsageLog;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerDetails;
use App\Models\DriverDetail;
use App\Models\Enquiry;
use App\Models\EnquiryAddress;
use App\Models\EnquiryNote;
use App\Models\Fjob;
use App\Models\FjobAddress;
use App\Models\FjobNote;
use App\Models\JobType;
use App\Models\Lead;
use App\Models\LeadAddress;
use App\Models\LeadNote;
use App\Models\Quotation;
use App\Models\StockCategory;
use App\Models\StockItem;
use App\Models\StockItemVariant;
use App\Models\StockWarehouse;
use App\Models\User;
use App\Models\VehicleCategory;
use App\Models\VehicleDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AiTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::first();
        $adminUserId = $adminUser ? $adminUser->id : 1;

        // -------------------------------------------------------------
        // 1. JOB TYPES
        // -------------------------------------------------------------
        $jobTypes = [
            [
                'name' => 'Commercial HVAC Installation & Servicing',
                'slug' => 'commercial-hvac',
                'description' => 'Industrial air handling, VRF systems, chillers, and ductwork installations.',
                'status' => 'active',
            ],
            [
                'name' => 'Electrical Inspection & Safety Testing (EICR)',
                'slug' => 'electrical-inspection',
                'description' => 'Periodic commercial electrical condition reports, thermal imaging, and remedial rewiring.',
                'status' => 'active',
            ],
            [
                'name' => 'Commercial Plumbing & Pipework Maintenance',
                'slug' => 'commercial-plumbing',
                'description' => 'Pressurized water mains, booster sets, heating loops, and commercial washrooms.',
                'status' => 'active',
            ],
            [
                'name' => 'Solar PV & Energy Storage EPC',
                'slug' => 'solar-pv-storage',
                'description' => 'Turnkey commercial rooftop solar photovoltaic arrays and battery energy storage systems.',
                'status' => 'active',
            ],
            [
                'name' => 'Fire Alarm & Life Safety Systems',
                'slug' => 'fire-life-safety',
                'description' => 'BS 5839 compliant addressable fire alarm systems, emergency lighting, and dampers.',
                'status' => 'active',
            ],
        ];

        $jobTypeModels = [];
        foreach ($jobTypes as $jt) {
            $jobTypeModels[$jt['slug']] = JobType::firstOrCreate(
                ['slug' => $jt['slug']],
                $jt
            );
        }

        // -------------------------------------------------------------
        // 2. QUOTATION TEMPLATES
        // -------------------------------------------------------------
        $hvacJobType = $jobTypeModels['commercial-hvac'];
        $solarJobType = $jobTypeModels['solar-pv-storage'];

        $tmpl1 = DB::table('quotation_templates')->updateOrInsert(
            ['template_slug' => 'standard-commercial-hvac-v1'],
            [
                'job_type_id' => $hvacJobType->id,
                'job_type_name' => $hvacJobType->name,
                'template_name' => 'Commercial HVAC Equipment Replacement & Commissioning',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $tmpl2 = DB::table('quotation_templates')->updateOrInsert(
            ['template_slug' => 'solar-pv-commercial-turnkey-v1'],
            [
                'job_type_id' => $solarJobType->id,
                'job_type_name' => $solarJobType->name,
                'template_name' => 'Turnkey Commercial Rooftop Solar PV & Battery Proposal',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $templateId1 = DB::table('quotation_templates')->where('template_slug', 'standard-commercial-hvac-v1')->value('id') ?? 1;
        $templateId2 = DB::table('quotation_templates')->where('template_slug', 'solar-pv-commercial-turnkey-v1')->value('id') ?? 2;

        // -------------------------------------------------------------
        // 3. CUSTOMERS
        // -------------------------------------------------------------
        $customersData = [
            [
                'company_name' => 'Apex Industrial Solutions Ltd',
                'contact_firstname' => 'Richard',
                'contact_lastname' => 'Vance',
                'contact_phone' => '0121 496 0192',
                'contact_mobile' => '07700 900210',
                'contact_email' => 'r.vance@apexindustrial.co.uk',
                'status' => 'active',
                'details' => [
                    'registration_number' => '08492019',
                    'assets' => 1250000.00,
                    'net_assets' => 850000.00,
                    'liabilities' => 400000.00,
                    'cash_in_bank' => 210000.00,
                ],
                'billing_address' => [
                    'address' => 'Unit 4, Great Western Industrial Park, Bromford Lane',
                    'county' => 'West Midlands',
                    'postcode' => 'B24 8DX',
                    'country' => 'United Kingdom',
                    'location' => 'Birmingham',
                ],
                'site_address' => [
                    'address' => 'Apex Manufacturing Plant 2, Tyseley Depot, Wharf Road',
                    'county' => 'West Midlands',
                    'postcode' => 'B11 2FE',
                    'country' => 'United Kingdom',
                    'location' => 'Birmingham',
                ],
            ],
            [
                'company_name' => 'Horizon Luxury Hotels Group',
                'contact_firstname' => 'Eleanor',
                'contact_lastname' => 'Thorne',
                'contact_phone' => '020 7946 0881',
                'contact_mobile' => '07700 900445',
                'contact_email' => 'e.thorne@horizonhotels.co.uk',
                'status' => 'active',
                'details' => [
                    'registration_number' => '06291840',
                    'assets' => 4500000.00,
                    'net_assets' => 3100000.00,
                    'liabilities' => 1400000.00,
                    'cash_in_bank' => 620000.00,
                ],
                'billing_address' => [
                    'address' => 'Horizon House, 45 Berkeley Square, Mayfair',
                    'county' => 'Greater London',
                    'postcode' => 'W1J 5AS',
                    'country' => 'United Kingdom',
                    'location' => 'London',
                ],
                'site_address' => [
                    'address' => 'The Grand Horizon Hotel, 12-18 Victoria Embankment',
                    'county' => 'Greater London',
                    'postcode' => 'WC2N 6PB',
                    'country' => 'United Kingdom',
                    'location' => 'London',
                ],
            ],
            [
                'company_name' => 'Meridian Logistics & Warehousing UK',
                'contact_firstname' => 'Marcus',
                'contact_lastname' => 'Bradley',
                'contact_phone' => '0161 834 9901',
                'contact_mobile' => '07700 900891',
                'contact_email' => 'mbradley@meridianlogistics.co.uk',
                'status' => 'active',
                'details' => [
                    'registration_number' => '09102481',
                    'assets' => 2800000.00,
                    'net_assets' => 1950000.00,
                    'liabilities' => 850000.00,
                    'cash_in_bank' => 480000.00,
                ],
                'billing_address' => [
                    'address' => 'Trafford Gateway Building, Tenax Road, Trafford Park',
                    'county' => 'Greater Manchester',
                    'postcode' => 'M17 1JT',
                    'country' => 'United Kingdom',
                    'location' => 'Manchester',
                ],
                'site_address' => [
                    'address' => 'Meridian Central Distribution Depot, Logistics Way',
                    'county' => 'Greater Manchester',
                    'postcode' => 'M17 1WA',
                    'country' => 'United Kingdom',
                    'location' => 'Manchester',
                ],
            ],
            [
                'company_name' => 'Vantage Facilities Management Ltd',
                'contact_firstname' => 'Sarah',
                'contact_lastname' => 'Jenkins',
                'contact_phone' => '0113 496 0332',
                'contact_mobile' => '07700 900712',
                'contact_email' => 'sjenkins@vantagefm.co.uk',
                'status' => 'active',
                'details' => [
                    'registration_number' => '11029482',
                    'assets' => 980000.00,
                    'net_assets' => 640000.00,
                    'liabilities' => 340000.00,
                    'cash_in_bank' => 150000.00,
                ],
                'billing_address' => [
                    'address' => 'Riverside Corporate Centre, Sovereign Street',
                    'county' => 'West Yorkshire',
                    'postcode' => 'LS1 4DA',
                    'country' => 'United Kingdom',
                    'location' => 'Leeds',
                ],
                'site_address' => [
                    'address' => 'Leeds Tech Hub Campus, Wellington Place',
                    'county' => 'West Yorkshire',
                    'postcode' => 'LS1 4AP',
                    'country' => 'United Kingdom',
                    'location' => 'Leeds',
                ],
            ],
            [
                'company_name' => 'BlueSky Renewable Energy Ltd',
                'contact_firstname' => 'Liam',
                'contact_lastname' => 'Gallagher',
                'contact_phone' => '0117 496 0221',
                'contact_mobile' => '07700 900334',
                'contact_email' => 'l.gallagher@blueskyenergy.co.uk',
                'status' => 'active',
                'details' => [
                    'registration_number' => '12093845',
                    'assets' => 1800000.00,
                    'net_assets' => 1200000.00,
                    'liabilities' => 600000.00,
                    'cash_in_bank' => 310000.00,
                ],
                'billing_address' => [
                    'address' => 'Innovation Centre, Temple Way',
                    'county' => 'Bristol',
                    'postcode' => 'BS2 0PT',
                    'country' => 'United Kingdom',
                    'location' => 'Bristol',
                ],
                'site_address' => [
                    'address' => 'Avonmouth CleanTech Solar Park, St Andrews Road',
                    'county' => 'Bristol',
                    'postcode' => 'BS11 9HS',
                    'country' => 'United Kingdom',
                    'location' => 'Bristol',
                ],
            ],
            [
                'company_name' => 'Sterling Property Estates Group',
                'contact_firstname' => 'Fiona',
                'contact_lastname' => 'MacLeod',
                'contact_phone' => '0131 496 0990',
                'contact_mobile' => '07700 900650',
                'contact_email' => 'fmacleod@sterlingestates.co.uk',
                'status' => 'active',
                'details' => [
                    'registration_number' => 'SC482910',
                    'assets' => 3200000.00,
                    'net_assets' => 2400000.00,
                    'liabilities' => 800000.00,
                    'cash_in_bank' => 420000.00,
                ],
                'billing_address' => [
                    'address' => 'St Andrew Square Commercial Chambers',
                    'county' => 'City of Edinburgh',
                    'postcode' => 'EH2 2AF',
                    'country' => 'United Kingdom',
                    'location' => 'Edinburgh',
                ],
                'site_address' => [
                    'address' => 'Princes Street Commercial Arcade, 90-95 Princes St',
                    'county' => 'City of Edinburgh',
                    'postcode' => 'EH2 2ER',
                    'country' => 'United Kingdom',
                    'location' => 'Edinburgh',
                ],
            ],
        ];

        $customerModels = [];
        foreach ($customersData as $cData) {
            $customer = Customer::firstOrCreate(
                ['company_name' => $cData['company_name']],
                [
                    'contact_firstname' => $cData['contact_firstname'],
                    'contact_lastname' => $cData['contact_lastname'],
                    'contact_phone' => $cData['contact_phone'],
                    'contact_mobile' => $cData['contact_mobile'],
                    'contact_email' => $cData['contact_email'],
                    'status' => $cData['status'],
                ]
            );

            // Customer Details (Financials)
            CustomerDetails::updateOrCreate(
                ['customer_id' => $customer->id],
                array_merge($cData['details'], ['company_name' => $cData['company_name']])
            );

            // Billing Address
            CustomerAddress::updateOrCreate(
                ['customer_id' => $customer->id, 'address_type' => 'billing'],
                array_merge($cData['billing_address'], [
                    'contact_firstname' => $cData['contact_firstname'],
                    'contact_lastname' => $cData['contact_lastname'],
                    'contact_phone' => $cData['contact_phone'],
                    'contact_mobile' => $cData['contact_mobile'],
                    'contact_email' => $cData['contact_email'],
                    'default' => 'yes',
                    'status' => 'active',
                ])
            );

            // Site Address
            CustomerAddress::updateOrCreate(
                ['customer_id' => $customer->id, 'address_type' => 'site'],
                array_merge($cData['site_address'], [
                    'contact_firstname' => $cData['contact_firstname'],
                    'contact_lastname' => $cData['contact_lastname'],
                    'contact_phone' => $cData['contact_phone'],
                    'contact_mobile' => $cData['contact_mobile'],
                    'contact_email' => $cData['contact_email'],
                    'default' => 'no',
                    'status' => 'active',
                ])
            );

            $customerModels[$customer->company_name] = $customer;
        }

        // -------------------------------------------------------------
        // 4. ENQUIRIES
        // -------------------------------------------------------------
        $enquiriesData = [
            [
                'enquiry_name' => 'ENQ-2026-0001',
                'customer' => 'Apex Industrial Solutions Ltd',
                'enquiry' => 'Emergency Server Room Chiller Unit Failure - Critical Temperature Alarm',
                'enquiry_description' => "Chiller Unit 1 in server room 2 has experienced catastrophic compressor coil breakdown. Room temperature is currently at 27.5°C and climbing. Needs emergency diagnostic site survey, temporary spot-cooler deployment, and formal quotation for complete replacement with energy-efficient 45kW inverter package.",
                'enquiry_type' => 'technical',
                'enquiry_category' => 'request',
                'enquiry_priority' => 'high',
                'enquiry_source' => 'phone',
                'enquiry_status' => 'inprogress',
                'notes' => [
                    [
                        'note' => 'Client called emergency dispatch at 07:15. Senior HVAC engineer dispatched to site. Temporary portable 7kW spot-chillers installed as interim safeguard.',
                        'note_by' => 'staff',
                        'note_type' => 'internal',
                    ],
                    [
                        'note' => 'Primary refrigerant loop has lost 8kg R410A due to fractured brazing joint. Compressor motor burned out. Complete 45kW Daikin package replacement recommended.',
                        'note_by' => 'staff',
                        'note_type' => 'general',
                    ],
                ],
            ],
            [
                'enquiry_name' => 'ENQ-2026-0002',
                'customer' => 'Horizon Luxury Hotels Group',
                'enquiry' => 'Annual Commercial Fire Alarm & Emergency Lighting BS 5839 / BS 5266 Audit',
                'enquiry_description' => "Comprehensive annual fire life safety inspection across all 120 guest suites, ballroom, and 3 restaurants. Requires full 3-hour emergency lighting discharge test, 240 optical smoke detector testing, sounder pressure measurement (75dB minimum at bedheads), and updated logbook sign-off for local council licensing authority.",
                'enquiry_type' => 'sales',
                'enquiry_category' => 'query',
                'enquiry_priority' => 'medium',
                'enquiry_source' => 'email',
                'enquiry_status' => 'new',
                'notes' => [
                    [
                        'note' => 'Eleanor Thorne requested audit to be completed during low-occupancy window between October 15-20. Testing must be quiet during 09:00 - 11:30 to avoid disturbing hotel VIP guests.',
                        'note_by' => 'staff',
                        'note_type' => 'external',
                    ],
                ],
            ],
            [
                'enquiry_name' => 'ENQ-2026-0003',
                'customer' => 'Meridian Logistics & Warehousing UK',
                'enquiry' => '150kW Rooftop Solar PV & 80kWh Commercial Battery Storage Feasibility',
                'enquiry_description' => "Requesting turnkey feasibility study, structural engineering assessment, and formal proposal for a 150kW grid-tied rooftop solar PV system combined with an 80kWh battery energy storage system (BESS) to offset peak tariff electricity consumption for electric forklift fleet charging.",
                'enquiry_type' => 'sales',
                'enquiry_category' => 'request',
                'enquiry_priority' => 'high',
                'enquiry_source' => 'email',
                'enquiry_status' => 'inprogress',
                'notes' => [
                    [
                        'note' => 'Met Marcus Bradley on site. Roof is trapezoidal profile sheet with unobstructed southern exposure. Structural drawings reviewed and load capacity verified for up to 14kg/m2.',
                        'note_by' => 'staff',
                        'note_type' => 'internal',
                    ],
                    [
                        'note' => 'G99 grid connection pre-application submitted to Electricity North West (ENWL). Initial feedback indicates no export constraint up to 100kW.',
                        'note_by' => 'staff',
                        'note_type' => 'general',
                    ],
                ],
            ],
            [
                'enquiry_name' => 'ENQ-2026-0004',
                'customer' => 'Vantage Facilities Management Ltd',
                'enquiry' => '11kV High Voltage Substation Annual Maintenance & Thermal Survey',
                'enquiry_description' => "Preventative maintenance and non-invasive infrared thermography scanning on two 11kV/415V 1500kVA step-down distribution transformers and main switchboard incomers across Leeds business park.",
                'enquiry_type' => 'technical',
                'enquiry_category' => 'request',
                'enquiry_priority' => 'low',
                'enquiry_source' => 'chat',
                'enquiry_status' => 'converted_to_lead',
                'notes' => [
                    [
                        'note' => 'Thermographic scanning identified slight thermal rise (+14°C above ambient) on busbar connection L2 on Switchboard B. Converted to Lead for detailed repair scheduling.',
                        'note_by' => 'staff',
                        'note_type' => 'internal',
                    ],
                ],
            ],
            [
                'enquiry_name' => 'ENQ-2026-0005',
                'customer' => 'Horizon Luxury Hotels Group',
                'enquiry' => 'Commercial Kitchen TR19 Ventilation Duct Deep Clean & Hygiene Certification',
                'enquiry_description' => "Insurance-mandated TR19 grease extraction duct cleaning for central commercial kitchen and pastry bakery. Includes canopy filter degreasing, riser duct access door installation, and grease deposit thickness gauge testing before and after clean.",
                'enquiry_type' => 'general',
                'enquiry_category' => 'request',
                'enquiry_priority' => 'medium',
                'enquiry_source' => 'phone',
                'enquiry_status' => 'new',
                'notes' => [
                    [
                        'note' => 'Needs to be conducted overnight between 23:00 and 06:00 when kitchen service is completely shut down.',
                        'note_by' => 'staff',
                        'note_type' => 'general',
                    ],
                ],
            ],
        ];

        $enquiryModels = [];
        foreach ($enquiriesData as $eData) {
            $customer = $customerModels[$eData['customer']];
            $enquiry = Enquiry::firstOrCreate(
                ['enquiry_name' => $eData['enquiry_name']],
                [
                    'customer_id' => $customer->id,
                    'enquiry' => $eData['enquiry'],
                    'enquiry_description' => $eData['enquiry_description'],
                    'enquiry_type' => $eData['enquiry_type'],
                    'enquiry_category' => $eData['enquiry_category'],
                    'enquiry_priority' => $eData['enquiry_priority'],
                    'enquiry_source' => $eData['enquiry_source'],
                    'enquiry_status' => $eData['enquiry_status'],
                    'annual_maintenance' => 0,
                    'installations' => 1,
                    'repairs' => 1,
                    'testing' => 1,
                ]
            );

            // Enquiry Address
            EnquiryAddress::updateOrCreate(
                ['enquiry_id' => $enquiry->id],
                [
                    'contact_firstname' => $customer->contact_firstname,
                    'contact_lastname' => $customer->contact_lastname,
                    'contact_phone' => $customer->contact_phone,
                    'contact_mobile' => $customer->contact_mobile,
                    'contact_email' => $customer->contact_email,
                    'address' => $customer->billingaddress->address ?? 'Commercial Site Address',
                    'county' => $customer->billingaddress->county ?? 'England',
                    'postcode' => $customer->billingaddress->postcode ?? 'B1 1AA',
                    'country' => 'United Kingdom',
                    'location' => $customer->billingaddress->location ?? 'Site Location',
                    'status' => 'active',
                    'default' => 'yes',
                ]
            );

            // Notes
            foreach ($eData['notes'] as $note) {
                EnquiryNote::firstOrCreate(
                    [
                        'enquiry_id' => $enquiry->id,
                        'note' => $note['note'],
                    ],
                    [
                        'note_by' => $note['note_by'],
                        'note_type' => $note['note_type'],
                        'note_status' => 'active',
                    ]
                );
            }

            $enquiryModels[$eData['enquiry_name']] = $enquiry;
        }

        // -------------------------------------------------------------
        // 5. LEADS
        // -------------------------------------------------------------
        $leadsData = [
            [
                'lead_name' => 'LEAD-2026-0001',
                'enquiry_name' => 'ENQ-2026-0001',
                'customer' => 'Apex Industrial Solutions Ltd',
                'lead_title' => 'Apex Server Room 45kW Chiller Package & Copper Piping Overhaul',
                'lead_description' => 'Complete replacement of failed cooling unit with high-efficiency Daikin inverter chiller, crane lift onto plant roof, reinforced acoustic mounting, and insulated brazed copper manifold installation.',
                'lead_type' => 'sales',
                'lead_category' => 'request',
                'lead_priority' => 'high',
                'lead_status' => 'inprogress',
                'notes' => [
                    [
                        'note' => 'Facility Director Richard Vance confirmed capital budget of up to £48,000 is approved. Client stresses urgency to prevent server shutdowns.',
                        'note_by' => 'staff',
                    ],
                    [
                        'note' => 'Site survey completed. Crane permit required from Birmingham City Council for Saturday road closure outside Wharf Road entrance.',
                        'note_by' => 'staff',
                    ],
                    [
                        'note' => 'Competitor Carrier bid estimated at £52,000 with 4-week lead time. Our proposed turnaround is 10 days with Daikin stock in depot.',
                        'note_by' => 'staff',
                    ],
                    [
                        'note' => 'Action required: Finalize quotation Q-2026-0001 and deliver formal scope document for director sign-off.',
                        'note_by' => 'staff',
                    ],
                ],
            ],
            [
                'lead_name' => 'LEAD-2026-0002',
                'enquiry_name' => 'ENQ-2026-0003',
                'customer' => 'Meridian Logistics & Warehousing UK',
                'lead_title' => 'Meridian Logistics 150kW Solar PV Array & 80kWh Battery Storage EPC',
                'lead_description' => 'Turnkey commercial rooftop solar EPC project featuring 380 Tier-1 monocrystalline panels, Sungrow 150kW commercial string inverter, 80kWh lithium iron phosphate battery system, and cloud telemetry.',
                'lead_type' => 'sales',
                'lead_category' => 'request',
                'lead_priority' => 'high',
                'lead_status' => 'inprogress',
                'notes' => [
                    [
                        'note' => 'Energy yield modeling completed using PVsyst software. Projected annual generation: 142,000 kWh, delivering £34,000/year electricity savings.',
                        'note_by' => 'staff',
                    ],
                    [
                        'note' => 'Financial analysis shows simple payback period of 4.2 years with 22% internal rate of return (IRR).',
                        'note_by' => 'staff',
                    ],
                    [
                        'note' => 'Next milestone: Present executive summary to Meridian Board of Directors on Thursday 14:00.',
                        'note_by' => 'staff',
                    ],
                ],
            ],
            [
                'lead_name' => 'LEAD-2026-0003',
                'enquiry_name' => 'ENQ-2026-0004',
                'customer' => 'Vantage Facilities Management Ltd',
                'lead_title' => 'Vantage FM High Voltage Substation 3-Year Preventative Service Contract',
                'lead_description' => '3-year planned preventative maintenance agreement covering four 11kV transformers, SF6 circuit breakers, thermal imaging surveys, and 24/7 priority emergency breakdown cover across Leeds portfolio.',
                'lead_type' => 'sales',
                'lead_category' => 'request',
                'lead_priority' => 'medium',
                'lead_status' => 'new',
                'notes' => [
                    [
                        'note' => 'Sarah Jenkins submitted procurement tender documents. Submission deadline is end of current month.',
                        'note_by' => 'staff',
                    ],
                    [
                        'note' => 'Draft contract schedule prepared based on £18,500 annual recurring fee.',
                        'note_by' => 'staff',
                    ],
                ],
            ],
            [
                'lead_name' => 'LEAD-2026-0004',
                'enquiry_name' => 'ENQ-2026-0005',
                'customer' => 'BlueSky Renewable Energy Ltd',
                'lead_title' => 'BlueSky Commercial EV Fleet Hub (12 x 22kW Dual AC Fast Chargers)',
                'lead_description' => 'Design, civil trenching, 400A feeder pillar installation, and commissioning of 12 intelligent EV charging points with dynamic load management and RFID contactless payment terminal.',
                'lead_type' => 'sales',
                'lead_category' => 'request',
                'lead_priority' => 'low',
                'lead_status' => 'on_hold',
                'notes' => [
                    [
                        'note' => 'Client is awaiting Western Power Distribution (WPD) substations connection upgrade confirmation before placing formal contract.',
                        'note_by' => 'staff',
                    ],
                ],
            ],
        ];

        $leadModels = [];
        foreach ($leadsData as $lData) {
            $customer = $customerModels[$lData['customer']];
            $enquiry = $enquiryModels[$lData['enquiry_name']];

            $lead = Lead::firstOrCreate(
                ['lead_name' => $lData['lead_name']],
                [
                    'enquiry_id' => $enquiry->id,
                    'enquiry' => $enquiry->enquiry,
                    'lead_title' => $lData['lead_title'],
                    'lead_description' => $lData['lead_description'],
                    'customer_id' => $customer->id,
                    'lead_type' => $lData['lead_type'],
                    'lead_category' => $lData['lead_category'],
                    'lead_priority' => $lData['lead_priority'],
                    'lead_status' => $lData['lead_status'],
                    'assigned_to' => $adminUserId,
                    'annual_maintenance' => 1,
                    'installations' => 1,
                    'repairs' => 0,
                    'testing' => 1,
                ]
            );

            // Lead Address
            LeadAddress::updateOrCreate(
                ['lead_id' => $lead->id],
                [
                    'contact_firstname' => $customer->contact_firstname,
                    'contact_lastname' => $customer->contact_lastname,
                    'contact_phone' => $customer->contact_phone,
                    'contact_mobile' => $customer->contact_mobile,
                    'contact_email' => $customer->contact_email,
                    'address' => $customer->siteaddress->first()->address ?? 'Site Address',
                    'county' => $customer->siteaddress->first()->county ?? 'England',
                    'postcode' => $customer->siteaddress->first()->postcode ?? 'B1 1AA',
                    'country' => 'United Kingdom',
                    'location' => $customer->siteaddress->first()->location ?? 'Site Location',
                    'status' => 'active',
                    'default' => 'yes',
                ]
            );

            // Notes
            foreach ($lData['notes'] as $n) {
                LeadNote::firstOrCreate(
                    [
                        'lead_id' => $lead->id,
                        'note' => $n['note'],
                    ],
                    [
                        'note_by' => $n['note_by'],
                        'note_type' => 'general',
                        'note_status' => 'active',
                    ]
                );
            }

            $leadModels[$lData['lead_name']] = $lead;
        }

        // -------------------------------------------------------------
        // 6. QUOTATIONS
        // -------------------------------------------------------------
        $lead1 = $leadModels['LEAD-2026-0001'];
        $lead2 = $leadModels['LEAD-2026-0002'];
        $lead3 = $leadModels['LEAD-2026-0003'];

        Quotation::updateOrCreate(
            ['lead_id' => $lead1->id],
            [
                'customer_id' => $lead1->customer_id,
                'job_type_id' => $hvacJobType->id,
                'quotation_version' => 'Q-2026-0001-V1',
                'quotation_date' => now()->subDays(3)->format('Y-m-d'),
                'cover_letter' => 'We are pleased to provide our formal turnkey proposal for the replacement and commissioning of the 45kW Daikin Server Room Chiller Package at Apex Industrial Plant 2.',
                'quotation_template_id' => $templateId1,
                'valid_until' => now()->addDays(27)->format('Y-m-d'),
                'total_amount' => 46850.00,
                'status' => 'accepted',
                'remarks' => 'Accepted by Richard Vance via purchase order PO-APX-99401.',
            ]
        );

        Quotation::updateOrCreate(
            ['lead_id' => $lead2->id],
            [
                'customer_id' => $lead2->customer_id,
                'job_type_id' => $solarJobType->id,
                'quotation_version' => 'Q-2026-0002-V1',
                'quotation_date' => now()->subDays(1)->format('Y-m-d'),
                'cover_letter' => 'Turnkey commercial engineering proposal for 150kW Rooftop Solar PV & 80kWh Battery Energy Storage System for Meridian Logistics Central Depot.',
                'quotation_template_id' => $templateId2,
                'valid_until' => now()->addDays(30)->format('Y-m-d'),
                'total_amount' => 128400.00,
                'status' => 'draft',
                'remarks' => 'Pending board approval scheduled for this Thursday.',
            ]
        );

        Quotation::updateOrCreate(
            ['lead_id' => $lead3->id],
            [
                'customer_id' => $lead3->customer_id,
                'job_type_id' => $jobTypeModels['electrical-inspection']->id,
                'quotation_version' => 'Q-2026-0003-V1',
                'quotation_date' => now()->format('Y-m-d'),
                'cover_letter' => 'Annual recurring maintenance proposal for 11kV Substation Switchgear and preventative thermography.',
                'quotation_template_id' => $templateId1,
                'valid_until' => now()->addDays(45)->format('Y-m-d'),
                'total_amount' => 18500.00,
                'status' => 'draft',
                'remarks' => 'Competitive tender pricing submission.',
            ]
        );

        // -------------------------------------------------------------
        // 7. FJOBS (Active Jobs)
        // -------------------------------------------------------------
        $jobsData = [
            [
                'job_name' => 'JOB-2026-0001',
                'slug' => 'job-2026-0001',
                'lead_name' => 'LEAD-2026-0001',
                'enquiry_name' => 'ENQ-2026-0001',
                'customer' => 'Apex Industrial Solutions Ltd',
                'job_title' => 'Apex Industrial - Emergency 45kW Chiller Package Replacement',
                'job_description' => 'Decommission existing faulty chiller, execute crane lift to roof plantroom, install new 45kW Daikin inverter chiller, vacuum purge and pressure test refrigerant lines, wire 3-phase power, and commission control BMS integration.',
                'job_type' => 'commercial-hvac',
                'job_category' => 'request',
                'job_priority' => 'high',
                'job_status' => 'inprogress',
                'notes' => [
                    [
                        'note' => 'Day 1: Decommissioned old chiller and safely recovered 6.4kg remaining R410A gas into certified recovery cylinder.',
                        'note_by' => 'staff',
                    ],
                    [
                        'note' => 'Day 2: 30-tonne mobile crane on site at 06:00. Lifted Daikin 45kW unit onto vibration-isolated roof frame. Anchors secured.',
                        'note_by' => 'staff',
                    ],
                    [
                        'note' => 'Next: Complete copper brazing with nitrogen purge and conduct 24-hour OFN pressure decay test at 35 bar.',
                        'note_by' => 'staff',
                    ],
                ],
            ],
            [
                'job_name' => 'JOB-2026-0002',
                'slug' => 'job-2026-0002',
                'lead_name' => 'LEAD-2026-0002',
                'enquiry_name' => 'ENQ-2026-0002',
                'customer' => 'Horizon Luxury Hotels Group',
                'job_title' => 'Horizon Grand Hotel - 3-Year Electrical Periodic Inspection (EICR)',
                'job_description' => 'Comprehensive periodic testing and inspection of 18 distribution boards, guest suites sub-circuits, and central heating plantroom. Testing insulation resistance, earth fault loop impedance, and RCD trip times.',
                'job_type' => 'electrical-inspection',
                'job_category' => 'request',
                'job_priority' => 'medium',
                'job_status' => 'inprogress',
                'notes' => [
                    [
                        'note' => 'Floors 1 to 3 completed without disruption to guests. DB-1, DB-2, and DB-3 passed with satisfactory results.',
                        'note_by' => 'staff',
                    ],
                    [
                        'note' => 'Found code C2 observation on Kitchen Sub-Board DB-4: 32A breaker feeding combi-oven has degraded insulation (0.4 MOhm). Replacement planned for tomorrow morning.',
                        'note_by' => 'staff',
                    ],
                ],
            ],
            [
                'job_name' => 'JOB-2026-0003',
                'slug' => 'job-2026-0003',
                'lead_name' => 'LEAD-2026-0003',
                'enquiry_name' => 'ENQ-2026-0004',
                'customer' => 'Sterling Property Estates Group',
                'job_title' => 'Princes Arcade - Burst 2-Inch Copper Water Main Emergency Repair',
                'job_description' => 'Excavation of basement pipe duct, freeze isolation of incoming municipal water supply, removal of burst 54mm copper elbow, installation of new press-fit copper assembly, and pressure testing to 8 bar.',
                'job_type' => 'commercial-plumbing',
                'job_category' => 'complaint',
                'job_priority' => 'high',
                'job_status' => 'completed',
                'notes' => [
                    [
                        'note' => 'Emergency crew attended within 45 minutes. Water isolated preventing flood damage to retail tenant stock.',
                        'note_by' => 'staff',
                    ],
                    [
                        'note' => 'New 54mm copper line installed, pressure tested to 8 bar for 2 hours with zero pressure drop. Facility sign-off signed by Fiona MacLeod.',
                        'note_by' => 'staff',
                    ],
                ],
            ],
            [
                'job_name' => 'JOB-2026-0004',
                'slug' => 'job-2026-0004',
                'lead_name' => 'LEAD-2026-0004',
                'enquiry_name' => 'ENQ-2026-0005',
                'customer' => 'BlueSky Renewable Energy Ltd',
                'job_title' => 'Avonmouth CleanTech Solar Park - Inverter Firmware Optimization',
                'job_description' => 'Scheduled remote and on-site firmware update across 6 central solar inverters to optimize MPPT tracking curve during low-irradiance morning hours.',
                'job_type' => 'solar-pv-storage',
                'job_category' => 'request',
                'job_priority' => 'low',
                'job_status' => 'new',
                'notes' => [
                    [
                        'note' => 'Job scheduled for engineer dispatch next Monday.',
                        'note_by' => 'staff',
                    ],
                ],
            ],
        ];

        foreach ($jobsData as $jData) {
            $customer = $customerModels[$jData['customer']];
            $lead = $leadModels[$jData['lead_name']];
            $enquiry = $enquiryModels[$jData['enquiry_name']];

            $fjob = Fjob::firstOrCreate(
                ['job_name' => $jData['job_name']],
                [
                    'slug' => $jData['slug'],
                    'lead_id' => $lead->id,
                    'enquiry_id' => $enquiry->id,
                    'enquiry' => $enquiry->enquiry,
                    'job_title' => $jData['job_title'],
                    'job_description' => $jData['job_description'],
                    'customer_id' => $customer->id,
                    'job_type' => $jData['job_type'],
                    'job_category' => $jData['job_category'],
                    'job_priority' => $jData['job_priority'],
                    'job_status' => $jData['job_status'],
                    'annual_maintenance' => 1,
                    'installations' => 1,
                    'repairs' => 1,
                    'testing' => 1,
                ]
            );

            // Job Address
            FjobAddress::updateOrCreate(
                ['fjob_id' => $fjob->id],
                [
                    'contact_firstname' => $customer->contact_firstname,
                    'contact_lastname' => $customer->contact_lastname,
                    'contact_phone' => $customer->contact_phone,
                    'contact_mobile' => $customer->contact_mobile,
                    'contact_email' => $customer->contact_email,
                    'address' => $customer->siteaddress->first()->address ?? 'Site Address',
                    'county' => $customer->siteaddress->first()->county ?? 'England',
                    'postcode' => $customer->siteaddress->first()->postcode ?? 'B1 1AA',
                    'country' => 'United Kingdom',
                    'location' => $customer->siteaddress->first()->location ?? 'Site Location',
                    'status' => 'active',
                    'default' => 'yes',
                ]
            );

            // Notes
            foreach ($jData['notes'] as $n) {
                FjobNote::firstOrCreate(
                    [
                        'fjob_id' => $fjob->id,
                        'note' => $n['note'],
                    ],
                    [
                        'note_by' => $n['note_by'],
                        'note_type' => 'general',
                        'note_status' => 'active',
                    ]
                );
            }
        }

        // -------------------------------------------------------------
        // 8. STOCK / INVENTORY WAREHOUSES & ITEMS
        // -------------------------------------------------------------
        $warehouses = [
            [
                'name' => 'Central London Logistics Depot',
                'location' => 'Park Royal, London NW10',
                'description' => 'Primary south-east central materials depot and logistics clearing house.',
            ],
            [
                'name' => 'Midlands Spares & Engineering Hub',
                'location' => 'Tyseley Industrial Estate, Birmingham B11',
                'description' => 'Heavy HVAC components, refrigeration gases, and electrical switchgear depot.',
            ],
            [
                'name' => 'Northern Operations Store',
                'location' => 'Trafford Park, Manchester M17',
                'description' => 'Northern regional spare parts depot for rapid call-out response.',
            ],
        ];

        $warehouseModels = [];
        foreach ($warehouses as $wh) {
            $warehouseModels[$wh['name']] = StockWarehouse::firstOrCreate(
                ['name' => $wh['name']],
                $wh
            );
        }

        $categories = [
            ['name' => 'HVAC & Refrigeration', 'slug' => 'hvac-refrigeration', 'description' => 'Chillers, heat pumps, compressors, and gases'],
            ['name' => 'Electrical Switchgear & Controls', 'slug' => 'electrical-switchgear', 'description' => 'Circuit breakers, contactors, distribution boards, and cables'],
            ['name' => 'Plumbing & Commercial Pipefitting', 'slug' => 'plumbing-pipefitting', 'description' => 'Copper pipe, press fittings, booster pumps, and valves'],
            ['name' => 'Personal Protective Equipment (PPE)', 'slug' => 'ppe-safety', 'description' => 'Harnesses, helmets, arc flash protection, and gas monitors'],
            ['name' => 'Industrial Tools & Instruments', 'slug' => 'tools-instruments', 'description' => 'Vacuum pumps, thermal imaging cameras, and power tools'],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['slug']] = StockCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }

        $whMidlands = $warehouseModels['Midlands Spares & Engineering Hub'];
        $whLondon = $warehouseModels['Central London Logistics Depot'];
        $whNorth = $warehouseModels['Northern Operations Store'];

        $stockItemsData = [
            [
                'name' => 'Daikin 15kW Inverter Heat Pump Outdoor Unit',
                'slug' => 'daikin-15kw-inverter-heat-pump',
                'category_slug' => 'hvac-refrigeration',
                'description' => 'High-efficiency R32 commercial split heat pump unit with variable-speed inverter scroll compressor.',
                'variant' => [
                    'name' => 'Daikin 15kW Standard Unit (3-Phase)',
                    'sku' => 'DKN-HP15-3P',
                    'quantity' => 3.0, // LOW STOCK ALERT (< 5)
                    'unit' => 'Pcs',
                    'avg_price' => 3450.00,
                    'warehouse_id' => $whMidlands->id,
                ],
            ],
            [
                'name' => 'R410A Refrigerant Gas Cylinder (10kg)',
                'slug' => 'r410a-refrigerant-gas-10kg',
                'category_slug' => 'hvac-refrigeration',
                'description' => 'Virgin R410A HFC refrigerant gas for commercial split systems and packaged chillers.',
                'variant' => [
                    'name' => 'R410A 10kg Returnable Cylinder',
                    'sku' => 'GAS-R410A-10KG',
                    'quantity' => 2.0, // CRITICAL LOW STOCK ALERT (< 5)
                    'unit' => 'Pcs',
                    'avg_price' => 145.00,
                    'warehouse_id' => $whMidlands->id,
                ],
            ],
            [
                'name' => 'Schneider Electric 63A 3-Phase Type B MCB',
                'slug' => 'schneider-63a-3phase-mcb',
                'category_slug' => 'electrical-switchgear',
                'description' => 'Acti9 iC60N 63A 3P miniature circuit breaker, 10kA breaking capacity according to EN/IEC 60947-2.',
                'variant' => [
                    'name' => 'Acti9 63A 3P Type B',
                    'sku' => 'SCH-MCB-3P63-B',
                    'quantity' => 45.0,
                    'unit' => 'Pcs',
                    'avg_price' => 38.50,
                    'warehouse_id' => $whLondon->id,
                ],
            ],
            [
                'name' => 'Hard Copper Tube 22mm x 3m Table X',
                'slug' => 'copper-tube-22mm-3m',
                'category_slug' => 'plumbing-pipefitting',
                'description' => 'BS EN 1057 plain half-hard copper tube for commercial potable water and heating installations.',
                'variant' => [
                    'name' => '22mm x 3m Length',
                    'sku' => 'COP-22MM-3M',
                    'quantity' => 120.0,
                    'unit' => 'Pcs',
                    'avg_price' => 16.80,
                    'warehouse_id' => $whLondon->id,
                ],
            ],
            [
                'name' => 'Philips 150W Industrial High-Bay LED Luminaire',
                'slug' => 'philips-150w-highbay-led',
                'category_slug' => 'electrical-switchgear',
                'description' => 'CoreLine Highbay 20,000 lumens, 4000K neutral white, IP65/IK08 rated for warehouse lighting.',
                'variant' => [
                    'name' => 'Philips CoreLine 150W IP65',
                    'sku' => 'PHI-HB150-LED',
                    'quantity' => 32.0,
                    'unit' => 'Pcs',
                    'avg_price' => 84.00,
                    'warehouse_id' => $whNorth->id,
                ],
            ],
            [
                'name' => 'JSP Pioneer 2-Point Full Body Safety Harness',
                'slug' => 'jsp-pioneer-safety-harness',
                'category_slug' => 'ppe-safety',
                'description' => 'EN 361 certified fall arrest harness with front and rear fall arrest D-rings and adjustable thigh straps.',
                'variant' => [
                    'name' => 'JSP Pioneer Standard Harness (M-XL)',
                    'sku' => 'JSP-HARN-P2',
                    'quantity' => 18.0,
                    'unit' => 'Pcs',
                    'avg_price' => 62.00,
                    'warehouse_id' => $whMidlands->id,
                ],
            ],
            [
                'name' => 'Milwaukee M18 Fuel Brushless SDS+ Rotary Hammer',
                'slug' => 'milwaukee-m18-sds-hammer',
                'category_slug' => 'tools-instruments',
                'description' => 'Heavy-duty 18V cordless brushless hammer drill delivering 2.5 Joules of impact energy.',
                'variant' => [
                    'name' => 'Milwaukee M18 FHX-0 Bare Unit',
                    'sku' => 'MLW-M18-SDS-FHX',
                    'quantity' => 6.0,
                    'unit' => 'Pcs',
                    'avg_price' => 285.00,
                    'warehouse_id' => $whLondon->id,
                ],
            ],
        ];

        foreach ($stockItemsData as $sData) {
            $cat = $categoryModels[$sData['category_slug']];
            $item = StockItem::firstOrCreate(
                ['slug' => $sData['slug']],
                [
                    'name' => $sData['name'],
                    'category_id' => $cat->id,
                    'description' => $sData['description'],
                    'is_active' => true,
                ]
            );

            StockItemVariant::updateOrCreate(
                ['sku' => $sData['variant']['sku']],
                [
                    'stock_item_id' => $item->id,
                    'name' => $sData['variant']['name'],
                    'quantity' => $sData['variant']['quantity'],
                    'unit' => $sData['variant']['unit'],
                    'avg_price' => $sData['variant']['avg_price'],
                    'warehouse_id' => $sData['variant']['warehouse_id'],
                    'is_active' => true,
                ]
            );
        }

        // -------------------------------------------------------------
        // 9. FLEET VEHICLES & DRIVERS
        // -------------------------------------------------------------
        $vCategory = VehicleCategory::firstOrCreate(
            ['name' => 'Commercial Service Vans'],
            [
                'description' => 'Medium and long wheelbase commercial vans equipped for mobile engineering teams.',
                'icon' => 'ti tabler-truck',
                'is_active' => true,
            ]
        );

        $vehiclesData = [
            [
                'category_id' => $vCategory->id,
                'registration_no' => 'LD72 XKM',
                'vin_number' => 'WF0XXXTTGXPK92101',
                'manufacturer' => 'Ford',
                'model' => 'Transit Custom 2.0 EcoBlue 130PS',
                'color' => 'Frozen White',
                'year' => 2022,
                'fuel_type' => 'Diesel',
                'mileage' => 34200,
                'status' => 'Assigned',
            ],
            [
                'category_id' => $vCategory->id,
                'registration_no' => 'BL73 VWE',
                'vin_number' => 'WDB9066331P849202',
                'manufacturer' => 'Mercedes-Benz',
                'model' => 'Sprinter 315 CDI Progressive LWB',
                'color' => 'Arctic White',
                'year' => 2023,
                'fuel_type' => 'Diesel',
                'mileage' => 18500,
                'status' => 'Available',
            ],
            [
                'category_id' => $vCategory->id,
                'registration_no' => 'EA24 FTY',
                'vin_number' => 'VNV24X000E8391039',
                'manufacturer' => 'Nissan',
                'model' => 'Townstar EV 45kWh Tekna',
                'color' => 'Diamond Black',
                'year' => 2024,
                'fuel_type' => 'Electric',
                'mileage' => 5400,
                'status' => 'Assigned',
            ],
        ];

        foreach ($vehiclesData as $vData) {
            VehicleDetail::updateOrCreate(
                ['registration_no' => $vData['registration_no']],
                $vData
            );
        }

        $driversData = [
            [
                'user_id' => $adminUserId,
                'license_no' => 'MILLD802194D99XX',
                'license_category' => 'B, C1',
                'digital_tacho_card' => true,
                'dbs_check_passed' => true,
                'medical_check_passed' => true,
                'license_expiry' => now()->addYears(3)->format('Y-m-d'),
            ],
            [
                'user_id' => null,
                'license_no' => 'EVANC709121E88YY',
                'license_category' => 'B',
                'digital_tacho_card' => false,
                'dbs_check_passed' => true,
                'medical_check_passed' => true,
                'license_expiry' => now()->addYears(4)->format('Y-m-d'),
            ],
            [
                'user_id' => null,
                'license_no' => 'PATEN905102P77ZZ',
                'license_category' => 'B, BE',
                'digital_tacho_card' => true,
                'dbs_check_passed' => true,
                'medical_check_passed' => true,
                'license_expiry' => now()->addYears(2)->format('Y-m-d'),
            ],
        ];

        foreach ($driversData as $dData) {
            DriverDetail::updateOrCreate(
                ['license_no' => $dData['license_no']],
                $dData
            );
        }

        // -------------------------------------------------------------
        // 10. AI USAGE LOGS (Today's Telemetry Data)
        // -------------------------------------------------------------
        $aiLogs = [
            [
                'provider' => 'gemini',
                'model' => 'gemini-flash-lite-latest',
                'prompt_tokens' => 380,
                'completion_tokens' => 195,
                'total_tokens' => 575,
                'latency_ms' => 640.5,
                'status' => 'success',
                'module' => 'chatbot',
                'created_at' => now()->subMinutes(140),
            ],
            [
                'provider' => 'gemini',
                'model' => 'gemini-flash-lite-latest',
                'prompt_tokens' => 420,
                'completion_tokens' => 240,
                'total_tokens' => 660,
                'latency_ms' => 710.2,
                'status' => 'success',
                'module' => 'chatbot',
                'created_at' => now()->subMinutes(110),
            ],
            [
                'provider' => 'gemini',
                'model' => 'gemini-flash-lite-latest',
                'prompt_tokens' => 510,
                'completion_tokens' => 310,
                'total_tokens' => 820,
                'latency_ms' => 890.4,
                'status' => 'success',
                'module' => 'lead_summarization',
                'created_at' => now()->subMinutes(85),
            ],
            [
                'provider' => 'gemini',
                'model' => 'gemini-flash-lite-latest',
                'prompt_tokens' => 340,
                'completion_tokens' => 180,
                'total_tokens' => 520,
                'latency_ms' => 590.1,
                'status' => 'success',
                'module' => 'enquiry_summarization',
                'created_at' => now()->subMinutes(60),
            ],
            [
                'provider' => 'gemini',
                'model' => 'gemini-flash-lite-latest',
                'prompt_tokens' => 620,
                'completion_tokens' => 380,
                'total_tokens' => 1000,
                'latency_ms' => 920.8,
                'status' => 'success',
                'module' => 'floating_chatbot',
                'created_at' => now()->subMinutes(35),
            ],
            [
                'provider' => 'openai',
                'model' => 'gpt-4o-mini',
                'prompt_tokens' => 410,
                'completion_tokens' => 210,
                'total_tokens' => 620,
                'latency_ms' => 1120.5,
                'status' => 'success',
                'module' => 'lead_summarization',
                'created_at' => now()->subMinutes(20),
            ],
        ];

        foreach ($aiLogs as $log) {
            AiUsageLog::create($log);
        }
    }
}
