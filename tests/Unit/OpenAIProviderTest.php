<?php

namespace Tests\Unit;

use App\Services\AI\AIConfig;
use App\Services\AI\Providers\OpenAIProvider;
use OpenAI\Exceptions\TransporterException as OpenAITransporterException;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Testing\ClientFake;
use PHPUnit\Framework\TestCase;

class OpenAIProviderTest extends TestCase
{
    protected AIConfig $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = new AIConfig([
            'primary_provider' => 'gemini',
            'fallback_provider' => 'openai',
            'timeout_ms' => 15000,
            'max_retries' => 1,
            'gemini' => [
                'api_key' => 'mock-gemini-key',
                'model' => 'gemini-1.5-flash',
                'daily_cap' => 100,
            ],
            'openai' => [
                'api_key' => 'mock-openai-key',
                'model' => 'gpt-4o-mini',
                'daily_cap' => 50,
            ],
        ]);
    }

    public function test_openai_success_path(): void
    {
        $fakeResponse = CreateResponse::fake([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Hello from OpenAI!'
                    ]
                ]
            ],
            'usage' => [
                'prompt_tokens' => 6,
                'completion_tokens' => 5,
                'total_tokens' => 11,
            ]
        ]);

        $clientFake = new ClientFake([$fakeResponse]);
        $provider = new OpenAIProvider($this->config, $clientFake);

        $response = $provider->generateText('Test prompt');

        $this->assertTrue($response->isSuccess());
        $this->assertEquals('Hello from OpenAI!', $response->text);
        $this->assertEquals('openai', $response->providerUsed);
        $this->assertEquals(11, $response->tokensUsed);
        $this->assertNull($response->errorMessage);
        $this->assertNull($response->errorType);
    }

    public function test_openai_rate_limit_error_classification(): void
    {
        $clientFake = new ClientFake([
            new \Exception('Rate limit reached: 429 Too Many Requests. Quota exceeded.')
        ]);

        $provider = new OpenAIProvider($this->config, $clientFake);
        $response = $provider->generateText('Test prompt');

        $this->assertFalse($response->isSuccess());
        $this->assertEquals('rate_limit', $response->errorType);
        $this->assertEquals('openai', $response->providerUsed);
        $this->assertStringContainsString('Rate limit reached', $response->errorMessage);
    }

    public function test_openai_auth_error_classification(): void
    {
        $clientFake = new ClientFake([
            new \Exception('Incorrect API key provided: sk-invalid. You can find your API key at https://platform.openai.com/account/api-keys.')
        ]);

        $provider = new OpenAIProvider($this->config, $clientFake);
        $response = $provider->generateText('Test prompt');

        $this->assertFalse($response->isSuccess());
        $this->assertEquals('authentication', $response->errorType);
        $this->assertEquals('openai', $response->providerUsed);
    }

    public function test_openai_timeout_error_classification(): void
    {
        $clientFake = new ClientFake([
            new OpenAITransporterException(
                new \GuzzleHttp\Exception\ConnectException(
                    'cURL error 28: Operation timed out after 15000 milliseconds',
                    new \GuzzleHttp\Psr7\Request('POST', 'https://api.openai.com')
                )
            ),
            // Max retries is 1, so second exception for the retry attempt
            new OpenAITransporterException(
                new \GuzzleHttp\Exception\ConnectException(
                    'cURL error 28: Operation timed out after 15000 milliseconds',
                    new \GuzzleHttp\Psr7\Request('POST', 'https://api.openai.com')
                )
            )
        ]);

        $provider = new OpenAIProvider($this->config, $clientFake);
        $response = $provider->generateText('Test prompt');

        $this->assertFalse($response->isSuccess());
        $this->assertEquals('timeout', $response->errorType);
        $this->assertEquals('openai', $response->providerUsed);
    }
}
