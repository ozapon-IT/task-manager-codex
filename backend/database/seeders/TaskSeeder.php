<?php

// purpose: seed tasks for each project with randomized metadata

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();

        Project::query()->each(function (Project $project) use ($faker): void {
            $project->tasks()->createMany(
                collect(range(1, $faker->numberBetween(3, 5)))->map(fn () => [
                    'title' => $faker->sentence(3),
                    'description' => $faker->paragraph(),
                    'due_date' => $faker->optional()->dateTimeBetween('now', '+2 months'),
                    'status' => Arr::random(Task::STATUSES),
                    'priority' => Arr::random(Task::PRIORITIES),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ])->all()
            );
        });
    }
}
