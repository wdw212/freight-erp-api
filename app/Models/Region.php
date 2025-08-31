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
    
    /**
     * Get the user's first name.
     */
    protected function pathIds(): Attribute
    {
        return Attribute::make(
            get: static fn(string $value) => array_filter(explode('-', trim($this->path, '-'))),
        );
    }
}
