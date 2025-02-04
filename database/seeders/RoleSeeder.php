<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::query()->truncate();

        Role::create(['name' => '超管']);
        Role::create(['name' => '操作']);
        Role::create(['name' => '单证']);
        Role::create(['name' => '商务']);
        Role::create(['name' => '业务']);
        Role::create(['name' => '财务']);
        Role::create(['name' => '调度']);
    }
}
