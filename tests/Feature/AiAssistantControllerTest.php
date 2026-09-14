<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\Lead;
use App\Models\User;
use App\Services\AI\AIService;
use App\Services\AI\DTOs\AIResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AiAssistantControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'staff']);
    }

    public function test_generate_requires_authentication(): void
    {
        $response = $this->postJson('/ai/generate', [
            'prompt' => 'Hello AI',
        ]);

        $response->assertStatus(401);
    }

    public function test_generate_returns_response_without_provider_for_standard_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole('staff');

        $mockAi = Mockery::mock(AIService::class);
        $mockAi->shouldReceive('generate')
            ->once()
            ->with('Hello AI', Mockery::type('array'))
            ->andReturn(AIResponse::success('AI response text', 'gemini', 15, 250.0));

        $this->app->instance(AIService::class, $mockAi);

        $response = $this->actingAs($user)->postJson('/ai/generate', [
            'prompt' => 'Hello AI',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'text' => 'AI response text',
                'isAdmin' => false,
                'providerUsed' => null, // Hidden for non-admins!
            ]);
    }

    public function test_generate_returns_provider_telemetry_for_admin_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $mockAi = Mockery::mock(AIService::class);
        $mockAi->shouldReceive('generate')
            ->once()
            ->with('Hello Admin AI', Mockery::type('array'))
            ->andReturn(AIResponse::success('Admin response text', 'gemini', 20, 180.0));

        $this->app->instance(AIService::class, $mockAi);

        $response = $this->actingAs($admin)->postJson('/ai/generate', [
            'prompt' => 'Hello Admin AI',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'text' => 'Admin response text',
                'isAdmin' => true,
                'providerUsed' => 'gemini', // Visible for admin cost monitoring!
            ]);
    }

    public function test_usage_stats_requires_admin_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('staff');

        $response = $this->actingAs($user)->getJson('/ai/usage-stats');
        $response->assertStatus(403);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $responseAdmin = $this->actingAs($admin)->getJson('/ai/usage-stats');
        $responseAdmin->assertStatus(200)
            ->assertJsonStructure([
                'gemini' => ['today_requests', 'daily_cap'],
                'openai' => ['today_requests', 'daily_cap'],
                'recent_logs',
            ]);
    }

    public function test_chat_page_renders_successfully(): void
    {
        $user = User::factory()->create();
        $user->assignRole('staff');

        $response = $this->actingAs($user)->get('/ai/chat');
        $response->assertStatus(200)
            ->assertViewIs('ai.chat')
            ->assertViewHas('stats');
    }

    public function test_chat_conversational_endpoint_success(): void
    {
        $user = User::factory()->create();
        $user->assignRole('staff');

        $mockAi = Mockery::mock(AIService::class);
        $mockAi->shouldReceive('generate')
            ->once()
            ->with(Mockery::type('string'), Mockery::on(function ($options) {
                return ($options['module'] ?? '') === 'chatbot';
            }))
            ->andReturn(AIResponse::success('Chatbot answer', 'gemini', 25, 210.0));

        $this->app->instance(AIService::class, $mockAi);

        $response = $this->actingAs($user)->postJson('/ai/chat', [
            'message' => 'What is the status of my enquiries?',
            'history' => [
                ['role' => 'user', 'text' => 'Hello'],
                ['role' => 'model', 'text' => 'Hi, how can I help?']
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'text' => 'Chatbot answer',
            ]);
    }
}
