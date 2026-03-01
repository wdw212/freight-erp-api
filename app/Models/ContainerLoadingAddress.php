<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContainerLoadingAddress extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'container_id',
        'loading_address_id',
        'loading_address',
        'address',
        'contact_name',
        'phone',
        'remark',
    ];

    /**
     * @return BelongsTo
     */
    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class);
    }

    /**
     * @return BelongsTo
     */
    public function loadingAddress(): BelongsTo
    {
        return $this->belongsTo(LoadingAddress::class);
    }

    /**
     * 装柜地址展示名称（快照优先）
     * @return string
     */
    public function getLoadingAddressDisplayNameAttribute(): string
    {
        $snapshot = trim((string)($this->loading_address ?? ''));
        if ($snapshot !== '') {
            return $snapshot;
        }

        if ($this->relationLoaded('loadingAddress')) {
            $relationAddress = trim((string)($this->loadingAddress?->address ?? ''));
            if ($relationAddress !== '') {
                return $relationAddress;
            }
        }

        return empty($this->loading_address_id) ? '' : (string)$this->loading_address_id;
    }

    /**
     * 装柜地址展示结构
     * @return array{id:int|null,name:string}
     */
    public function getLoadingAddressDisplayAttribute(): array
    {
        return [
            'id' => empty($this->loading_address_id) ? null : (int)$this->loading_address_id,
            'name' => $this->loading_address_display_name,
        ];
    }
}
