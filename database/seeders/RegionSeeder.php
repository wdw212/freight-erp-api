<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Region::query()->truncate();

        $data = [
            [
                'name' => '浙江省',
                'children' => []
            ],
            [
                'name' => '宁波地区',
                'children' => [
                    [
                        'name' => '北仑'
                    ],
                    [
                        'name' => '小港'
                    ],
                    [
                        'name' => '镇海'
                    ],
                    [
                        'name' => '九龙湖/澥浦',
                    ],
                    [
                        'name' => '江北慈城',
                    ],
                    [
                        'name' => '江北',
                    ],
                ],
            ]
        ];


        foreach ($data as $item) {
            $parent = new Region();
            $parent->parent_id = 0;
            $parent->name = $item['name'];
            $parent->save();

            foreach ($item['children'] as $child) {
                $region = new Region();
                $region->parent_id = $parent->id;
                $region->name = $child['name'];
                $region->save();
            }
        }
    }
}
