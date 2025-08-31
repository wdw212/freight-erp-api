<?php

namespace App\Models;

use App\Observers\RegionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected function ancestors(): Attribute
    {
        return Attribute::make(
            get: static function (mixed $value, array $attributes) {
                $pathIds = array_filter(explode('-', trim($attributes['path'], '-')));
                self::query()
                    // 使用上面的访问器获取所有祖先类目 ID
                    ->whereIn('id', $pathIds)
                    // 按层级排序
                    ->orderBy('level')
                    ->get();
            }
        );
    }
}
