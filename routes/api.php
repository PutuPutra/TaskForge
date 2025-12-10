<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;

// Custom routes MUST come before apiResource to avoid conflicts
Route::get('projects/count-tasks', [ProjectController::class, 'countTasks']);
Route::get('projects/recap-status', [ProjectController::class, 'recapByStatus']);
Route::get('projects/problematic', [ProjectController::class, 'problematic']);
Route::get('projects/{id}/progress', [ProjectController::class, 'progress']);
Route::get('tasks/completed-per-month/{year?}', [ProjectController::class, 'completedPerMonth']);

// Resource routes
Route::apiResource('projects', ProjectController::class);
Route::apiResource('tasks', TaskController::class);
