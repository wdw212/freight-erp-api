<?php

namespace App\Http\Resources\PageAnnotation;

use App\Models\PageAnnotation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $model_type
 * @property mixed $content
 * @property mixed $created_at
 */
class PageAnnotationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id ' => $this->id,
            'model_type' => PageAnnotation::$modelTypeMap[$this->model_type],
            'content' => $this->content,
        ];
    }
}
