<?php

// purpose: REST controller managing tasks nested beneath projects

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index(Project $project): JsonResponse
    {
        $tasks = $project->tasks()->orderByDesc('created_at')->get();

        return response()->json($tasks);
    }

    public function store(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', Rule::in(Task::STATUSES)],
            'priority' => ['nullable', 'string', Rule::in(Task::PRIORITIES)],
        ]);

        $task = $project->tasks()->create($validated);

        return response()->json($task, 201);
    }

    public function show(Project $project, Task $task): JsonResponse
    {
        $this->ensureTaskBelongsToProject($project, $task);

        return response()->json($task);
    }

    public function update(Request $request, Project $project, Task $task): JsonResponse
    {
        $this->ensureTaskBelongsToProject($project, $task);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'status' => ['sometimes', 'string', Rule::in(Task::STATUSES)],
            'priority' => ['sometimes', 'string', Rule::in(Task::PRIORITIES)],
        ]);

        $task->fill($validated)->save();

        return response()->json($task->fresh());
    }

    public function destroy(Project $project, Task $task): JsonResponse
    {
        $this->ensureTaskBelongsToProject($project, $task);

        try {
            $task->delete();

            return response()->json(null, 204);
        } catch (\Throwable $exception) {
            Log::error('Destroy task error: ' . $exception->getMessage(), [
                'project_id' => $project->id,
                'task_id' => $task->id,
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Failed to delete task.'], 500);
        }
    }

    private function ensureTaskBelongsToProject(Project $project, Task $task): void
    {
        if ($task->project_id !== $project->id) {
            abort(404, 'Task not found for this project.');
        }
    }
}
