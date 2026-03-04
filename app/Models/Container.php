<?php
/**
 * 集装箱 Model
 */

namespace App\Models;

use App\Observers\ContainerObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property mixed $order
 * @property mixed $no_image
 */
#[ObservedBy(ContainerObserver::class)]
class Container extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'no',
        'seal_number',
        'container_type_id',
        'container_type_name',
        'serial_number',
        'pre_pull_wharf_id',
        'pre_pull_wharf_name',
        'wharf_id',
        'wharf_name',
        'drop_off_wharf_id',
        'drop_off_wharf_name',
        'is_entered_port',
        'driver',
        'driver_name',
        'fleet_id',
        'fleet_name',
        'cargo_weight',
        'loading_at',
        'remark',
        'freight_status',
        'freight_remark',
        'no_image',
        'seal_number_image',
        'wharf_record_image',
        'entered_port_record_image',
        'entered_port_info'
    ];

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo
     */
    public function containerType(): BelongsTo
    {
        return $this->belongsTo(ContainerType::class);
    }

    /**
     * @return BelongsTo
     */
    public function fleet(): BelongsTo
    {
        return $this->belongsTo(Fleet::class);
    }

    /**
     * @return BelongsTo
     */
    public function prePullWharf(): BelongsTo
    {
        return $this->belongsTo(YardWharf::class, 'pre_pull_wharf_id');
    }

    /**
     * @return BelongsTo
     */
    public function wharf(): BelongsTo
    {
        return $this->belongsTo(Wharf::class, 'wharf_id');
    }

    /**
     * @return BelongsTo
     */
    public function dropOffWharf(): BelongsTo
    {
        return $this->belongsTo(YardWharf::class, 'drop_off_wharf_id');
    }

    /**
     * 集装箱信息(件毛体)
     * @return HasMany
     */
    public function containerItems(): HasMany
    {
        return $this->hasMany(ContainerItem::class);
    }


    /**
     * @return HasMany
     */
    public function containerLoadingAddresses(): HasMany
    {
        return $this->hasMany(ContainerLoadingAddress::class);
    }

    /**
     * 柜型展示名称（快照优先）
     * @return string
     */
    public function getContainerTypeDisplayNameAttribute(): string
    {
        $snapshotName = trim((string)($this->container_type_name ?? ''));
        if ($snapshotName !== '') {
            return $snapshotName;
        }

        if ($this->relationLoaded('containerType')) {
            $relationName = trim((string)($this->containerType?->name ?? ''));
            if ($relationName !== '') {
                return $relationName;
            }
        }

        return empty($this->container_type_id) ? '' : (string)$this->container_type_id;
    }

    /**
     * 车队展示名称（快照优先）
     * @return string
     */
    public function getFleetDisplayNameAttribute(): string
    {
        $snapshotName = trim((string)($this->fleet_name ?? ''));
        if ($snapshotName !== '') {
            return $snapshotName;
        }

        if ($this->relationLoaded('fleet')) {
            $relationName = trim((string)($this->fleet?->name ?? ''));
            if ($relationName !== '') {
                return $relationName;
            }
        }

        return empty($this->fleet_id) ? '' : (string)$this->fleet_id;
    }

    /**
     * 预提码头展示名称（快照优先）
     * @return string
     */
    public function getPrePullWharfDisplayNameAttribute(): string
    {
        return $this->resolveWharfDisplayName('pre_pull_wharf_name', 'prePullWharf', $this->pre_pull_wharf_id);
    }

    /**
     * 提箱码头展示名称（快照优先）
     * @return string
     */
    public function getWharfDisplayNameAttribute(): string
    {
        return $this->resolveWharfDisplayName('wharf_name', 'wharf', $this->wharf_id);
    }

    /**
     * 落箱码头展示名称（快照优先）
     * @return string
     */
    public function getDropOffWharfDisplayNameAttribute(): string
    {
        return $this->resolveWharfDisplayName('drop_off_wharf_name', 'dropOffWharf', $this->drop_off_wharf_id);
    }

    /**
     * @return array{id: int|null, name: string}
     */
    public function getContainerTypeDisplayAttribute(): array
    {
        return [
            'id' => empty($this->container_type_id) ? null : (int)$this->container_type_id,
            'name' => $this->container_type_display_name,
        ];
    }

    /**
     * @return array{id: int|null, name: string}
     */
    public function getFleetDisplayAttribute(): array
    {
        return [
            'id' => empty($this->fleet_id) ? null : (int)$this->fleet_id,
            'name' => $this->fleet_display_name,
        ];
    }

    /**
     * 司机展示名称（快照优先）
     * @return string
     */
    public function getDriverDisplayNameAttribute(): string
    {
        $snapshotName = trim((string)($this->driver_name ?? ''));
        if ($snapshotName !== '') {
            return $snapshotName;
        }

        return trim((string)($this->driver ?? ''));
    }

    /**
     * @return string
     */
    public function getDriverDisplayAttribute(): string
    {
        return $this->driver_display_name;
    }

    /**
     * @return array{id: int|null, name: string}
     */
    public function getPrePullWharfDisplayAttribute(): array
    {
        return [
            'id' => empty($this->pre_pull_wharf_id) ? null : (int)$this->pre_pull_wharf_id,
            'name' => $this->pre_pull_wharf_display_name,
        ];
    }

    /**
     * @return array{id: int|null, name: string}
     */
    public function getWharfDisplayAttribute(): array
    {
        return [
            'id' => empty($this->wharf_id) ? null : (int)$this->wharf_id,
            'name' => $this->wharf_display_name,
        ];
    }

    /**
     * @return array{id: int|null, name: string}
     */
    public function getDropOffWharfDisplayAttribute(): array
    {
        return [
            'id' => empty($this->drop_off_wharf_id) ? null : (int)$this->drop_off_wharf_id,
            'name' => $this->drop_off_wharf_display_name,
        ];
    }

    /**
     * 码头展示名称统一计算
     * @param string $snapshotField
     * @param string $relation
     * @param mixed $id
     * @return string
     */
    private function resolveWharfDisplayName(string $snapshotField, string $relation, mixed $id): string
    {
        $snapshotName = trim((string)($this->{$snapshotField} ?? ''));
        if ($snapshotName !== '') {
            return $snapshotName;
        }

        if ($this->relationLoaded($relation)) {
            $relationName = trim((string)($this->{$relation}?->name ?? ''));
            if ($relationName !== '') {
                return $relationName;
            }
        }

        return empty($id) ? '' : (string)$id;
    }
}
