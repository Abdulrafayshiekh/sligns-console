<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'role' => Role::Client,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= 'password',
            'birthday' => fake()->date(max: '-20 years'),
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(['role' => Role::SuperAdmin]);
    }

    public function companyAdmin(Company $company): static
    {
        return $this->state(['role' => Role::Company, 'company_id' => $company->id]);
    }

    public function coach(Company $company): static
    {
        return $this->state(['role' => Role::Coach, 'company_id' => $company->id]);
    }

    public function clientOf(User $coach): static
    {
        return $this->state(['role' => Role::Client, 'company_id' => $coach->company_id, 'coach_id' => $coach->id]);
    }
}
