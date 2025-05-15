<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::query()->truncate();
        Permission::create(['name' => '权限1']);
        Permission::create(['name' => '权限2']);
        Permission::create(['name' => '权限3']);
        Permission::create(['name' => '权限4']);
    }
}
