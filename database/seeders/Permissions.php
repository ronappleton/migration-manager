<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class Permissions extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::query()->updateOrCreate(['name' => 'personal', 'guard_name' => 'api']);
        Role::query()->updateOrCreate(['name' => 'business', 'guard_name' => 'api']);
    }
}
