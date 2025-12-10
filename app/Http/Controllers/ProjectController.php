<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return Project::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:1,2,3',
        ]);
        return Project::create($validated);
    }

    public function show($id)
    {
        return Project::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $validated = $request->validate([
            'name' => 'string',
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
            'status' => 'in:1,2,3',
        ]);
        $project->update($validated);
        return $project;
    }

    public function destroy($id)
    {
        Project::findOrFail($id)->delete();
        return response()->json(['message' => 'Project deleted']);
    }

    // Example endpoints for functions
    public function countTasks()
    {
        return Project::countTasksPerProject();
    }

    public function recapByStatus()
    {
        return Project::recapTasksByStatusPerProject();
    }

    public function progress($id)
    {
        $project = Project::findOrFail($id);
        return ['progress' => $project->getProgress()];
    }

    public function completedPerMonth($year = null)
    {
        return Project::completedTasksPerMonth($year);
    }

    public function problematic()
    {
        return Project::getProblematicProjects();
    }
}
