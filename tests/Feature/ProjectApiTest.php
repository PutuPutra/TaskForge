<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_all_projects()
    {
        Project::factory()->count(3)->create();

        $response = $this->getJson('/api/projects');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_create_project()
    {
        $projectData = [
            'name' => 'New Project',
            'start_date' => '2025-12-01',
            'end_date' => '2025-12-31',
            'status' => 1
        ];

        $response = $this->postJson('/api/projects', $projectData);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'New Project']);
    }

    public function test_can_show_single_project()
    {
        $project = Project::factory()->create(['name' => 'Test Project']);

        $response = $this->getJson("/api/projects/{$project->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Test Project']);
    }

    public function test_can_update_project()
    {
        $project = Project::factory()->create();

        $response = $this->putJson("/api/projects/{$project->id}", [
            'name' => 'Updated Project'
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Project']);
    }

    public function test_can_delete_project()
    {
        $project = Project::factory()->create();

        $response = $this->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_count_tasks_per_project()
    {
        $project = Project::factory()->create();
        Task::factory()->count(5)->create(['project_id' => $project->id]);

        $response = $this->getJson('/api/projects/count-tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([['project_id', 'task_count']]);
    }

    public function test_recap_by_status()
    {
        $project = Project::factory()->create();
        Task::factory()->create(['project_id' => $project->id, 'status' => 1]);
        Task::factory()->create(['project_id' => $project->id, 'status' => 2]);

        $response = $this->getJson('/api/projects/recap-status');

        $response->assertStatus(200)
            ->assertJsonStructure([['project_id', 'status_recap']]);
    }

    public function test_project_progress()
    {
        $project = Project::factory()->create();
        Task::factory()->count(10)->create(['project_id' => $project->id, 'status' => 4]); // Done
        Task::factory()->count(10)->create(['project_id' => $project->id, 'status' => 1]); // Not done

        $response = $this->getJson("/api/projects/{$project->id}/progress");

        $response->assertStatus(200)
            ->assertJsonStructure(['progress'])
            ->assertJson(['progress' => 50.0]);
    }

    public function test_completed_per_month()
    {
        $project = Project::factory()->create();
        Task::factory()->count(3)->create([
            'project_id' => $project->id,
            'status' => 4,
            'updated_at' => '2025-12-10'
        ]);

        $response = $this->getJson('/api/tasks/completed-per-month/2025');

        $response->assertStatus(200)
            ->assertJsonStructure([['month', 'count']]);
    }

    public function test_problematic_projects()
    {
        // Create project with overdue tasks and low progress
        $project = Project::factory()->create();
        Task::factory()->count(5)->create([
            'project_id' => $project->id,
            'status' => 1,
            'deadline' => '2025-12-01' // Overdue
        ]);
        Task::factory()->create([
            'project_id' => $project->id,
            'status' => 4,
            'deadline' => '2025-12-01'
        ]);

        $response = $this->getJson('/api/projects/problematic');

        $response->assertStatus(200);
    }
}
