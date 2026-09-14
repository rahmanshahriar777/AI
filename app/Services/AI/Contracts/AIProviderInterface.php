<?php

namespace App\Services\AI\Contracts;

use App\Services\AI\DTOs\AIResponse;

interface AIProviderInterface
{
    /**
     * Generate text from the AI provider.
     *
     * @param string $prompt The user prompt.
     * @param array{
     *     maxTokens?: int,
     *     temperature?: float,
     *     systemPrompt?: string
     * } $options Additional generation options.
     * @return AIResponse
     */
    public function generateText(string $prompt, array $options = []): AIResponse;

    /**
     * Perform a lightweight health check or cached availability check.
     *
     * @return bool
     */
    public function isAvailable(): bool;

    /**
     * Get the identifier name of the provider (e.g. 'gemini', 'openai').
     *
     * @return string
     */
    public function getName(): string;
}
