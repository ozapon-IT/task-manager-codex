<?php

// purpose: define public API routes for health checks, projects, and tasks
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => response()->json(['pong' => true]));

Route::apiResource('projects', ProjectController::class);
Route::apiResource('projects.tasks', TaskController::class);
