<?php

namespace App\Services\AI;

use App\Services\AI\Exceptions\MissingAIConfigurationException;

class AIConfig
{
    protected string $geminiApiKey;
    protected string $openAiApiKey;
    protected string $primaryProvider;
    protected string $fallbackProvider;
    protected int $requestTimeoutMs;
    protected int $maxRetries;
    protected string $geminiModel;
    protected string $openAiModel;
    protected int $geminiDailyCap;
    protected int $openAiDailyCap;

    /**
     * AIConfig constructor.
     *
     * @param array<string, mixed>|null $config Optional config array for testing/overrides
     * @throws MissingAIConfigurationException
     */
    public function __construct(?array $config = null)
    {
        $this->loadAndValidate($config);
    }

    /**
     * Load and validate all AI configuration values.
     *
     * @param array<string, mixed>|null $config
     * @throws MissingAIConfigurationException
     */
    public function loadAndValidate(?array $config = null): void
    {
        $rawConfig = $config ?? config('ai', []);

        $geminiKey = trim((string) ($rawConfig['gemini']['api_key'] ?? env('GEMINI_API_KEY', '')));
        $openAiKey = trim((string) ($rawConfig['openai']['api_key'] ?? env('OPENAI_API_KEY', '')));
        $primary = trim((string) ($rawConfig['primary_provider'] ?? env('AI_PRIMARY_PROVIDER', '')));
        $fallback = trim((string) ($rawConfig['fallback_provider'] ?? env('AI_FALLBACK_PROVIDER', '')));
        $timeoutRaw = $rawConfig['timeout_ms'] ?? env('AI_REQUEST_TIMEOUT_MS');
        $retriesRaw = $rawConfig['max_retries'] ?? env('AI_MAX_RETRIES');

        $missing = [];

        if (empty($geminiKey)) {
            $missing[] = 'GEMINI_API_KEY';
        }
        if (empty($openAiKey)) {
            $missing[] = 'OPENAI_API_KEY';
        }
        if (empty($primary)) {
            $missing[] = 'AI_PRIMARY_PROVIDER';
        }
        if (empty($fallback)) {
            $missing[] = 'AI_FALLBACK_PROVIDER';
        }
        if ($timeoutRaw === null || $timeoutRaw === '') {
            $missing[] = 'AI_REQUEST_TIMEOUT_MS';
        }
        if ($retriesRaw === null || $retriesRaw === '') {
            $missing[] = 'AI_MAX_RETRIES';
        }

        if (!empty($missing)) {
            throw MissingAIConfigurationException::forMissingKeys($missing);
        }

        $this->geminiApiKey = $geminiKey;
        $this->openAiApiKey = $openAiKey;
        $this->primaryProvider = strtolower($primary);
        $this->fallbackProvider = strtolower($fallback);
        $this->requestTimeoutMs = (int) $timeoutRaw;
        $this->maxRetries = (int) $retriesRaw;

        $this->geminiModel = (string) ($rawConfig['gemini']['model'] ?? env('AI_GEMINI_MODEL', 'gemini-1.5-flash'));
        $this->openAiModel = (string) ($rawConfig['openai']['model'] ?? env('AI_FALLBACK_MODEL', 'gpt-4o-mini'));
        $this->geminiDailyCap = (int) ($rawConfig['gemini']['daily_cap'] ?? env('AI_DAILY_CAP_GEMINI', 100));
        $this->openAiDailyCap = (int) ($rawConfig['openai']['daily_cap'] ?? env('AI_DAILY_CAP_OPENAI', 50));
    }

    public function getGeminiApiKey(): string
    {
        return $this->geminiApiKey;
    }

    public function getOpenAiApiKey(): string
    {
        return $this->openAiApiKey;
    }

    public function getPrimaryProvider(): string
    {
        return $this->primaryProvider;
    }

    public function getFallbackProvider(): string
    {
        return $this->fallbackProvider;
    }

    public function getRequestTimeoutMs(): int
    {
        return $this->requestTimeoutMs;
    }

    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    public function getGeminiModel(): string
    {
        return $this->geminiModel;
    }

    public function getOpenAiModel(): string
    {
        return $this->openAiModel;
    }

    public function getGeminiDailyCap(): int
    {
        return $this->geminiDailyCap;
    }

    public function getOpenAiDailyCap(): int
    {
        return $this->openAiDailyCap;
    }
}
