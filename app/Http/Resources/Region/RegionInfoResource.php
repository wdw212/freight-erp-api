<?php

namespace App\Http\Resources\Region;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $parent_id
 * @property mixed $name
 * @property mixed $nb_20_gp
 * @property mixed $nb_40_hq
 * @property mixed $sh_20_gp
 * @property mixed $sh_40_hq
 * @property mixed $remark
 */
class RegionInfoResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'nb_20_gp' => $this->nb_20_gp,
            'nb_40_hq' => $this->nb_40_hq,
            'sh_20_gp' => $this->sh_20_gp,
            'sh_40_hq' => $this->sh_40_hq,
            'remark' => $this->remark,
        ];
    }
}
