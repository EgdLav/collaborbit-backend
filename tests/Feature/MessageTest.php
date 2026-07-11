<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function test_user_can_send_message()
    {
        $user = User::factory()->create();

        $chat = Chat::factory()->create();
        $chat->users()->attach($user->id);

        $response = $this->actingAs($user)
            ->postJson("/api/chats/{$chat->id}/messages", [
                'body' => 'Hello'
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('messages', [
            'body' => 'Hello',
            'user_id' => $user->id,
            'chat_id' => $chat->id,
        ]);
    }
    public function test_user_cannot_send_message_if_not_in_chat()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $chat = Chat::factory()->create();
        $chat->users()->attach($other->id);

        $this->actingAs($user)
            ->postJson("/api/chats/{$chat->id}/messages", [
                'body' => 'Hack attempt'
            ])
            ->assertForbidden();
    }
}
