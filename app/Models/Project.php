<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start_date', 'end_date', 'status'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // a. Count the number of tasks in each project
    public static function countTasksPerProject()
    {
        return self::withCount('tasks')->get()->map(function ($project) {
            return ['project_id' => $project->id, 'task_count' => $project->tasks_count];
        });
    }

    // b. Recap number of tasks based on status per project
    public static function recapTasksByStatusPerProject()
    {
        return self::with('tasks')->get()->map(function ($project) {
            $statuses = $project->tasks->groupBy('status')->map->count();
            return ['project_id' => $project->id, 'status_recap' => $statuses];
        });
    }

    // c. Project progress (percentage of tasks Done)
    public function getProgress()
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) return 0;
        $doneTasks = $this->tasks()->where('status', 4)->count();
        return round(($doneTasks / $totalTasks) * 100, 2);
    }

    // d. Statistics of completed tasks (Done) per month
    public static function completedTasksPerMonth($year = null)
    {
        $year = $year ?? Carbon::now()->year;

        // Use database-agnostic approach
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            return Task::where('status', 4)
                ->whereYear('updated_at', $year)
                ->select(DB::raw("CAST(strftime('%m', updated_at) AS INTEGER) as month"), DB::raw('COUNT(*) as count'))
                ->groupBy('month')
                ->get();
        }

        // MySQL
        return Task::where('status', 4)
            ->whereYear('updated_at', $year)
            ->select(DB::raw('MONTH(updated_at) as month'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')
            ->get();
    }

    // e. Determine Problematic Projects (has overdue tasks + small progress, e.g., <50%)
    public static function getProblematicProjects($progressThreshold = 50)
    {
        return self::with('tasks')->get()->filter(function ($project) use ($progressThreshold) {
            $hasOverdue = $project->tasks->where('deadline', '<', Carbon::now())->where('status', '!=', 4)->count() > 0;
            $progress = $project->getProgress();
            return $hasOverdue && $progress < $progressThreshold;
        });
    }
}
