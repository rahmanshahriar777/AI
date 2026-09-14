<?php

namespace App\Services\AI\DTOs;

class AIResponse
{
    public function __construct(
        public readonly ?string $text,
        public readonly string $providerUsed,
        public readonly ?int $tokensUsed,
        public readonly float $latencyMs,
        public readonly bool $success,
        public readonly ?string $errorMessage = null,
        public readonly ?string $errorType = null
    ) {
    }

    public static function success(
        string $text,
        string $providerUsed,
        ?int $tokensUsed = null,
        float $latencyMs = 0.0
    ): self {
        return new self(
            text: $text,
            providerUsed: $providerUsed,
            tokensUsed: $tokensUsed,
            latencyMs: round($latencyMs, 2),
            success: true,
            errorMessage: null,
            errorType: null
        );
    }

    public static function failure(
        string $errorMessage,
        string $providerUsed,
        ?string $errorType = 'generic',
        float $latencyMs = 0.0,
        ?int $tokensUsed = null
    ): self {
        return new self(
            text: null,
            providerUsed: $providerUsed,
            tokensUsed: $tokensUsed,
            latencyMs: round($latencyMs, 2),
            success: false,
            errorMessage: $errorMessage,
            errorType: $errorType
        );
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'providerUsed' => $this->providerUsed,
            'tokensUsed' => $this->tokensUsed,
            'latencyMs' => $this->latencyMs,
            'success' => $this->success,
            'errorMessage' => $this->errorMessage,
            'errorType' => $this->errorType,
        ];
    }
}
