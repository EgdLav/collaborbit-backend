<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'first_name' => 'Egor',
            'last_name' => 'Test',
            'email' => 'egor@test.com',
            'password' => 'password123',
            'department' => 'Backend Development',
        ]);
        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'egor@test.com',
        ]);
    }

    public function test_register_validation_fails_for_missing_required_fields(): void
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422);
    }

    public function test_user_can_login_and_receive_token(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'token',
            ],
        ]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/logout');

        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_protected_route(): void
    {
        $response = $this->getJson('/api/workspaces');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_access_protected_route(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/workspaces');

        $response->assertStatus(200);
    }
}
