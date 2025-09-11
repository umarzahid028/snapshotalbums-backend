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
            'event_title'       => $this->event_title,
            'google_drive_folder_id ' => 'https://drive.google.com/drive/folders/'.$this->google_drive_folder_id,
            'google_drive_folder_name ' => $this->google_drive_folder_name,
            'event_date' => $this->event_date,
        ];
    }
}
