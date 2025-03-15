<?php

namespace App\Http\Resources\ActivityLog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $log_name
 * @property mixed $description
 * @property mixed $subject_type
 * @property mixed $event
 * @property mixed $subject_id
 * @property mixed $causer_type
 * @property mixed $causer_id
 * @property mixed $properties
 * @property mixed $batch_uuid
 * @property mixed $created_at
 */
class ActivityLogResource extends JsonResource
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
            'log_name' => $this->log_name,
            'description' => $this->description,
            'subject_type' => $this->subject_type,
            'event' => $this->event,
            'subject_id' => $this->subject_id,
            'causer_type' => $this->causer_type,
            'causer_id' => $this->causer_id,
            'properties' => $this->properties,
            'batch_uuid' => $this->batch_uuid,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
