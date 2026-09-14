<?php

namespace App\Providers;

use App\Services\AI\AIConfig;
use App\Services\AI\AIService;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\OpenAIProvider;
use Illuminate\Support\ServiceProvider;

class AiServiceProvider extends ServiceProvider
{
    /**
     * Register AI services.
     */
    public function register(): void
    {
        $this->app->singleton(AIConfig::class, function () {
            return new AIConfig();
        });

        $this->app->singleton(GeminiProvider::class, function ($app) {
            return new GeminiProvider($app->make(AIConfig::class));
        });

        $this->app->singleton(OpenAIProvider::class, function ($app) {
            return new OpenAIProvider($app->make(AIConfig::class));
        });

        $this->app->singleton(AIService::class, function ($app) {
            return new AIService(
                $app->make(AIConfig::class),
                $app->make(GeminiProvider::class),
                $app->make(OpenAIProvider::class)
            );
        });
    }

    /**
     * Bootstrap AI services.
     */
    public function boot(): void
    {
        // AI configuration is validated on resolution of AIConfig
    }
}
