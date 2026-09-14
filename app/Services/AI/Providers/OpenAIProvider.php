<?php

namespace App\Services\AI\Providers;

use App\Services\AI\AIConfig;
use App\Services\AI\AIProvider;
use App\Services\AI\DTOs\AIResponse;
use GuzzleHttp\Client as GuzzleClient;
use OpenAI;
use OpenAI\Contracts\ClientContract as OpenAIClientContract;
use OpenAI\Exceptions\ErrorException as OpenAIErrorException;
use OpenAI\Exceptions\RateLimitException as OpenAIRateLimitException;
use OpenAI\Exceptions\ServerException as OpenAIServerException;
use OpenAI\Exceptions\TransporterException as OpenAITransporterException;
use Throwable;

class OpenAIProvider extends AIProvider
{
    protected AIConfig $config;
    protected ?OpenAIClientContract $client = null;

    /**
     * OpenAIProvider constructor.
     *
     * @param AIConfig $config
     * @param OpenAIClientContract|null $client Optional client for dependency injection / mocking
     */
    public function __construct(AIConfig $config, ?OpenAIClientContract $client = null)
    {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * Get or initialize the OpenAI client with timeout.
     */
    public function getClient(): OpenAIClientContract
    {
        if ($this->client === null) {
            $timeoutSeconds = max(1.0, $this->config->getRequestTimeoutMs() / 1000.0);

            $guzzle = new GuzzleClient([
                'timeout' => $timeoutSeconds,
                'connect_timeout' => min(5.0, $timeoutSeconds),
            ]);

            $this->client = OpenAI::factory()
                ->withApiKey($this->config->getOpenAiApiKey())
                ->withHttpClient($guzzle)
                ->make();
        }

        return $this->client;
    }

    /**
     * Set a custom OpenAI client (useful for testing/mocking).
     */
    public function setClient(OpenAIClientContract $client): void
    {
        $this->client = $client;
    }

    public function getName(): string
    {
        return 'openai';
    }

    /**
     * {@inheritdoc}
     */
    public function generateText(string $prompt, array $options = []): AIResponse
    {
        $startTime = microtime(true);
        $modelName = $this->config->getOpenAiModel();
        $maxRetries = max(0, $this->config->getMaxRetries());

        $messages = [];
        if (!empty($options['systemPrompt'])) {
            $messages[] = [
                'role' => 'system',
                'content' => (string) $options['systemPrompt'],
            ];
        }
        $messages[] = [
            'role' => 'user',
            'content' => $prompt,
        ];

        $payload = [
            'model' => $modelName,
            'messages' => $messages,
        ];

        if (isset($options['maxTokens'])) {
            $payload['max_tokens'] = (int) $options['maxTokens'];
        }
        if (isset($options['temperature'])) {
            $payload['temperature'] = (float) $options['temperature'];
        }

        $attempt = 0;
        $lastException = null;

        while ($attempt <= $maxRetries) {
            try {
                $client = $this->getClient();
                $response = $client->chat()->create($payload);

                $latencyMs = (microtime(true) - $startTime) * 1000.0;
                $text = $response->choices[0]->message->content ?? '';
                $tokensUsed = $response->usage?->totalTokens ?? null;

                $this->recordSuccess();

                return AIResponse::success(
                    text: $text,
                    providerUsed: $this->getName(),
                    tokensUsed: $tokensUsed,
                    latencyMs: $latencyMs
                );
            } catch (Throwable $e) {
                $lastException = $e;
                $errorType = $this->classifyError($e);

                // Authentication and rate limit errors should not be retried immediately
                if ($errorType === 'authentication' || $errorType === 'rate_limit' || $attempt === $maxRetries) {
                    break;
                }

                // If transient timeout or 5xx, brief backoff before retrying
                $attempt++;
                usleep((int) (min(1000, 100 * (2 ** $attempt)) * 1000));
            }
        }

        $latencyMs = (microtime(true) - $startTime) * 1000.0;
        $errorType = $this->classifyError($lastException);

        return AIResponse::failure(
            errorMessage: $lastException ? $lastException->getMessage() : 'OpenAI request failed',
            providerUsed: $this->getName(),
            errorType: $errorType,
            latencyMs: $latencyMs
        );
    }

    /**
     * Classify exception into standard error types:
     * - 'rate_limit': Quota/rate-limit errors
     * - 'timeout': Network/connect/read timeout errors
     * - 'authentication': Invalid API key, forbidden, unauthorized
     * - 'generic': Other errors
     */
    public function classifyError(?Throwable $e): string
    {
        if ($e === null) {
            return 'generic';
        }

        $message = strtolower($e->getMessage());

        if ($e instanceof OpenAIRateLimitException) {
            return 'rate_limit';
        }

        if ($e instanceof OpenAITransporterException) {
            if (str_contains($message, 'timed out') || str_contains($message, 'timeout') || str_contains($message, 'curl error 28')) {
                return 'timeout';
            }
        }

        if (str_contains($message, 'timed out') || str_contains($message, 'timeout') || str_contains($message, 'curl error 28')) {
            return 'timeout';
        }

        if (str_contains($message, 'rate limit') || str_contains($message, 'quota') || str_contains($message, '429')) {
            return 'rate_limit';
        }

        if (str_contains($message, 'invalid_api_key') || str_contains($message, 'incorrect api key') || str_contains($message, '401') || str_contains($message, 'unauthorized')) {
            return 'authentication';
        }

        return 'generic';
    }

    /**
     * Lightweight health check for OpenAI.
     */
    protected function performHealthCheck(): bool
    {
        try {
            $response = $this->generateText('Ping', ['maxTokens' => 1]);
            return $response->isSuccess();
        } catch (Throwable) {
            return false;
        }
    }
}
