<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->event_title ?? null,
            'type' => $this->event_type ?? null,
            'time' => $this->event_time ?? null,
            'location' => $this->location ?? null,
            'status' => $this->status ?? null,
            'qrCode' => $this->qrCode ?? null,
            'description' => $this->event_description ?? null,
            'max_photos_per_guest' => $this->max_photos_per_guest ?? null,
            'custom_welcome_message' => $this->custom_welcome_message ?? null,
            'privacy_level' => $this->privacy_level ?? null,
            'allow_guest_uploads' => $this->allow_guest_uploads ?? null,
            'google_drive_folder_id' => $this->google_drive_folder_id
                ? 'https://drive.google.com/drive/folders/' . $this->google_drive_folder_id
                : null ,
            'google_drive_folder_name' => $this->google_drive_folder_name ?? null,
            'date' => $this->event_date ?? null,
        ];
    }
}
