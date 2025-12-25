<?php
/**
 * 港口导入模版
 */

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HarborImportTemplate implements ShouldAutoSize, WithHeadings
{
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            '代码',
            '港口（中文）',
            '港口（英文）',
            '国家（中文）',
            '国家（英文）',
            '航线',
            '备注（可空）'
        ];
    }
}
