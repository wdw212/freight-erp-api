<?php

namespace App\Http\Resources\Notice;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $adminUser
 * @property mixed $title
 * @property mixed $content
 * @property mixed $created_at
 */
class NoticeResource extends JsonResource
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
            'adminUser' => $this->adminUser,
            'title' => $this->title,
            'content' => $this->content,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
