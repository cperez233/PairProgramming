<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_dashboard_returns_a_successful_response(): void
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get('/app');

        $response->assertStatus(200);
    }
}
