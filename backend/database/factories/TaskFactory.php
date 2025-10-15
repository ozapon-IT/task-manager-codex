<?php
// purpose: generate fake task attributes including status and priority

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'due_date' => $this->faker->optional()->dateTimeBetween('now', '+2 months'),
            'status' => Arr::random(Task::STATUSES),
            'priority' => Arr::random(Task::PRIORITIES),
        ];
    }
}
