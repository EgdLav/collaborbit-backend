<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function owner_can_invite_user()
    {
        $owner = User::factory()->create();
        $invitee = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $response = $this->actingAs($owner)->postJson(
            "/api/workspaces/{$workspace->id}/invitations",
            ['invitee_id' => $invitee->id]
        );
        $response->assertStatus(201);

        $this->assertDatabaseHas('invitations', [
            'workspace_id' => $workspace->id,
            'invitee_id' => $invitee->id,
        ]);
    }

    /** @test */
    public function non_owner_cannot_invite()
    {
        $owner = User::factory()->create();
        $random = User::factory()->create();
        $invitee = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $response = $this->actingAs($random)->postJson(
            "/api/workspaces/{$workspace->id}/invitations",
            ['invitee_id' => $invitee->id]
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function cannot_invite_same_user_twice()
    {
        $owner = User::factory()->create();
        $invitee = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);

        Invitation::factory()->forWorkspaceUser($workspace, $invitee)->create();

        $response = $this->actingAs($owner)->postJson(
            "/api/workspaces/{$workspace->id}/invitations",
            ['invitee_id' => $invitee->id]
        );

        $response->assertStatus(409);
    }

    /** @test */
    public function invitee_can_accept_invitation()
    {
        $this->withoutExceptionHandling();
        $owner = User::factory()->create();
        $invitee = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);
        $chat = Chat::factory()->create([
            'workspace_id' => $workspace->id,
            'type' => 'workspace',
        ]);

        $invitation = Invitation::factory()
            ->forWorkspaceUser($workspace, $invitee)
            ->create();

        $response = $this->actingAs($invitee)->patchJson(
            "/api/workspaces/{$workspace->id}/invitations/{$invitation->id}",
            ['status' => 'accepted']
        );

        $response->assertStatus(200);

        $this->assertDatabaseMissing('invitations', [
            'id' => $invitation->id,
        ]);
    }

    /** @test */
    public function other_users_cannot_update_invitation()
    {
        $owner = User::factory()->create();
        $invitee = User::factory()->create();
        $random = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $invitation = Invitation::factory()
            ->forWorkspaceUser($workspace, $invitee)
            ->create();

        $response = $this->actingAs($random)->patchJson(
            "/api/workspaces/{$workspace->id}/invitations/{$invitation->id}",
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_get_his_invitations()
    {
        $user = User::factory()->create();

        Invitation::factory()->count(3)->create([
            'invitee_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/invitations');
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data.invitations');
    }
}
