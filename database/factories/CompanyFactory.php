<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'slug' => Company::uniqueSlug($name),
            'console_language' => 'en',
            'interface_color' => '#c90076',
            'contact_person' => fake()->name(),
            'preferred_language' => 'en',
            'time_zone' => 'UTC',
            'status' => Company::ACTIVE,
        ];
    }

    public function suspended(): static
    {
        return $this->state(['status' => Company::SUSPENDED]);
    }
}
