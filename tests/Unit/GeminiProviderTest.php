<?php

namespace Tests\Unit;

use App\Services\AI\AIConfig;
use App\Services\AI\Providers\GeminiProvider;
use Gemini\Exceptions\ErrorException as GeminiErrorException;
use Gemini\Exceptions\TransporterException as GeminiTransporterException;
use Gemini\Responses\GenerativeModel\GenerateContentResponse;
use Gemini\Testing\ClientFake;
use PHPUnit\Framework\TestCase;

class GeminiProviderTest extends TestCase
{
    protected AIConfig $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = new AIConfig([
            'primary_provider' => 'gemini',
            'fallback_provider' => 'openai',
            'timeout_ms' => 15000,
            'max_retries' => 2,
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

    public function test_gemini_success_path(): void
    {
        $fakeResponse = GenerateContentResponse::fake([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => 'Hello from Gemini!']
                        ]
                    ]
                ]
            ],
            'usageMetadata' => [
                'promptTokenCount' => 5,
                'candidatesTokenCount' => 8,
                'totalTokenCount' => 13,
            ]
        ]);

        $clientFake = new ClientFake([$fakeResponse]);
        $provider = new GeminiProvider($this->config, $clientFake);

        $response = $provider->generateText('Test prompt');

        $this->assertTrue($response->isSuccess());
        $this->assertEquals('Hello from Gemini!', $response->text);
        $this->assertEquals('gemini', $response->providerUsed);
        $this->assertEquals(13, $response->tokensUsed);
        $this->assertNull($response->errorMessage);
        $this->assertNull($response->errorType);
    }

    public function test_gemini_rate_limit_error_classification(): void
    {
        $clientFake = new ClientFake([
            new GeminiErrorException([
                'code' => 429,
                'message' => 'Quota exceeded for quota metric',
                'status' => 'RESOURCE_EXHAUSTED',
            ])
        ]);

        $provider = new GeminiProvider($this->config, $clientFake);
        $response = $provider->generateText('Test prompt');

        $this->assertFalse($response->isSuccess());
        $this->assertEquals('rate_limit', $response->errorType);
        $this->assertEquals('gemini', $response->providerUsed);
        $this->assertStringContainsString('Quota exceeded', $response->errorMessage);
    }

    public function test_gemini_auth_error_classification(): void
    {
        $clientFake = new ClientFake([
            new GeminiErrorException([
                'code' => 403,
                'message' => 'API key not valid. Please pass a valid API key.',
                'status' => 'PERMISSION_DENIED',
            ])
        ]);

        $provider = new GeminiProvider($this->config, $clientFake);
        $response = $provider->generateText('Test prompt');

        $this->assertFalse($response->isSuccess());
        $this->assertEquals('authentication', $response->errorType);
        $this->assertEquals('gemini', $response->providerUsed);
    }

    public function test_gemini_timeout_error_classification(): void
    {
        $clientFake = new ClientFake([
            new GeminiTransporterException(
                new \GuzzleHttp\Exception\ConnectException(
                    'cURL error 28: Operation timed out after 15000 milliseconds',
                    new \GuzzleHttp\Psr7\Request('POST', 'https://generativelanguage.googleapis.com')
                )
            )
        ]);

        $provider = new GeminiProvider($this->config, $clientFake);
        $response = $provider->generateText('Test prompt');

        $this->assertFalse($response->isSuccess());
        $this->assertEquals('timeout', $response->errorType);
        $this->assertEquals('gemini', $response->providerUsed);
    }
}
