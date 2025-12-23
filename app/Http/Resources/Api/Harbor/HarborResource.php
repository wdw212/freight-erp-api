<?php

namespace App\Http\Resources\Api\Harbor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $code
 * @property mixed $name
 * @property mixed $country
 * @property mixed $route
 * @property mixed $created_at
 */
class HarborResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'country' => $this->country,
            'route' => $this->route,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
