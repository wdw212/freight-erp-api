<?php
/**
 *
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $id
 * @property mixed $job_type
 * @property mixed $month_code
 * @property mixed $basic_salary
 * @property mixed $base_rate
 * @property mixed $higher_rate
 * @property mixed $tickets
 * @property mixed $unit_price
 * @property mixed $remark
 */
class AdminUserSalary extends Model
{
    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class);
    }
}
