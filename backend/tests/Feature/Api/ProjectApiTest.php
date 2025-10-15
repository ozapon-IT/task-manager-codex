<?php
// purpose: verify CRUD lifecycle for the project API endpoints with JSON assertions

namespace Tests\Feature\Api;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_index_returns_paginated_project_list(): void
    {
        Project::factory()->count(3)->create();

        $response = $this->getJson('/api/projects');

        $response->assertOk()
            ->assertJsonStructure([
                'current_page',
                'data' => [
                    '*' => ['id', 'title', 'description', 'due_date', 'status', 'created_at', 'updated_at', 'deleted_at', 'tasks'],
                ],
                'first_page_url',
                'from',
                'last_page',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ]);
    }

    public function test_store_creates_a_project(): void
    {
        $payload = [
            'title' => 'New Project',
            'description' => 'Seed project description',
            'due_date' => now()->addWeek()->toDateString(),
            'status' => Project::STATUS_ACTIVE,
        ];

        $response = $this->postJson('/api/projects', $payload);

        $response->assertCreated()
            ->assertJsonFragment([
                'title' => 'New Project',
                'status' => Project::STATUS_ACTIVE,
            ]);

        $this->assertDatabaseHas('projects', [
            'title' => 'New Project',
            'status' => Project::STATUS_ACTIVE,
        ]);
    }

    public function test_show_returns_a_single_project(): void
    {
        $project = Project::factory()->create();

        $response = $this->getJson("/api/projects/{$project->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $project->id,
                'title' => $project->title,
            ]);
    }

    public function test_update_modifies_a_project(): void
    {
        $project = Project::factory()->create([
            'status' => Project::STATUS_DRAFT,
        ]);

        $payload = [
            'title' => 'Updated Project Title',
            'status' => Project::STATUS_ACTIVE,
        ];

        $response = $this->patchJson("/api/projects/{$project->id}", $payload);

        $response->assertOk()
            ->assertJsonFragment([
                'title' => 'Updated Project Title',
                'status' => Project::STATUS_ACTIVE,
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Updated Project Title',
            'status' => Project::STATUS_ACTIVE,
        ]);
    }

    public function test_destroy_deletes_a_project(): void
    {
        $project = Project::factory()->create();

        $response = $this->deleteJson("/api/projects/{$project->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }
}
