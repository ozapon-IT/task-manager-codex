<?php
// purpose: generate fake project attributes for testing and seeding scenarios

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $statuses = Project::STATUSES;

        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'due_date' => $this->faker->optional()->dateTimeBetween('now', '+3 months'),
            'status' => Arr::random($statuses),
        ];
    }
}
