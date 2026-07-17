<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_route_returns_successful_response(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
    }

    public function test_movements_route_returns_successful_response(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/movimientos');

        $response->assertStatus(200);
    }
}
