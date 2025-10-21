<?php

namespace App\Http\Resources\Container;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $no
 * @property mixed $cargo_weight
 * @property mixed $containerItems
 * @property mixed $containerLoadingAddresses
 * @property mixed $container_type_id
 * @property mixed $driver
 * @property mixed $entered_port_info
 * @property mixed $drop_off_wharf_id
 * @property mixed $fleet_id
 * @property mixed $freight_remark
 * @property mixed $freight_status
 * @property mixed $is_entered_port
 * @property mixed $loading_at
 * @property mixed $pre_pull_wharf_id
 * @property mixed $seal_number
 * @property mixed $serial_number
 * @property mixed $wharf_id
 * @property mixed $no_image
 * @property mixed $entered_port_record_image
 * @property mixed $seal_number_image
 * @property mixed $wharf_record_image
 */
class ContainerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'no' => $this->no,
            'cargo_weight' => $this->cargo_weight,
            'container_items' => $this->containerItems,
            'container_loading_addresses' => $this->containerLoadingAddresses,
            'container_type_id' => $this->container_type_id,
            'driver' => $this->driver,
            'drop_off_wharf_id' => $this->drop_off_wharf_id,
            'entered_port_info' => $this->entered_port_info,
            'fleet_id' => $this->fleet_id,
            'freight_remark' => $this->freight_remark,
            'freight_status' => $this->freight_status,
            'is_entered_port' => $this->is_entered_port,
            'loading_at' => $this->loading_at,
            'pre_pull_wharf_id' => $this->pre_pull_wharf_id,
            'seal_number' => $this->seal_number,
            'serial_number' => $this->serial_number,
            'wharf_id' => $this->wharf_id,
            'no_image' => formatFullUrl($this->no_image),
            'entered_port_record_image' => formatFullUrl($this->entered_port_record_image),
            'seal_number_image' => formatFullUrl($this->seal_number_image),
            'wharf_record_image' => formatFullUrl($this->wharf_record_image),
        ];
    }
}
