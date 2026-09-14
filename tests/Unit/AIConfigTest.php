<?php

namespace Tests\Unit;

use App\Services\AI\AIConfig;
use App\Services\AI\Exceptions\MissingAIConfigurationException;
use PHPUnit\Framework\TestCase;

class AIConfigTest extends TestCase
{
    public function test_loads_valid_configuration_successfully(): void
    {
        $config = new AIConfig([
            'primary_provider' => 'gemini',
            'fallback_provider' => 'openai',
            'timeout_ms' => 15000,
            'max_retries' => 2,
            'gemini' => [
                'api_key' => 'test-gemini-key',
                'model' => 'gemini-1.5-flash',
                'daily_cap' => 100,
            ],
            'openai' => [
                'api_key' => 'test-openai-key',
                'model' => 'gpt-4o-mini',
                'daily_cap' => 50,
            ],
        ]);

        $this->assertEquals('test-gemini-key', $config->getGeminiApiKey());
        $this->assertEquals('test-openai-key', $config->getOpenAiApiKey());
        $this->assertEquals('gemini', $config->getPrimaryProvider());
        $this->assertEquals('openai', $config->getFallbackProvider());
        $this->assertEquals(15000, $config->getRequestTimeoutMs());
        $this->assertEquals(2, $config->getMaxRetries());
        $this->assertEquals('gemini-1.5-flash', $config->getGeminiModel());
        $this->assertEquals('gpt-4o-mini', $config->getOpenAiModel());
        $this->assertEquals(100, $config->getGeminiDailyCap());
        $this->assertEquals(50, $config->getOpenAiDailyCap());
    }

    public function test_throws_exception_when_keys_are_missing(): void
    {
        $this->expectException(MissingAIConfigurationException::class);
        $this->expectExceptionMessage('GEMINI_API_KEY');

        new AIConfig([
            'primary_provider' => 'gemini',
            'fallback_provider' => 'openai',
            'timeout_ms' => 15000,
            'max_retries' => 2,
            'gemini' => [
                'api_key' => '', // Missing!
                'model' => 'gemini-1.5-flash',
            ],
            'openai' => [
                'api_key' => 'test-openai-key',
                'model' => 'gpt-4o-mini',
            ],
        ]);
    }
}
