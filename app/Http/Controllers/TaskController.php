<?php



namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return Task::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string',
            'description' => 'required|string',
            'priority' => 'required|in:1,2,3',
            'deadline' => 'required|date',
            'status' => 'required|in:1,2,3,4',
        ]);
        return Task::create($validated);
    }

    public function show($id)
    {
        return Task::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $validated = $request->validate([
            'project_id' => 'exists:projects,id',
            'title' => 'string',
            'description' => 'string',
            'priority' => 'in:1,2,3',
            'deadline' => 'date',
            'status' => 'in:1,2,3,4',
        ]);
        $task->update($validated);
        return $task;
    }

    public function destroy($id)
    {
        Task::findOrFail($id)->delete();
        return response()->json(['message' => 'Task deleted']);
    }
}
