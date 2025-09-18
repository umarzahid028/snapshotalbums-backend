<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriveAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'name'       => $this->drive_name,
            'email'       => $this->drive_email,
            'avatar'       => $this->avatar,
            'drive_storage' => $this->drive_storage,
            'used_storage'  => $this->used_storage,
            'status'  => $this->status,
            'event_title' => optional($this->album)->event_title,
            'total_files' => optional($this->album)->total_files,
        ];
    }
}
