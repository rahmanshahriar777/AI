<?php

namespace App\Http\Controllers;

use App\Models\AiUsageLog;
use App\Models\BusinessInfo;
use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\Fjob;
use App\Models\Lead;
use App\Models\StockItem;
use App\Models\StockItemVariant;
use App\Services\AI\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AiAssistantController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Display the full-page AI Chatbot interface.
     */
    public function chatPage(): View
    {
        $stats = [
            'customers' => Customer::count(),
            'open_enquiries' => Enquiry::whereIn('enquiry_status', ['new', 'inprogress'])->count(),
            'active_leads' => Lead::whereIn('lead_status', ['new', 'inprogress'])->count(),
            'active_jobs' => Fjob::whereIn('job_status', ['new', 'inprogress'])->count(),
            'stock_items' => StockItem::count(),
        ];

        return view('ai.chat', compact('stats'));
    }

    /**
     * Interactive conversational Chatbot endpoint.
     */
    public function chat(Request $request): JsonResponse
    {
        @set_time_limit(120);

        $request->validate([
            'message' => 'required|string|max:4000',
            'history' => 'nullable|array',
            'history.*.role' => 'required_with:history|string|in:user,model,assistant',
            'history.*.text' => 'required_with:history|string',
            'module' => 'nullable|string|max:64',
        ]);

        $message = trim($request->input('message'));
        $history = $request->input('history', []);
        $module = $request->input('module', 'chatbot');

        $user = Auth::user();
        $userName = $user ? $user->name : 'ERP Staff';
        $userRoles = ($user && method_exists($user, 'getRoleNames')) ? implode(', ', $user->getRoleNames()->toArray()) : 'staff';
        $isAdmin = $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['super_admin', 'admin']);

        // Gather real-time context from the ERP system
        $customerCount = Customer::count();
        $openEnquiries = Enquiry::whereIn('enquiry_status', ['new', 'inprogress'])->count();
        $activeLeads = Lead::whereIn('lead_status', ['new', 'inprogress'])->count();
        $activeJobs = Fjob::whereIn('job_status', ['new', 'inprogress'])->count();
        $stockCount = StockItem::count();

        // Detailed live context for accurate AI reasoning
        $recentEnquiries = Enquiry::with('customer')
            ->whereIn('enquiry_status', ['new', 'inprogress'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($e) {
                $cName = $e->customer->company_name ?? 'Client';
                return "- [Enquiry #{$e->id}] {$cName}: {$e->enquiry} (Priority: {$e->enquiry_priority}, Status: {$e->enquiry_status})";
            })->implode("\n");

        $recentLeads = Lead::with('customer')
            ->whereIn('lead_status', ['new', 'inprogress'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($l) {
                $cName = $l->customer->company_name ?? 'Client';
                return "- [Lead #{$l->id}] {$cName}: {$l->lead_title} (Priority: {$l->lead_priority}, Status: {$l->lead_status})";
            })->implode("\n");

        $recentJobs = Fjob::with('customer')
            ->whereIn('job_status', ['new', 'inprogress'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($j) {
                $cName = $j->customer->company_name ?? 'Client';
                return "- [Job #{$j->id}] {$cName}: {$j->job_title} (Priority: {$j->job_priority}, Status: {$j->job_status})";
            })->implode("\n");

        $lowStockVariants = StockItemVariant::with(['stockItem', 'warehouse'])
            ->where('quantity', '<=', 5)
            ->limit(5)
            ->get()
            ->map(function ($v) {
                $itemName = $v->stockItem->name ?? $v->name;
                $whName = $v->warehouse->name ?? 'Main Depot';
                return "- {$itemName} (SKU: {$v->sku}, Qty: {$v->quantity} {$v->unit}, Warehouse: {$whName})";
            })->implode("\n");

        $systemPrompt = "You are NeoERP AI Assistant, an enterprise-grade intelligent assistant integrated into the NeoERP business management system.\n" .
            "You are conversing with {$userName} (Role: {$userRoles}).\n" .
            "Current live system status:\n" .
            "- Total Customers: {$customerCount}\n" .
            "- Open Enquiries: {$openEnquiries}\n" .
            "- Active Leads: {$activeLeads}\n" .
            "- Active Jobs: {$activeJobs}\n" .
            "- Stock Items: {$stockCount}\n\n" .
            ($recentEnquiries ? "Open Enquiries:\n{$recentEnquiries}\n\n" : "") .
            ($recentLeads ? "Active Sales Leads:\n{$recentLeads}\n\n" : "") .
            ($recentJobs ? "Active In-Progress Jobs:\n{$recentJobs}\n\n" : "") .
            ($lowStockVariants ? "Low Stock Alerts (Immediate Reorder Required):\n{$lowStockVariants}\n\n" : "") .
            "Guidelines:\n" .
            "1. Answer business, ERP operational, customer, quoting, invoicing, scheduler, stock, and health & safety questions accurately using real data from the live ERP context.\n" .
            "2. Format responses with clean Markdown (bullet points, bold highlights, concise paragraphs).\n" .
            "3. If asked to draft emails or documents, provide professional, ready-to-send copy.\n" .
            "4. Be concise, polite, helpful, and focused on business productivity.";

        // Assemble conversational prompt with history
        $formattedPrompt = '';
        if (!empty($history)) {
            $formattedPrompt .= "Conversation History:\n";
            foreach (array_slice($history, -8) as $turn) {
                $roleLabel = ($turn['role'] === 'user') ? 'User' : 'Assistant';
                $turnText = trim($turn['text'] ?? '');
                $formattedPrompt .= "{$roleLabel}: {$turnText}\n";
            }
            $formattedPrompt .= "\nCurrent User Message:\n{$message}";
        } else {
            $formattedPrompt = $message;
        }

        $response = $this->aiService->generate($formattedPrompt, [
            'module' => $module,
            'systemPrompt' => $systemPrompt,
            'maxTokens' => 1000,
        ]);

        return response()->json([
            'success' => $response->isSuccess(),
            'text' => $response->text,
            'errorMessage' => $response->errorMessage,
            'errorType' => $response->errorType,
            'latencyMs' => $response->latencyMs,
            'tokensUsed' => $response->tokensUsed,
            'providerUsed' => $isAdmin ? $response->providerUsed : null,
            'isAdmin' => (bool) $isAdmin,
            'timestamp' => now()->format('H:i'),
        ]);
    }

    /**
     * General AI generation endpoint for single prompts.
     */
    public function generate(Request $request): JsonResponse
    {
        @set_time_limit(120);

        $request->validate([
            'prompt' => 'required|string|max:4000',
            'module' => 'nullable|string|max:64',
            'systemPrompt' => 'nullable|string|max:1000',
        ]);

        $prompt = $request->input('prompt');
        $module = $request->input('module', 'erp_assistant');
        $systemPrompt = $request->input('systemPrompt', 'You are the intelligent ERP AI Assistant for NeoERP. Provide concise, professional, and actionable business responses.');

        $response = $this->aiService->generate($prompt, [
            'module' => $module,
            'systemPrompt' => $systemPrompt,
            'maxTokens' => (int) $request->input('maxTokens', 1000),
        ]);

        $user = Auth::user();
        $isAdmin = $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['super_admin', 'admin']);

        return response()->json([
            'success' => $response->isSuccess(),
            'text' => $response->text,
            'errorMessage' => $response->errorMessage,
            'errorType' => $response->errorType,
            'latencyMs' => $response->latencyMs,
            'tokensUsed' => $response->tokensUsed,
            'providerUsed' => $isAdmin ? $response->providerUsed : null,
            'isAdmin' => (bool) $isAdmin,
        ]);
    }

    /**
     * Summarize an existing Lead and recommend next steps.
     */
    public function summarizeLead(int $id): JsonResponse
    {
        @set_time_limit(120);

        $lead = Lead::with(['customer', 'leadNotes'])->find($id);

        if (!$lead) {
            return response()->json([
                'success' => false,
                'errorMessage' => 'Lead not found.',
            ], 404);
        }

        $customerName = $lead->customer ? trim(($lead->customer->contact_firstname ?? '') . ' ' . ($lead->customer->contact_lastname ?? '')) : 'Unknown';
        $company = $lead->customer->company_name ?? 'N/A';
        $status = $lead->lead_status ?? 'new';
        $enquiry = $lead->enquiry ?? 'No description';

        $notesText = '';
        if ($lead->leadNotes && count($lead->leadNotes) > 0) {
            $notesText = $lead->leadNotes->pluck('note')->implode("\n- ");
        }

        $prompt = "Please summarize this ERP sales lead and suggest 3 concrete next action steps for our sales representative:\n\n" .
            "Customer: {$customerName} ({$company})\n" .
            "Lead Status: {$status}\n" .
            "Original Enquiry / Requirement: {$enquiry}\n" .
            ($notesText ? "Staff Notes:\n- {$notesText}\n" : "");

        $response = $this->aiService->generate($prompt, [
            'module' => 'lead_summarization',
            'systemPrompt' => 'You are an ERP Sales Operations specialist. Summarize the lead concisely with key facts and 3 actionable next steps.',
            'maxTokens' => 800,
        ]);

        $user = Auth::user();
        $isAdmin = $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['super_admin', 'admin']);

        return response()->json([
            'success' => $response->isSuccess(),
            'text' => $response->text,
            'errorMessage' => $response->errorMessage,
            'providerUsed' => $isAdmin ? $response->providerUsed : null,
            'latencyMs' => $response->latencyMs,
            'isAdmin' => (bool) $isAdmin,
        ]);
    }

    /**
     * Summarize an Enquiry and suggest a professional draft email reply.
     */
    public function summarizeEnquiry(int $id): JsonResponse
    {
        @set_time_limit(120);

        $enquiry = Enquiry::with(['customer', 'enquiryNotes'])->find($id);

        if (!$enquiry) {
            return response()->json([
                'success' => false,
                'errorMessage' => 'Enquiry not found.',
            ], 404);
        }

        $customerName = $enquiry->customer ? trim(($enquiry->customer->contact_firstname ?? '') . ' ' . ($enquiry->customer->contact_lastname ?? '')) : 'Valued Client';
        $status = $enquiry->enquiry_status ?? 'new';
        $details = $enquiry->description ?? $enquiry->enquiry ?? 'No details provided';

        $prompt = "Summarize this client enquiry and write a polite, professional initial email response to the customer:\n\n" .
            "Client: {$customerName}\n" .
            "Status: {$status}\n" .
            "Enquiry Details: {$details}";

        $response = $this->aiService->generate($prompt, [
            'module' => 'enquiry_summarization',
            'systemPrompt' => 'You are a customer support and sales coordinator in an ERP environment. Provide a brief summary followed by a ready-to-send draft email response.',
            'maxTokens' => 800,
        ]);

        $user = Auth::user();
        $isAdmin = $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['super_admin', 'admin']);

        return response()->json([
            'success' => $response->isSuccess(),
            'text' => $response->text,
            'errorMessage' => $response->errorMessage,
            'providerUsed' => $isAdmin ? $response->providerUsed : null,
            'latencyMs' => $response->latencyMs,
            'isAdmin' => (bool) $isAdmin,
        ]);
    }

    /**
     * Admin-only usage statistics endpoint.
     */
    public function usageStats(): JsonResponse
    {
        $user = Auth::user();
        if (!$user || !method_exists($user, 'hasAnyRole') || !$user->hasAnyRole(['super_admin', 'admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $todayGemini = AiUsageLog::getTodayUsageCount('gemini');
        $todayOpenAI = AiUsageLog::getTodayUsageCount('openai');

        $recentLogs = AiUsageLog::orderByDesc('created_at')->limit(15)->get();

        return response()->json([
            'gemini' => [
                'today_requests' => $todayGemini,
                'daily_cap' => config('ai.gemini.daily_cap', 100),
            ],
            'openai' => [
                'today_requests' => $todayOpenAI,
                'daily_cap' => config('ai.openai.daily_cap', 50),
            ],
            'recent_logs' => $recentLogs,
        ]);
    }
}
