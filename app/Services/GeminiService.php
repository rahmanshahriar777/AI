<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;
    protected float $temperature;
    protected int $maxTokens;
    protected int $timeout;

    public function __construct(?string $apiKey = null, ?string $model = null)
    {
        $this->apiKey = $apiKey ?: (string) config('gemini.api_key', env('GEMINI_API_KEY', ''));
        $this->model = $model ?: (string) config('gemini.model', 'gemini-1.5-flash');
        $this->baseUrl = (string) config('gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
        $this->temperature = (float) config('gemini.temperature', 0.7);
        $this->maxTokens = (int) config('gemini.max_output_tokens', 2048);
        $this->timeout = (int) config('gemini.timeout', 30);
    }

    /**
     * Set the API key dynamically.
     */
    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * Set the Model name dynamically.
     */
    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Check if Gemini API key is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Build an HTTP client instance configured for resilient SSL and timeouts.
     */
    protected function httpClient(int $timeout = 30): \Illuminate\Http\Client\PendingRequest
    {
        $client = Http::timeout($timeout)
            ->withHeaders(['Content-Type' => 'application/json']);

        $caPaths = [
            base_path('cacert.pem'),
            'C:/Users/dev/php-8.2/extras/ssl/cacert.pem',
            ini_get('curl.cainfo'),
            ini_get('openssl.cafile'),
        ];

        $foundCa = null;
        foreach ($caPaths as $path) {
            if (!empty($path) && file_exists($path)) {
                $foundCa = $path;
                break;
            }
        }

        if ($foundCa) {
            $client->withOptions(['verify' => $foundCa]);
        } else {
            // Avoid cURL error 60 if certificate bundle cannot be located on local Windows dev environment
            $client->withoutVerifying();
        }

        return $client;
    }

    /**
     * Extract text across all candidate parts.
     */
    protected function extractTextFromCandidates(array $candidates): string
    {
        if (empty($candidates)) {
            return '';
        }

        $textParts = [];
        $parts = $candidates[0]['content']['parts'] ?? [];
        foreach ($parts as $part) {
            if (!empty($part['text'])) {
                $textParts[] = $part['text'];
            }
        }

        return trim(implode("\n", $textParts));
    }

    /**
     * Test the API key connectivity with Google Gemini.
     */
    public function testConnection(?string $apiKey = null, ?string $model = null): array
    {
        $keyToTest = $apiKey ?: $this->apiKey;
        $modelToTest = $model ?: $this->model;

        if (empty($keyToTest)) {
            return [
                'success' => false,
                'message' => 'Gemini API key is not configured. Please provide a valid API key from Google AI Studio.',
            ];
        }

        try {
            $endpoint = "{$this->baseUrl}/models/{$modelToTest}:generateContent?key={$keyToTest}";

            $response = $this->httpClient(15)->post($endpoint, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => 'Hello Gemini! Respond in one short sentence confirming that you are operational and ready for ERP integration.']
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 100,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $candidates = $data['candidates'] ?? [];
                $text = $this->extractTextFromCandidates($candidates) ?: 'Connected successfully.';
                return [
                    'success' => true,
                    'message' => 'Successfully connected to Google Gemini AI (' . $modelToTest . ')!',
                    'sample_response' => trim($text),
                    'model' => $modelToTest,
                ];
            }

            $errorData = $response->json();
            $errorMessage = $errorData['error']['message'] ?? 'HTTP ' . $response->status() . ' Error';

            return [
                'success' => false,
                'message' => 'Gemini API Error: ' . $errorMessage,
                'status_code' => $response->status(),
            ];
        } catch (Exception $e) {
            Log::error('Gemini connection test failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Generate standard text from a prompt.
     */
    public function generateText(string $prompt, array $options = []): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Gemini API key is missing. Please configure GEMINI_API_KEY in .env or Settings.',
            ];
        }

        $modelsToTry = array_unique(array_filter([
            $options['model'] ?? $this->model,
            'gemini-3.6-flash',
            'gemini-3.7-flash',
        ]));

        $lastError = 'API Request Failed';

        foreach ($modelsToTry as $currentModel) {
            try {
                $endpoint = "{$this->baseUrl}/models/{$currentModel}:generateContent?key={$this->apiKey}";

                $systemInstruction = $options['system_instruction'] ?? null;

                $payload = [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => $options['temperature'] ?? $this->temperature,
                        'maxOutputTokens' => $options['max_tokens'] ?? $this->maxTokens,
                    ]
                ];

                if ($systemInstruction) {
                    $payload['systemInstruction'] = [
                        'parts' => [
                            ['text' => $systemInstruction]
                        ]
                    ];
                }

                $response = $this->httpClient($this->timeout)->post($endpoint, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $candidates = $data['candidates'] ?? [];
                    $text = $this->extractTextFromCandidates($candidates);

                    return [
                        'success' => true,
                        'text' => trim($text),
                        'raw' => $data,
                        'model' => $currentModel,
                    ];
                }

                $error = $response->json();
                $lastError = $error['error']['message'] ?? ('HTTP ' . $response->status() . ' Error');
                Log::warning("Gemini model {$currentModel} failed ({$lastError}), attempting fallback if available.");
            } catch (Exception $e) {
                $lastError = $e->getMessage();
                Log::warning("Gemini Service Exception with {$currentModel}: {$lastError}");
            }
        }

        return [
            'success' => false,
            'error' => $lastError,
        ];
    }

    /**
     * Generate structured JSON output from a prompt.
     */
    public function generateJson(string $prompt, array $options = []): array
    {
        $jsonPrompt = $prompt . "\n\nCRITICAL: Return your response strictly as valid, raw JSON without markdown code fences or backticks.";
        
        $res = $this->generateText($jsonPrompt, array_merge($options, [
            'temperature' => 0.2, // Lower temperature for structured consistency
        ]));

        if (!$res['success']) {
            return $res;
        }

        $rawText = trim($res['text']);
        // Strip markdown backticks if present
        $cleanJson = preg_replace('/^```(?:json)?\s*/i', '', $rawText);
        $cleanJson = preg_replace('/\s*```$/', '', $cleanJson);
        $cleanJson = trim($cleanJson);

        $decoded = json_decode($cleanJson, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return [
                'success' => true,
                'data' => $decoded,
                'raw_text' => $rawText,
            ];
        }

        // Fallback: try to extract JSON with regex if surrounded by text
        if (preg_match('/\{[\s\S]*\}|\[[\s\S]*\]/', $cleanJson, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return [
                    'success' => true,
                    'data' => $decoded,
                    'raw_text' => $rawText,
                ];
            }
        }

        return [
            'success' => true,
            'data' => ['raw_content' => $rawText],
            'raw_text' => $rawText,
            'json_parse_warning' => 'Could not strictly parse as JSON.',
        ];
    }

    /**
     * Workflow 1: AI Lead & Enquiry Analysis & Scoring
     */
    public function analyzeLeadOrEnquiry(array $data): array
    {
        $customerName = $data['customer_name'] ?? 'Potential Client';
        $email = $data['email'] ?? 'N/A';
        $phone = $data['phone'] ?? 'N/A';
        $company = $data['company'] ?? 'N/A';
        $jobType = $data['job_type'] ?? 'General Service';
        $description = $data['description'] ?? 'No description provided';
        $notes = $data['notes'] ?? '';

        $prompt = <<<PROMPT
You are an expert ERP Sales and Business Operations Analyst.
Analyze the following customer enquiry / lead for a service/installation/trade ERP business:

Customer Name: {$customerName}
Company: {$company}
Email: {$email}
Phone: {$phone}
Requested Service/Job Type: {$jobType}
Customer Enquiry/Description:
"{$description}"

Internal Notes:
"{$notes}"

Analyze the enquiry and provide a structured JSON assessment containing:
{
  "summary": "Concise 2-3 sentence executive summary of the customer's request.",
  "qualification_score": 85, // Integer from 1 to 100 based on clarity, intent, and value potential
  "urgency": "High", // "Low", "Medium", "High", or "Urgent"
  "sentiment": "Positive", // "Positive", "Neutral", "Hesitant", or "Urgent"
  "key_requirements": ["Requirement 1", "Requirement 2", "Requirement 3"],
  "recommended_job_type": "Suggested best matching service category",
  "estimated_complexity": "Low / Medium / High",
  "action_items": [
    "Recommended immediate action step 1",
    "Recommended follow-up step 2"
  ],
  "potential_risks_or_clarifications": [
    "Any missing information or risk to clarify with client"
  ]
}
PROMPT;

        return $this->generateJson($prompt);
    }

    /**
     * Workflow 2: Smart Email Reply / Quote Cover Drafter
     */
    public function draftEmailReply(array $context): array
    {
        $recipientName = $context['recipient_name'] ?? 'Valued Customer';
        $recipientEmail = $context['recipient_email'] ?? '';
        $subjectContext = $context['subject_context'] ?? 'Your Service Enquiry';
        $intent = $context['intent'] ?? 'Initial greeting, acknowledgment, and request for schedule';
        $businessName = $context['business_name'] ?? config('app.name', 'Our Company');
        $senderName = $context['sender_name'] ?? 'Customer Support Team';
        $enquiryDetails = $context['enquiry_details'] ?? '';
        $tone = $context['tone'] ?? 'Professional, warm, and prompt';

        $prompt = <<<PROMPT
You are an expert corporate communications specialist for {$businessName}.
Draft a professional email reply to a customer based on the following context:

Recipient Name: {$recipientName}
Context / Subject: {$subjectContext}
Purpose / Intent of Email: {$intent}
Customer's Enquiry Details:
"{$enquiryDetails}"

Tone: {$tone}
Sender Name: {$senderName}
Sender Company: {$businessName}

Generate a structured JSON response:
{
  "subject": "Clear, engaging email subject line",
  "email_body_html": "<p>Formatted HTML email content with proper paragraphs, greetings, bullet points if appropriate, and sign-off</p>",
  "email_body_plain": "Plain text version of the email message",
  "suggested_follow_up_days": 2
}
PROMPT;

        return $this->generateJson($prompt);
    }

    /**
     * Workflow 3: AI Quotation Scope of Work & Terms Generator
     */
    public function generateQuotationScope(array $details): array
    {
        $customer = $details['customer'] ?? 'Client';
        $projectTitle = $details['title'] ?? 'Service Project';
        $jobType = $details['job_type'] ?? 'General Work';
        $description = $details['description'] ?? '';
        $specialRequirements = $details['special_requirements'] ?? '';

        $prompt = <<<PROMPT
You are a senior estimator and contract specialist for a commercial & residential services ERP.
Generate a comprehensive, clear, and professional Scope of Work and Deliverables for a quotation:

Client: {$customer}
Project Title: {$projectTitle}
Job Category: {$jobType}
Client Requirements / Description:
"{$description}"

Special Notes / Materials:
"{$specialRequirements}"

Provide a structured JSON output:
{
  "project_overview": "Comprehensive overview of the proposed project",
  "scope_items": [
    {
      "title": "Phase / Deliverable 1",
      "description": "Detailed explanation of work to be performed",
      "estimated_materials": "Required parts or consumables"
    }
  ],
  "inclusions": ["Included item 1", "Included item 2"],
  "exclusions": ["Permits by others (if applicable)", "Work outside normal hours"],
  "warranty_and_terms": "Standard quality guarantee and conditions",
  "estimated_completion_time": "e.g. 2 - 3 business days"
}
PROMPT;

        return $this->generateJson($prompt);
    }

    /**
     * Workflow 4: RAMS / Safety Checklist & Risk Assessment Suggester
     */
    public function generateSafetyChecklist(string $jobType, string $jobDescription): array
    {
        $prompt = <<<PROMPT
You are a certified Health & Safety (H&S) Officer and RAMS specialist for field service engineering and trade jobs.
Generate a comprehensive site safety risk assessment checklist for the following job:

Job Category: {$jobType}
Job Description: "{$jobDescription}"

Provide a structured JSON response:
{
  "hazard_level": "Low / Medium / High",
  "identified_hazards": [
    {"hazard": "Working at height / Electrical / Heavy lifting / etc.", "risk_level": "Medium", "mitigation": "Proper PPE, stable ladder, harness"}
  ],
  "required_ppe": ["Hard hat", "High-visibility vest", "Safety boots", "Gloves", "Eye protection"],
  "pre_job_checklist": [
    "Site inspection for trip and overhead hazards",
    "Verify power isolation before commencing work",
    "Tool condition inspection"
  ],
  "emergency_procedures": "Brief emergency response instruction in case of incident"
}
PROMPT;

        return $this->generateJson($prompt);
    }

    /**
     * Workflow 5: Interactive ERP Assistant Chat
     */
    public function chatWithERP(string $userMessage, array $conversationHistory = []): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Gemini API key is not configured. Please set GEMINI_API_KEY in your .env or Gemini Hub settings.',
            ];
        }

        try {
            $endpoint = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

            $systemPrompt = "You are NeoERP Assistant, a friendly, intelligent, and highly knowledgeable AI copilot built into the NeoERP system. "
                . "You help staff, managers, and administrators manage leads, customer enquiries, job schedules, quotations, health & safety compliance, and business operations. "
                . "Keep your answers helpful, concise, well-formatted with markdown or bullet points, and actionable.";

            $contents = [];

            // Add previous history turns if available
            foreach ($conversationHistory as $msg) {
                $role = ($msg['role'] ?? 'user') === 'user' ? 'user' : 'model';
                $text = $msg['text'] ?? $msg['content'] ?? '';
                if (!empty($text)) {
                    $contents[] = [
                        'role' => $role,
                        'parts' => [['text' => $text]]
                    ];
                }
            }

            // Append latest user message
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $userMessage]]
            ];

            $payload = [
                'contents' => $contents,
                'systemInstruction' => [
                    'parts' => [['text' => $systemPrompt]]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1500,
                ]
            ];

            $response = $this->httpClient($this->timeout)->post($endpoint, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $candidates = $data['candidates'] ?? [];
                $reply = $this->extractTextFromCandidates($candidates) ?: 'I could not generate a response.';
                return [
                    'success' => true,
                    'reply' => trim($reply),
                ];
            }

            $error = $response->json();
            return [
                'success' => false,
                'error' => $error['error']['message'] ?? 'API call failed',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
