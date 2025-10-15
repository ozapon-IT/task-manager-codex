<?php
// purpose: validate CRUD lifecycle for task endpoints nested under projects

namespace Tests\Feature\Api;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_index_returns_tasks_for_a_project(): void
    {
        $project = Project::factory()->create();
        $project->tasks()->saveMany(Task::factory()->count(3)->make());

        $response = $this->getJson("/api/projects/{$project->id}/tasks");

        $response->assertOk()
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'project_id', 'title', 'description', 'due_date', 'status', 'priority', 'created_at', 'updated_at', 'deleted_at'],
            ]);
    }

    public function test_store_creates_a_task_for_the_project(): void
    {
        $project = Project::factory()->create();
        $payload = [
            'title' => 'New Task',
            'description' => 'Task description',
            'due_date' => now()->addDays(3)->toDateString(),
            'status' => Task::STATUS_IN_PROGRESS,
            'priority' => Task::PRIORITY_HIGH,
        ];

        $response = $this->postJson("/api/projects/{$project->id}/tasks", $payload);

        $response->assertCreated()
            ->assertJsonFragment([
                'title' => 'New Task',
                'status' => Task::STATUS_IN_PROGRESS,
                'priority' => Task::PRIORITY_HIGH,
            ]);

        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'title' => 'New Task',
            'status' => Task::STATUS_IN_PROGRESS,
        ]);
    }

    public function test_show_returns_a_task(): void
    {
        $project = Project::factory()->create();
        $task = $project->tasks()->create(Task::factory()->make()->toArray());

        $response = $this->getJson("/api/projects/{$project->id}/tasks/{$task->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $task->id,
                'title' => $task->title,
            ]);
    }

    public function test_update_modifies_a_task(): void
    {
        $project = Project::factory()->create();
        $task = $project->tasks()->create(Task::factory()->make([
            'status' => Task::STATUS_TODO,
        ])->toArray());

        $payload = [
            'title' => 'Updated Task',
            'status' => Task::STATUS_DONE,
        ];

        $response = $this->patchJson("/api/projects/{$project->id}/tasks/{$task->id}", $payload);

        $response->assertOk()
            ->assertJsonFragment([
                'title' => 'Updated Task',
                'status' => Task::STATUS_DONE,
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
            'status' => Task::STATUS_DONE,
        ]);
    }

    public function test_destroy_deletes_a_task(): void
    {
        $project = Project::factory()->create();
        $task = $project->tasks()->create(Task::factory()->make()->toArray());

        $response = $this->deleteJson("/api/projects/{$project->id}/tasks/{$task->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }
}
