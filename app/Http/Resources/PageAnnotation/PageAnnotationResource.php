<?php

namespace App\Http\Resources\PageAnnotation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageAnnotationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
