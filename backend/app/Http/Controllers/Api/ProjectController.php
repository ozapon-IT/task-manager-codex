<?php

// purpose: REST controller exposing CRUD endpoints for projects

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index(): JsonResponse
    {
        $projects = Project::query()
            ->with('tasks')
            ->orderByDesc('created_at')
            ->paginate();

        return response()->json($projects);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', Rule::in(Project::STATUSES)],
        ]);

        $project = Project::create($validated);

        return response()->json($project->load('tasks'), 201);
    }

    public function show(Project $project): JsonResponse
    {
        return response()->json($project->load('tasks'));
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'status' => ['sometimes', 'string', Rule::in(Project::STATUSES)],
        ]);

        $project->fill($validated)->save();

        return response()->json($project->fresh()->load('tasks'));
    }

    public function destroy(Project $project): JsonResponse
    {
        DB::beginTransaction();

        try {
            $project->tasks()->each(static function ($task): void {
                $task->delete();
            });

            $project->delete();

            DB::commit();

            return response()->json(null, 204);
        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error('Destroy project error: ' . $exception->getMessage(), [
                'project_id' => $project->id,
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Failed to delete project.'], 500);
        }
    }
}
