# NeoERP

A complete ERP system for small and mid level business.

---

## AI Provider Integration & Failover Architecture

NeoERP features a resilient, dual-provider AI integration architecture designed for enterprise reliability, high availability, and proactive cost control.

```
ERP Application (Controllers / UI)
          │
          ▼
      AIService (Facade / Orchestration Engine)
          │
          ├─► GeminiProvider (Primary: Google Gemini 1.5 Flash)
          │
          └─► OpenAIProvider (Secondary Fallback: OpenAI GPT-4o Mini)
```

### Architecture Highlights
1. **Primary Provider (Gemini)**: All requests default to Google Gemini using the free-tier eligible `gemini-1.5-flash` model.
2. **Automatic Failover (OpenAI)**: If Gemini experiences quota exhaustion (HTTP 429), timeout, or 5xx server errors, `AIService` automatically and seamlessly retries with OpenAI (`gpt-4o-mini`).
3. **Smart Auth Error Routing**: If Gemini encounters an authentication error (invalid API key / forbidden), `AIService` logs a distinct critical error and falls back immediately to OpenAI without redundant retries.
4. **Graceful User-Safe Degradation**: If all providers are exhausted or unavailable, the system returns a friendly `"AI assistant is temporarily unavailable."` message without unhandled exceptions crashing ERP workflows.
5. **Admin Cost & Telemetry Indicator**: Non-admin users see only the AI assistant response. Users with `admin` or `super_admin` roles see which provider answered and the latency in milliseconds.

---

## Environment & Secrets Configuration

All AI credentials are strictly loaded via environment variables and never hardcoded in source code.

Add the following keys to your `.env` file:

```env
# AI Primary Provider (Google Gemini)
GEMINI_API_KEY=your_gemini_api_key_here
AI_GEMINI_MODEL=gemini-1.5-flash
AI_DAILY_CAP_GEMINI=100

# AI Fallback Provider (OpenAI)
OPENAI_API_KEY=your_openai_api_key_here
AI_FALLBACK_MODEL=gpt-4o-mini
AI_DAILY_CAP_OPENAI=50

# Orchestration & Guardrails
AI_PRIMARY_PROVIDER=gemini
AI_FALLBACK_PROVIDER=openai
AI_REQUEST_TIMEOUT_MS=15000
AI_MAX_RETRIES=2
```

A template with placeholder values is available in `.env.example`.

---

## Monitoring & Cost Safeguards

### 1. Database Usage Audit Log
Every AI request (success, failure, or cap skip) is logged in the `ai_usage_logs` database table with:
- `provider` (`gemini`, `openai`, `none`)
- `model` (e.g. `gemini-1.5-flash`, `gpt-4o-mini`)
- `total_tokens`
- `latency_ms`
- `status` (`success`, `failed`, `cap_skipped`)
- `error_type` (`rate_limit`, `timeout`, `authentication`, `cap_exceeded`, `generic`)
- `module` (`lead_summarization`, `enquiry_summarization`, `erp_assistant`, etc.)
- `created_at` timestamp

### 2. Proactive Daily Request Caps
To prevent runaway costs:
- **`AI_DAILY_CAP_GEMINI`**: Daily request limit for Gemini (default: `100`).
- **`AI_DAILY_CAP_OPENAI`**: Daily request limit for OpenAI fallback (default: `50`).

When Gemini hits its daily cap, `AIService` proactively bypasses Gemini and routes calls directly to OpenAI, preventing unnecessary failed API calls.

### 3. How to Adjust Caps
To modify the daily cap limits, update the corresponding variables in your `.env` file and refresh the config cache:
```bash
# In .env:
AI_DAILY_CAP_GEMINI=200
AI_DAILY_CAP_OPENAI=100

# Clear config cache:
php artisan config:clear
```

### 4. Admin Telemetry API
Authenticated administrators can query real-time usage metrics and cap utilization:
```http
GET /ai/usage-stats
```
Response:
```json
{
  "gemini": {
    "today_requests": 14,
    "daily_cap": 100
  },
  "openai": {
    "today_requests": 2,
    "daily_cap": 50
  },
  "recent_logs": [...]
}
```

---

## Running AI Tests

To execute the AI test suite:
```bash
# Run all AI unit and feature tests:
php artisan test --filter=AI

# Run specific test suites:
php artisan test tests/Unit/AIConfigTest.php
php artisan test tests/Unit/GeminiProviderTest.php
php artisan test tests/Unit/OpenAIProviderTest.php
php artisan test tests/Feature/AIServiceIntegrationTest.php
php artisan test tests/Feature/AiAssistantControllerTest.php
```
