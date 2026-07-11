<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_get_chats_list()
    {
        $user = User::factory()->create();

        $chat = Chat::factory()->create();
        $chat->users()->attach($user->id);

        $this->actingAs($user)
            ->getJson('/api/chats')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'chats'
                ]
            ]);
    }

    /** @test */
    public function private_chat_is_created()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)
            ->postJson("/api/chats/private/{$user2->id}");

        $response->assertOk();

        $chatId = $response->json('data.chat.id');

        $chat = Chat::find($chatId);

        $this->assertNotNull($chat);

        $this->assertEquals('private', $chat->type);

        $this->assertTrue(
            $chat->users->contains($user1->id) &&
            $chat->users->contains($user2->id)
        );
    }

    /** @test */
    public function test_private_chat_is_not_duplicated()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user1, 'sanctum')
            ->postJson("/api/chats/private/{$user2->id}")
            ->assertOk();

        $this->actingAs($user1, 'sanctum')
            ->postJson("/api/chats/private/{$user2->id}")
            ->assertOk();

        $this->assertEquals(
            1,
            Chat::where('type', 'private')->count()
        );
    }

    /** @test */
    public function user_can_view_chat()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $chat = Chat::factory()->create([
            'type' => 'private',
        ]);

        $chat->users()->attach([
            $user->id,
            $other->id,
        ]);

        $this->actingAs($user)
            ->getJson("/api/chats/{$chat->id}")
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'chat' => [
                        'id',
                        'type',
                        'users',
                        'last_message',
                    ]
                ]
            ]);
    }

    /** @test */
    public function user_can_send_message()
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();

        $chat = Chat::factory()->create([
            'type' => 'private',
        ]);

        $chat->users()->attach([
            $user->id,
            $receiver->id,
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/chats/{$chat->id}/messages", [
                'body' => 'Hello world',
            ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'message' => [
                        'id',
                        'body',
                        'created_at',
                        'time',
                        'is_mine',
                        'user',
                    ]
                ]
            ]);

        $this->assertDatabaseHas('messages', [
            'body' => 'Hello world',
            'user_id' => $user->id,
            'chat_id' => $chat->id,
        ]);
    }

    /** @test */
    public function user_can_get_messages_list()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $chat = Chat::factory()->create([
            'type' => 'private',
        ]);

        $chat->users()->attach([
            $user->id,
            $other->id,
        ]);

        Message::factory()
            ->count(5)
            ->create([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
            ]);

        $this->actingAs($user)
            ->getJson("/api/chats/{$chat->id}/messages")
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'messages'
                ]
            ]);
    }

    /** @test */
    public function unauthorized_user_cannot_view_chat()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $chat = Chat::factory()->create([
            'type' => 'private',
        ]);

        $chat->users()->attach([
            $other->id,
        ]);

        $this->actingAs($user)
            ->getJson("/api/chats/{$chat->id}")
            ->assertForbidden();
    }
}
