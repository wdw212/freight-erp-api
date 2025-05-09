<?php

namespace Database\Seeders;

use App\Models\OrderType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderType::query()->truncate();

        $data = [
            '代拉代报',
            '仓库内装',
            '海运整柜',
            '拼箱空运',
            '进口清关',
            '自拉自报',
            '进出仓保税区',
            '其他'
        ];
        
        foreach ($data as $item) {
            $orderType = new OrderType();
            $orderType->name = $item;
            $orderType->save();
        }
    }
}
