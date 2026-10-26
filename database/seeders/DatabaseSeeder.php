<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'role' => Role::SuperAdmin,
            'name' => 'Aligna Team',
            'email' => env('DEPLOYER_EMAIL', 'admin@aligna.ai'),
            'password' => env('DEPLOYER_MASTER_KEY', 'aligna-master-key'),
        ]);

        $this->call(DemoSeeder::class);
    }
}
