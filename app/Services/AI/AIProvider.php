<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\DTOs\AIResponse;

abstract class AIProvider implements AIProviderInterface
{
    /**
     * Cache duration in seconds for health status.
     */
    protected int $healthCheckTtlSeconds = 60;

    /**
     * Last successful health check timestamp.
     */
    protected ?int $lastAvailableTimestamp = null;

    /**
     * {@inheritdoc}
     */
    abstract public function generateText(string $prompt, array $options = []): AIResponse;

    /**
     * {@inheritdoc}
     */
    abstract public function getName(): string;

    /**
     * {@inheritdoc}
     */
    public function isAvailable(): bool
    {
        if ($this->lastAvailableTimestamp !== null && (time() - $this->lastAvailableTimestamp) < $this->healthCheckTtlSeconds) {
            return true;
        }

        return $this->performHealthCheck();
    }

    /**
     * Perform the actual provider-specific health check.
     */
    abstract protected function performHealthCheck(): bool;

    /**
     * Mark that provider responded successfully to refresh availability cache.
     */
    protected function recordSuccess(): void
    {
        $this->lastAvailableTimestamp = time();
    }
}
