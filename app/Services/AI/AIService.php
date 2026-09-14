<?php

namespace App\Services\AI;

use App\Models\AiUsageLog;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\DTOs\AIResponse;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\OpenAIProvider;
use Illuminate\Support\Facades\Log;
use Throwable;

class AIService
{
    protected AIConfig $config;
    protected AIProviderInterface $geminiProvider;
    protected AIProviderInterface $openAiProvider;

    public function __construct(
        AIConfig $config,
        AIProviderInterface $geminiProvider,
        AIProviderInterface $openAiProvider
    ) {
        $this->config = $config;
        $this->geminiProvider = $geminiProvider;
        $this->openAiProvider = $openAiProvider;
    }

    /**
     * Unified generation method with automatic failover, audit logging, and proactive daily caps.
     *
     * @param string $prompt
     * @param array{
     *     maxTokens?: int,
     *     temperature?: float,
     *     systemPrompt?: string,
     *     module?: string
     * } $options
     * @return AIResponse
     */
    public function generate(string $prompt, array $options = []): AIResponse
    {
        @set_time_limit(120);
        $module = $options['module'] ?? 'general';

        try {
            // 1. Check Gemini (Primary) Daily Cap
            $geminiCap = $this->config->getGeminiDailyCap();
            $geminiUsageToday = $this->getProviderUsageToday('gemini');

            if ($geminiUsageToday >= $geminiCap) {
                Log::info("AI Service: Gemini daily cap reached ({$geminiUsageToday}/{$geminiCap}). Proactively skipping to OpenAI fallback.");
                $this->recordUsage(
                    provider: 'gemini',
                    model: $this->config->getGeminiModel(),
                    tokensUsed: 0,
                    latencyMs: 0,
                    status: 'cap_skipped',
                    errorType: 'cap_exceeded',
                    errorMessage: "Daily cap of {$geminiCap} requests exceeded.",
                    module: $module
                );

                return $this->attemptFallback($prompt, $options, $module, 'Gemini daily cap reached');
            }

            // 2. Attempt Gemini (Primary)
            Log::info("AI Service: Attempting request via primary provider [gemini]...");
            $geminiResponse = $this->geminiProvider->generateText($prompt, $options);

            if ($geminiResponse->isSuccess()) {
                Log::info("AI Service: Request successfully served by [gemini]", [
                    'provider' => 'gemini',
                    'latency_ms' => $geminiResponse->latencyMs,
                    'tokens_used' => $geminiResponse->tokensUsed,
                    'module' => $module,
                ]);

                $this->recordUsage(
                    provider: 'gemini',
                    model: $this->config->getGeminiModel(),
                    tokensUsed: $geminiResponse->tokensUsed,
                    latencyMs: $geminiResponse->latencyMs,
                    status: 'success',
                    errorType: null,
                    errorMessage: null,
                    module: $module
                );

                return $geminiResponse;
            }

            // 3. Gemini Failed - Inspect Error
            $isAuthError = ($geminiResponse->errorType === 'authentication');
            if ($isAuthError) {
                Log::error("CRITICAL AI Authentication Error on primary provider [gemini]: {$geminiResponse->errorMessage}. " .
                    "Check GEMINI_API_KEY in .env. Skipping retries on Gemini and falling back to OpenAI immediately.");
            } else {
                Log::warning("AI Service: Primary provider [gemini] failed with [{$geminiResponse->errorType}]: {$geminiResponse->errorMessage}. " .
                    "Automatically falling back to secondary provider [openai].");
            }

            $this->recordUsage(
                provider: 'gemini',
                model: $this->config->getGeminiModel(),
                tokensUsed: 0,
                latencyMs: $geminiResponse->latencyMs,
                status: 'failed',
                errorType: $geminiResponse->errorType,
                errorMessage: $geminiResponse->errorMessage,
                module: $module
            );

            // 4. Fallback to OpenAI
            return $this->attemptFallback($prompt, $options, $module, $geminiResponse->errorMessage);
        } catch (Throwable $e) {
            Log::critical("Unexpected error in AIService orchestration: {$e->getMessage()}", [
                'exception' => $e,
            ]);

            return AIResponse::failure(
                errorMessage: 'AI assistant is temporarily unavailable.',
                providerUsed: 'none',
                errorType: 'orchestration_failure'
            );
        }
    }

