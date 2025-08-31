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

    /**
     * 定义一个访问器，获取所有祖先类目并按层级排序
     * @return Collection
     */
    public function getAncestorsAttribute(): Collection
    {
        // 使用 whereIn 并利用集合的 isNotEmpty 方法做前置判断
        $ids = $this->path_ids;
        dd($ids);
        return self::query()
            ->when(collect($ids)->isNotEmpty(), function ($query) use ($ids) {
                $query->whereIn('id', $ids);
            })
            ->orderBy('level')
            ->get();
    }

    /**
     * 定义一个访问器，获取包含所有祖先和当前类目的完整名称
     * @return mixed
     */
    public function getFullNameAttribute(): mixed
    {
        // 利用集合的管道方法让代码更流畅
        return $this->ancestors
            ->pluck('name')
            ->push($this->name)
            ->implode(' - ');
    }
}
