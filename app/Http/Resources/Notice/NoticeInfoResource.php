<?php

namespace App\Http\Resources\Notice;

use App\Http\Resources\AdminUser\AdminUserInfoResource;
use App\Http\Resources\AdminUser\AdminUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $adminUser
 * @property mixed $title
 * @property mixed $content
 */
class NoticeInfoResource extends JsonResource
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
            'adminUser' => new AdminUserInfoResource($this->adminUser),
            'title' => $this->title,
            'content' => $this->content,
        ];
    }
}