    /**
     * Attempt the secondary fallback provider (OpenAI).
     */
    protected function attemptFallback(string $prompt, array $options, string $module, ?string $primaryFailureReason): AIResponse
    {
        $openAiCap = $this->config->getOpenAiDailyCap();
        $openAiUsageToday = $this->getProviderUsageToday('openai');

        if ($openAiUsageToday >= $openAiCap) {
            Log::warning("AI Service: OpenAI fallback daily cap reached ({$openAiUsageToday}/{$openAiCap}). Cannot fulfill request.");
            $this->recordUsage(
                provider: 'openai',
                model: $this->config->getOpenAiModel(),
                tokensUsed: 0,
                latencyMs: 0,
                status: 'cap_skipped',
                errorType: 'cap_exceeded',
                errorMessage: "Fallback daily cap of {$openAiCap} requests exceeded.",
                module: $module
            );

            return AIResponse::failure(
                errorMessage: 'AI assistant is temporarily unavailable.',
                providerUsed: 'none',
                errorType: 'cap_exceeded'
            );
        }

        Log::info("AI Service: Attempting request via fallback provider [openai]...");
        $openAiResponse = $this->openAiProvider->generateText($prompt, $options);

        if ($openAiResponse->isSuccess()) {
            Log::info("AI Service: Request successfully served by fallback [openai]", [
                'provider' => 'openai',
                'latency_ms' => $openAiResponse->latencyMs,
                'tokens_used' => $openAiResponse->tokensUsed,
                'module' => $module,
                'primary_failure_reason' => $primaryFailureReason,
            ]);

            $this->recordUsage(
                provider: 'openai',
                model: $this->config->getOpenAiModel(),
                tokensUsed: $openAiResponse->tokensUsed,
                latencyMs: $openAiResponse->latencyMs,
                status: 'success',
                errorType: null,
                errorMessage: null,
                module: $module
            );

            return $openAiResponse;
        }

        // Both providers failed
        Log::critical("AI Service: All AI providers failed to fulfill the request. Primary [gemini] error: {$primaryFailureReason}. Fallback [openai] error: {$openAiResponse->errorMessage}");

        $this->recordUsage(
            provider: 'openai',
            model: $this->config->getOpenAiModel(),
            tokensUsed: 0,
            latencyMs: $openAiResponse->latencyMs,
            status: 'failed',
            errorType: $openAiResponse->errorType,
            errorMessage: $openAiResponse->errorMessage,
            module: $module
        );

        return AIResponse::failure(
            errorMessage: 'AI assistant is temporarily unavailable.',
            providerUsed: 'none',
            errorType: 'all_providers_failed'
        );
    }

    /**
     * Get count of successful requests served by a provider today.
     */
    public function getProviderUsageToday(string $provider): int
    {
        try {
            return AiUsageLog::getTodayUsageCount($provider);
        } catch (Throwable $e) {
            Log::warning("Could not query AiUsageLog table: {$e->getMessage()}");
            return 0;
        }
    }

    /**
     * Record usage audit log in database.
     */
    protected function recordUsage(
        string $provider,
        ?string $model,
        ?int $tokensUsed,
        float $latencyMs,
        string $status,
        ?string $errorType,
        ?string $errorMessage,
        string $module
    ): void {
        try {
            AiUsageLog::create([
                'provider' => $provider,
                'model' => $model,
                'prompt_tokens' => null,
                'completion_tokens' => null,
                'total_tokens' => $tokensUsed,
                'latency_ms' => round($latencyMs, 2),
                'status' => $status,
                'error_type' => $errorType,
                'error_message' => $errorMessage ? mb_substr($errorMessage, 0, 1000) : null,
                'module' => $module,
            ]);
        } catch (Throwable $e) {
            Log::warning("Could not record AI usage log in DB: {$e->getMessage()}");
        }
    }

    /**
     * Static helper for convenience.
     */
    public static function make(): self
    {
        return app(self::class);
    }
}
