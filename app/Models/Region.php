<?php

namespace App\Models;

use App\Observers\RegionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property mixed $parent_id
 * @property int|mixed $level
 * @property mixed|string $path
 * @property mixed $parent
 * @property mixed $name
 */
#[ObservedBy(RegionObserver::class)]
class Region extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    /**
     * 关联上级
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'parent_id', 'id');
    }

    /**
     * 关联下级
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_id', 'id')->with(['children']);
    }

    /**
     * 定义一个访问器，获取所有祖先类目的 ID 值
     * @return string[]
     */
    public function getPathIdsAttribute(): array
    {
        // 使用 trim 和 explode 的组合，并通过 array_filter 移除空值
        return array_filter(explode('-', trim($this->path, '-')));
    }
}
