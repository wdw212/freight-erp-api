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
 * @property mixed $container_type_name
 * @property mixed $driver
 * @property mixed $entered_port_info
 * @property mixed $drop_off_wharf_id
 * @property mixed $fleet_id
 * @property mixed $fleet_name
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
        $containerTypeDetail = $this->container_type_display;
        $fleetDetail = $this->fleet_display;
        $prePullWharfDetail = $this->pre_pull_wharf_display;
        $wharfDetail = $this->wharf_display;
        $dropOffWharfDetail = $this->drop_off_wharf_display;
        $containerLoadingAddresses = collect($this->containerLoadingAddresses)->map(static function ($item) {
            $loadingAddressDetail = $item->loading_address_display;
            $loadingAddressName = $loadingAddressDetail['name'] ?? '';
            return [
                'id' => $item->id,
                'loading_address_id' => $loadingAddressDetail['id'],
                'loading_address_name' => $loadingAddressName,
                'loading_address' => $loadingAddressName,
                'loading_address_detail' => $loadingAddressDetail,
                'address' => $item->address,
                'contact_name' => $item->contact_name,
                'phone' => $item->phone,
                'remark' => $item->remark,
            ];
        })->values();

        return [
            'id' => $this->id,
            'no' => $this->no,
            'cargo_weight' => $this->cargo_weight,
            'container_items' => $this->containerItems,
            'container_loading_addresses' => $containerLoadingAddresses,
            'container_type_id' => $containerTypeDetail['id'],
            'container_type_name' => $containerTypeDetail['name'],
            'container_type' => $containerTypeDetail['name'],
            'container_type_detail' => $containerTypeDetail,
            'driver' => $this->driver,
            'drop_off_wharf_id' => $dropOffWharfDetail['id'],
            'drop_off_wharf_name' => $dropOffWharfDetail['name'],
            'drop_off_wharf' => $dropOffWharfDetail['name'],
            'drop_off_wharf_detail' => $dropOffWharfDetail,
            'entered_port_info' => $this->entered_port_info,
            'fleet_id' => $fleetDetail['id'],
            'fleet_name' => $fleetDetail['name'],
            'fleet' => $fleetDetail['name'],
            'fleet_detail' => $fleetDetail,
            'freight_remark' => $this->freight_remark,
            'freight_status' => $this->freight_status,
            'is_entered_port' => $this->is_entered_port,
            'loading_at' => $this->loading_at,
            'pre_pull_wharf_id' => $prePullWharfDetail['id'],
            'pre_pull_wharf_name' => $prePullWharfDetail['name'],
            'pre_pull_wharf' => $prePullWharfDetail['name'],
            'pre_pull_wharf_detail' => $prePullWharfDetail,
            'seal_number' => $this->seal_number,
            'serial_number' => $this->serial_number,
            'wharf_id' => $wharfDetail['id'],
            'wharf_name' => $wharfDetail['name'],
            'wharf' => $wharfDetail['name'],
            'wharf_detail' => $wharfDetail,
            'no_image' => formatFullUrl($this->no_image),
            'entered_port_record_image' => formatFullUrl($this->entered_port_record_image),
            'seal_number_image' => formatFullUrl($this->seal_number_image),
            'wharf_record_image' => formatFullUrl($this->wharf_record_image),
        ];
    }
}
