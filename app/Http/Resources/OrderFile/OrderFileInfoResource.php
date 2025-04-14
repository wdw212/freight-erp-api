<?php

namespace App\Http\Resources\OrderFile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $file
 * @property mixed $created_at
 */
class OrderFileInfoResource extends JsonResource
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
            'file' => $this->file,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
