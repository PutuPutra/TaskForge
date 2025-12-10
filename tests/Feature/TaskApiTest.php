<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_all_tasks()
    {
        $project = Project::factory()->create();
        Task::factory()->count(5)->create(['project_id' => $project->id]);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(5);
    }

    public function test_can_create_task()
    {
        $project = Project::factory()->create();

        $taskData = [
            'project_id' => $project->id,
            'title' => 'Bug Fixing',
            'description' => 'Resolve critical errors',
            'priority' => 3,
            'deadline' => '2025-12-15',
            'status' => 2
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Bug Fixing']);
    }

    public function test_can_show_single_task()
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'title' => 'Test Task'
        ]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Test Task']);
    }

    public function test_can_update_task()
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Task'
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated Task']);
    }

    public function test_can_delete_task()
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_task_validation_fails_without_required_fields()
    {
        $response = $this->postJson('/api/tasks', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['project_id', 'title', 'description', 'priority', 'deadline', 'status']);
    }

    public function test_task_validation_fails_with_invalid_project_id()
    {
        $response = $this->postJson('/api/tasks', [
            'project_id' => 999999,
            'title' => 'Test',
            'description' => 'Test',
            'priority' => 1,
            'deadline' => '2025-12-15',
            'status' => 1
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['project_id']);
    }
}
