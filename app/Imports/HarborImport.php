<?php

namespace App\Imports;

use App\Models\Harbor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class HarborImport implements ToCollection, WithStartRow, SkipsEmptyRows
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection): void
    {
        foreach ($collection as $row) {
            if (Harbor::query()->where('code', $row[0])->exists()) {
                continue;
            }
            $harbor = new Harbor();
            $harbor->code = $row[0];
            $harbor->name = $row[1];
            $harbor->en_name = $row[2];
            $harbor->country = $row[3];
            $harbor->en_country = $row[4];
            $harbor->route = $row[5];
            $harbor->remark = $row[6];
            $harbor->save();
        }
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }
}
