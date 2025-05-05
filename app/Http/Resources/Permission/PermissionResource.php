<?php

namespace App\Http\Resources\Permission;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $parent_id
 * @property mixed $type
 * @property mixed $icon
 * @property mixed $name
 * @property mixed $path
 * @property mixed $query
 * @property mixed $component
 * @property mixed $code
 * @property mixed $sort
 * @property mixed $is_cache
 * @property mixed $is_show
 * @property mixed $status
 * @property mixed $created_at
 * @property mixed $children
 */
class PermissionResource extends JsonResource
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
            'type' => $this->type,
            'icon' => $this->icon,
            'name' => $this->name,
            'path' => $this->path,
            'query' => $this->query,
            'component' => $this->component,
            'code' => $this->code,
            'sort' => $this->sort,
            'is_cache' => $this->is_cache,
            'is_show' => $this->is_show,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'children' => $this->children,
        ];
    }
}
