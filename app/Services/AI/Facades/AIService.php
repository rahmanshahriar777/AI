<?php

namespace App\Services\AI\Facades;

use App\Services\AI\AIService as ConcreteAIService;
use App\Services\AI\DTOs\AIResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static AIResponse generate(string $prompt, array $options = [])
 * @method static int getProviderUsageToday(string $provider)
 *
 * @see \App\Services\AI\AIService
 */
class AIService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ConcreteAIService::class;
    }
}
