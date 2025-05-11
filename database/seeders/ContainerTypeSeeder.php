<?php

namespace Database\Seeders;

use App\Models\ContainerType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContainerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContainerType::query()->truncate();

        $data = [
            '20GP',
            '40HQ',
            '45HQ',
            '40RH',
            '其他'
        ];
        
        foreach ($data as $item) {
            $containerType = new ContainerType();
            $containerType->name = $item;
            $containerType->save();
        }
    }
}
