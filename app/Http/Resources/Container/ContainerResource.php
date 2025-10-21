<?php

namespace App\Http\Resources\Container;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $no
 * @property mixed $cargo_weight
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
