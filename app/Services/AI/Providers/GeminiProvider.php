<?php

namespace App\Services\AI\Providers;

use App\Services\AI\AIConfig;
use App\Services\AI\AIProvider;
use App\Services\AI\DTOs\AIResponse;
use Gemini;
use Gemini\Contracts\ClientContract as GeminiClientContract;
use Gemini\Data\GenerationConfig;
use Gemini\Data\Content;
use Gemini\Exceptions\ErrorException as GeminiErrorException;
use Gemini\Exceptions\TransporterException as GeminiTransporterException;
use GuzzleHttp\Client as GuzzleClient;
use Throwable;

class GeminiProvider extends AIProvider
{
    protected AIConfig $config;
    protected ?GeminiClientContract $client = null;

    /**
     * GeminiProvider constructor.
     *
     * @param AIConfig $config
     * @param GeminiClientContract|null $client Optional client for dependency injection / mocking
     */
    public function __construct(AIConfig $config, ?GeminiClientContract $client = null)
    {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * Get or initialize the Gemini client with timeout.
     */
    public function getClient(): GeminiClientContract
    {
        if ($this->client === null) {
            $timeoutSeconds = max(1.0, $this->config->getRequestTimeoutMs() / 1000.0);

            $guzzle = new GuzzleClient([
                'timeout' => $timeoutSeconds,
                'connect_timeout' => min(5.0, $timeoutSeconds),
            ]);

            $this->client = Gemini::factory()
                ->withApiKey($this->config->getGeminiApiKey())
                ->withHttpClient($guzzle)
                ->make();
        }

        return $this->client;
    }

    /**
     * Set a custom Gemini client (useful for testing/mocking).
     */
    public function setClient(GeminiClientContract $client): void
    {
        $this->client = $client;
    }

    public function getName(): string
    {
        return 'gemini';
    }

    /**
     * {@inheritdoc}
     */
    public function generateText(string $prompt, array $options = []): AIResponse
    {
        $startTime = microtime(true);
        $modelName = $this->config->getGeminiModel();

        try {
            $client = $this->getClient();
            $model = $client->generativeModel(model: $modelName);

            // Configure generation parameters if provided
            if (isset($options['maxTokens']) || isset($options['temperature'])) {
                $generationConfig = new GenerationConfig(
                    maxOutputTokens: $options['maxTokens'] ?? null,
                    temperature: $options['temperature'] ?? null
                );
                $model = $model->withGenerationConfig($generationConfig);
            }

            // Configure system prompt if provided
            if (!empty($options['systemPrompt'])) {
                $model = $model->withSystemInstruction(
                    Content::parse((string) $options['systemPrompt'])
                );
            }

            $response = $model->generateContent($prompt);
            $latencyMs = (microtime(true) - $startTime) * 1000.0;

            $text = $response->text();
            $tokensUsed = $response->usageMetadata?->totalTokenCount ?? null;

            $this->recordSuccess();

            return AIResponse::success(
                text: $text,
                providerUsed: $this->getName(),
                tokensUsed: $tokensUsed,
                latencyMs: $latencyMs
            );
        } catch (Throwable $e) {
            $latencyMs = (microtime(true) - $startTime) * 1000.0;
            $errorType = $this->classifyError($e);

            return AIResponse::failure(
                errorMessage: $e->getMessage(),
                providerUsed: $this->getName(),
                errorType: $errorType,
                latencyMs: $latencyMs
            );
        }
    }

    /**
     * Classify exception into standard error types:
     * - 'rate_limit': Quota/rate-limit errors
     * - 'timeout': Network/connect/read timeout errors
     * - 'authentication': Invalid API key, forbidden, unauthorized
     * - 'generic': Other errors
     */
    public function classifyError(Throwable $e): string
    {
        $message = strtolower($e->getMessage());

        if ($e instanceof GeminiErrorException) {
            $code = $e->getErrorCode();
            $status = strtoupper($e->getErrorStatus());

            if ($code === 429 || $status === 'RESOURCE_EXHAUSTED' || str_contains($message, 'quota') || str_contains($message, 'rate limit')) {
                return 'rate_limit';
            }

            if ($code === 401 || $code === 403 || $status === 'PERMISSION_DENIED' || $status === 'UNAUTHENTICATED' || str_contains($message, 'api key') || str_contains($message, 'unauthorized')) {
                return 'authentication';
            }
        }

        if ($e instanceof GeminiTransporterException) {
            if (str_contains($message, 'timed out') || str_contains($message, 'timeout') || str_contains($message, 'curl error 28')) {
                return 'timeout';
            }
        }

        if (str_contains($message, 'timed out') || str_contains($message, 'timeout') || str_contains($message, 'curl error 28')) {
            return 'timeout';
        }

        if (str_contains($message, '429') || str_contains($message, 'resource_exhausted') || str_contains($message, 'quota')) {
            return 'rate_limit';
        }

        if (str_contains($message, '401') || str_contains($message, '403') || str_contains($message, 'permission_denied') || str_contains($message, 'api_key_invalid')) {
            return 'authentication';
        }

        return 'generic';
    }

    /**
     * Lightweight health check for Gemini.
     */
    protected function performHealthCheck(): bool
    {
        try {
            // A minimal 1-token test prompt or cached success check
            $response = $this->generateText('Ping', ['maxTokens' => 1]);
            return $response->isSuccess();
        } catch (Throwable) {
            return false;
        }
    }
}
