<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_create_returns_422_if_executor_is_not_workspace_member(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create();
        $executor = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $workspace->users()->attach($owner->id);

        $category = Category::query()->create([
            'workspace_id' => $workspace->id,
            'name' => 'General',
        ]);

        $this->actingAs($owner, 'sanctum');

        $response = $this->postJson(
            "/api/workspaces/{$workspace->id}/categories/{$category->id}/tasks",
            [
                'name' => 'My Task',
                'description' => 'Task description',
                'executor_id' => $executor->id,
                'due_date' => now()->addDay()->toDateString(),
            ]
        );

        $response->assertStatus(422);
    }

    public function test_task_creator_can_update_task(): void
    {
        $this->withoutExceptionHandling();
        $owner = User::factory()->create();
        $executor = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $workspace->users()->attach([$owner->id, $executor->id]);

        $category = Category::query()->create([
            'workspace_id' => $workspace->id,
            'name' => 'Updates',
        ]);

        $task = $this->insertTask([
            'workspace_id' => $workspace->id,
            'category_id' => $category->id,
            'creator_id' => $owner->id,
            'executor_id' => $executor->id,
            'name' => 'Initial task',
        ]);


        $this->actingAs($owner, 'sanctum');

        $response = $this->actingAs($owner)
            ->patchJson(
                "/api/workspaces/{$workspace->id}/categories/{$category->id}/tasks/{$task->id}",
                [
                    'name' => 'Updated task',
                    'description' => $task->description,
                    'due_date' => $task->due_date,
                    'executor_id' => $task->executor_id,
                ]
            );

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated task',
            ]);
    }

    public function test_random_member_cannot_update_task(): void
    {
        $owner = User::factory()->create();
        $executor = User::factory()->create();
        $member = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $workspace->users()->attach([$owner->id, $executor->id, $member->id]);

        $category = Category::query()->create([
            'workspace_id' => $workspace->id,
            'name' => 'Security',
        ]);

        $task = $this->insertTask([
            'workspace_id' => $workspace->id,
            'category_id' => $category->id,
            'creator_id' => $owner->id,
            'executor_id' => $executor->id,
        ]);

        $this->actingAs($member, 'sanctum');

        $response = $this->patchJson(
            "/api/workspaces/{$workspace->id}/categories/{$category->id}/tasks/{$task->id}",
            [
                'name' => 'Hacker move',
            ]
        );

        $response->assertStatus(403);
    }

    private function insertTask(array $data): Task
    {
        DB::table('tasks')->insert([
            'name' => $data['name'] ?? 'Task',
            'description' => 'Task description',
            'preview' => 'previews/default.png',
            'files' => json_encode([]),
            'workspace_id' => $data['workspace_id'],
            'category_id' => $data['category_id'],
            'creator_id' => $data['creator_id'],
            'executor_id' => $data['executor_id'],
            'due_date' => now()->addDay()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Task::query()->latest('id')->firstOrFail();
    }
}
