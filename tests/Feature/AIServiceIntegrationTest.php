<?php

namespace Tests\Feature;

use App\Models\AiUsageLog;
use App\Services\AI\AIConfig;
use App\Services\AI\AIService;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\DTOs\AIResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class AIServiceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected AIConfig $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = new AIConfig([
            'primary_provider' => 'gemini',
            'fallback_provider' => 'openai',
            'timeout_ms' => 15000,
            'max_retries' => 2,
            'gemini' => [
                'api_key' => 'mock-gemini-key',
                'model' => 'gemini-1.5-flash',
                'daily_cap' => 5,
            ],
            'openai' => [
                'api_key' => 'mock-openai-key',
                'model' => 'gpt-4o-mini',
                'daily_cap' => 5,
            ],
        ]);
    }

    public function test_gemini_primary_success(): void
    {
        $mockGemini = Mockery::mock(AIProviderInterface::class);
        $mockOpenAI = Mockery::mock(AIProviderInterface::class);

        $mockGemini->shouldReceive('getName')->andReturn('gemini');
        $mockOpenAI->shouldReceive('getName')->andReturn('openai');

        $mockGemini->shouldReceive('generateText')
            ->once()
            ->with('Summarize ERP invoice', ['module' => 'invoices'])
            ->andReturn(AIResponse::success('Invoice summary from Gemini', 'gemini', 25, 350.0));

        // OpenAI should NEVER be called when Gemini succeeds
        $mockOpenAI->shouldNotReceive('generateText');

        $aiService = new AIService($this->config, $mockGemini, $mockOpenAI);
        $response = $aiService->generate('Summarize ERP invoice', ['module' => 'invoices']);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals('gemini', $response->providerUsed);
        $this->assertEquals('Invoice summary from Gemini', $response->text);

        // Check DB audit log
        $this->assertDatabaseHas('ai_usage_logs', [
            'provider' => 'gemini',
            'status' => 'success',
            'module' => 'invoices',
        ]);
    }

    public function test_gemini_transient_failure_falls_back_to_openai_success(): void
    {
        $mockGemini = Mockery::mock(AIProviderInterface::class);
        $mockOpenAI = Mockery::mock(AIProviderInterface::class);

        $mockGemini->shouldReceive('getName')->andReturn('gemini');
        $mockOpenAI->shouldReceive('getName')->andReturn('openai');

        // Gemini fails with 429 quota/rate limit
        $mockGemini->shouldReceive('generateText')
            ->once()
            ->andReturn(AIResponse::failure('Quota exceeded', 'gemini', 'rate_limit', 200.0));

        // Fallback OpenAI is called and succeeds
        $mockOpenAI->shouldReceive('generateText')
            ->once()
            ->with('Draft response', ['module' => 'leads'])
            ->andReturn(AIResponse::success('Lead response from OpenAI', 'openai', 30, 450.0));

        $aiService = new AIService($this->config, $mockGemini, $mockOpenAI);
        $response = $aiService->generate('Draft response', ['module' => 'leads']);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals('openai', $response->providerUsed);
        $this->assertEquals('Lead response from OpenAI', $response->text);

        // Verify both attempts are recorded in DB
        $this->assertDatabaseHas('ai_usage_logs', [
            'provider' => 'gemini',
            'status' => 'failed',
            'error_type' => 'rate_limit',
        ]);
        $this->assertDatabaseHas('ai_usage_logs', [
            'provider' => 'openai',
            'status' => 'success',
        ]);
    }

    public function test_gemini_auth_failure_falls_back_to_openai_without_gemini_retry(): void
    {
        $mockGemini = Mockery::mock(AIProviderInterface::class);
        $mockOpenAI = Mockery::mock(AIProviderInterface::class);

        $mockGemini->shouldReceive('getName')->andReturn('gemini');
        $mockOpenAI->shouldReceive('getName')->andReturn('openai');

        // Gemini fails with auth error
        $mockGemini->shouldReceive('generateText')
            ->once()
            ->andReturn(AIResponse::failure('API key not valid', 'gemini', 'authentication', 50.0));

        // OpenAI called and succeeds
        $mockOpenAI->shouldReceive('generateText')
            ->once()
            ->andReturn(AIResponse::success('OpenAI answered', 'openai', 20, 300.0));

        $aiService = new AIService($this->config, $mockGemini, $mockOpenAI);
        $response = $aiService->generate('Test prompt');

        $this->assertTrue($response->isSuccess());
        $this->assertEquals('openai', $response->providerUsed);
    }

    public function test_both_providers_fail_returns_graceful_user_safe_error(): void
    {
        $mockGemini = Mockery::mock(AIProviderInterface::class);
        $mockOpenAI = Mockery::mock(AIProviderInterface::class);

        $mockGemini->shouldReceive('getName')->andReturn('gemini');
        $mockOpenAI->shouldReceive('getName')->andReturn('openai');

        // Gemini fails
        $mockGemini->shouldReceive('generateText')
            ->once()
            ->andReturn(AIResponse::failure('Gemini 500 server error', 'gemini', 'generic', 100.0));

        // OpenAI also fails
        $mockOpenAI->shouldReceive('generateText')
            ->once()
            ->andReturn(AIResponse::failure('OpenAI 500 server error', 'openai', 'generic', 120.0));

        $aiService = new AIService($this->config, $mockGemini, $mockOpenAI);
        $response = $aiService->generate('Test prompt');

        $this->assertFalse($response->isSuccess());
        $this->assertEquals('none', $response->providerUsed);
        $this->assertEquals('all_providers_failed', $response->errorType);
        $this->assertEquals('AI assistant is temporarily unavailable.', $response->errorMessage);
    }

    public function test_proactive_daily_cap_skips_primary_provider(): void
    {
        // Populate 5 successful logs today for gemini (matching the cap of 5)
        for ($i = 0; $i < 5; $i++) {
            AiUsageLog::create([
                'provider' => 'gemini',
                'model' => 'gemini-1.5-flash',
                'total_tokens' => 10,
                'latency_ms' => 100,
                'status' => 'success',
            ]);
        }

        $mockGemini = Mockery::mock(AIProviderInterface::class);
        $mockOpenAI = Mockery::mock(AIProviderInterface::class);

        $mockGemini->shouldReceive('getName')->andReturn('gemini');
        $mockOpenAI->shouldReceive('getName')->andReturn('openai');

        // Gemini should NOT be called at all because cap was reached!
        $mockGemini->shouldNotReceive('generateText');

        // OpenAI should be called directly
        $mockOpenAI->shouldReceive('generateText')
            ->once()
            ->andReturn(AIResponse::success('Served by OpenAI fallback after cap skip', 'openai', 20, 200.0));

        $aiService = new AIService($this->config, $mockGemini, $mockOpenAI);
        $response = $aiService->generate('Test prompt');

        $this->assertTrue($response->isSuccess());
        $this->assertEquals('openai', $response->providerUsed);

        // Check cap_skipped log entry exists
        $this->assertDatabaseHas('ai_usage_logs', [
            'provider' => 'gemini',
            'status' => 'cap_skipped',
            'error_type' => 'cap_exceeded',
        ]);
    }
}
