<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiService;
use App\Models\Lead;
use App\Models\Enquiry;
use App\Models\Quotation;
use App\Models\JobType;
use Illuminate\Support\Facades\File;
use Exception;

class GeminiAIController extends Controller
{
    protected GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * Display the Gemini AI Hub & Settings Dashboard.
     */
    public function index()
    {
        $isConfigured = $this->gemini->isConfigured();
        $currentModel = config('gemini.model', 'gemini-1.5-flash');
        $apiKeyMasked = $isConfigured ? substr(config('gemini.api_key'), 0, 8) . '...' . substr(config('gemini.api_key'), -4) : null;
        $jobTypes = JobType::all();

        return view('gemini.index', compact('isConfigured', 'currentModel', 'apiKeyMasked', 'jobTypes'));
    }

    /**
     * Test Gemini API Connection.
     */
    public function testConnection(Request $request)
    {
        $apiKey = $request->input('api_key');
        $model = $request->input('model', config('gemini.model', 'gemini-1.5-flash'));

        $result = $this->gemini->testConnection($apiKey, $model);

        return response()->json($result);
    }

    /**
     * Save / Update Gemini Settings in .env
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'api_key' => 'nullable|string',
            'model' => 'required|string',
        ]);

        $apiKey = trim($request->input('api_key', ''));
        $model = trim($request->input('model', 'gemini-1.5-flash'));

        try {
            $envPath = base_path('.env');
            if (File::exists($envPath)) {
                $envContent = File::get($envPath);

                // Update GEMINI_API_KEY
                if (!empty($apiKey)) {
                    if (str_contains($envContent, 'GEMINI_API_KEY=')) {
                        $envContent = preg_replace('/^GEMINI_API_KEY=.*$/m', "GEMINI_API_KEY={$apiKey}", $envContent);
                    } else {
                        $envContent .= "\nGEMINI_API_KEY={$apiKey}\n";
                    }
                }

                // Update GEMINI_MODEL
                if (str_contains($envContent, 'GEMINI_MODEL=')) {
                    $envContent = preg_replace('/^GEMINI_MODEL=.*$/m', "GEMINI_MODEL={$model}", $envContent);
                } else {
                    $envContent .= "\nGEMINI_MODEL={$model}\n";
                }

                File::put($envPath, $envContent);
            }

            return response()->json([
                'success' => true,
                'message' => 'Gemini AI settings saved successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Workflow 1: Analyze Lead or Enquiry
     */
    public function analyzeLead(Request $request)
    {
        $leadId = $request->input('lead_id');
        $enquiryId = $request->input('enquiry_id');

        $data = [
            'customer_name' => $request->input('customer_name', 'Valued Client'),
            'email' => $request->input('email', ''),
            'phone' => $request->input('phone', ''),
            'company' => $request->input('company', ''),
            'job_type' => $request->input('job_type', 'General Inquiry'),
            'description' => $request->input('description', ''),
            'notes' => $request->input('notes', ''),
        ];

        // If leadId is passed, retrieve lead data
        if ($leadId && empty($data['description'])) {
            $lead = Lead::with('customer', 'jobType')->find($leadId);
            if ($lead) {
                $data['customer_name'] = $lead->customer->name ?? $data['customer_name'];
                $data['company'] = $lead->customer->company ?? $data['company'];
                $data['email'] = $lead->customer->email ?? $data['email'];
                $data['phone'] = $lead->customer->phone ?? $data['phone'];
                $data['job_type'] = $lead->jobType->name ?? $data['job_type'];
                $data['description'] = $lead->description ?? $data['description'];
            }
        }

        // If enquiryId is passed, retrieve enquiry data
        if ($enquiryId && empty($data['description'])) {
            $enquiry = Enquiry::with('customer', 'jobType')->find($enquiryId);
            if ($enquiry) {
                $data['customer_name'] = $enquiry->name ?? ($enquiry->customer->name ?? $data['customer_name']);
                $data['email'] = $enquiry->email ?? $data['email'];
                $data['phone'] = $enquiry->phone ?? $data['phone'];
                $data['job_type'] = $enquiry->jobType->name ?? $data['job_type'];
                $data['description'] = $enquiry->description ?? $data['description'];
            }
        }

        $result = $this->gemini->analyzeLeadOrEnquiry($data);
        return response()->json($result);
    }

    /**
     * Workflow 2: Smart Email Reply Drafter
     */
    public function draftEmail(Request $request)
    {
        $context = [
            'recipient_name' => $request->input('recipient_name', 'Customer'),
            'recipient_email' => $request->input('recipient_email', ''),
            'subject_context' => $request->input('subject_context', 'Service Inquiry'),
            'intent' => $request->input('intent', 'Initial quotation and response'),
            'business_name' => config('app.name', 'NeoERP'),
            'sender_name' => auth()->user()->name ?? 'Customer Service',
            'enquiry_details' => $request->input('enquiry_details', ''),
            'tone' => $request->input('tone', 'Professional, prompt, and friendly'),
        ];

        $result = $this->gemini->draftEmailReply($context);
        return response()->json($result);
    }

    /**
     * Workflow 3: Generate Quotation Scope of Work
     */
    public function generateQuotationScope(Request $request)
    {
        $details = [
            'customer' => $request->input('customer', 'Client'),
            'title' => $request->input('title', 'Service Proposal'),
            'job_type' => $request->input('job_type', 'General Work'),
            'description' => $request->input('description', ''),
            'special_requirements' => $request->input('special_requirements', ''),
        ];

        $result = $this->gemini->generateQuotationScope($details);
        return response()->json($result);
    }

    /**
     * Workflow 4: Generate RAMS Safety Checklist
     */
    public function generateSafetyChecklist(Request $request)
    {
        $jobType = $request->input('job_type', 'General Trade / Installation');
        $jobDescription = $request->input('job_description', 'On-site technical and installation work.');

        $result = $this->gemini->generateSafetyChecklist($jobType, $jobDescription);
        return response()->json($result);
    }

    /**
     * Workflow 5: Interactive Chatbot endpoint for ERP
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'history' => 'nullable|array',
        ]);

        $message = $request->input('message');
        $history = $request->input('history', []);

        $result = $this->gemini->chatWithERP($message, $history);
        return response()->json($result);
    }
}
