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

        $response->assertRedirect('/login');

        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
    }

    public function test_dashboard_can_be_accessed_by_authenticated_user(): void
    {
        $user = \App\Models\User::create([
            'name' => 'Admin RT',
            'email' => 'admin@rt.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }
}
