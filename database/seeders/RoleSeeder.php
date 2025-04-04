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

        Role::create([
            'name' => '超管',
            'code' => 'SUPER_ADMIN',
        ]);
        Role::create([
            'name' => '操作',
            'code' => 'OPERATE',
        ]);
        Role::create([
            'name' => '单证',
            'code' => 'DOCUMENT'
        ]);
        Role::create([
            'name' => '商务',
            'code' => 'COMMERCE',
        ]);
        Role::create([
            'name' => '业务',
            'code' => 'BUSINESS',
        ]);
        Role::create([
            'name' => '财务',
            'code' => 'FINANCE',
        ]);
        Role::create([
            'name' => '调度',
            'code' => 'SCHEDULE',
        ]);
    }
}
