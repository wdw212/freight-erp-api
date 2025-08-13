<?php

namespace Database\Seeders;

use App\Models\InvoiceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InvoiceType::query()->truncate();

        $list = [
            '电子普通发票',
            '专用发票',
            '普通发票'
        ];
        
        foreach ($list as $item) {
            $invoiceType = new InvoiceType();
            $invoiceType->name = $item;
            $invoiceType->save();
        }
    }
}
