<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
            'description' => $this->description,
            'no_of_ablums' => $this->no_of_ablums,
            'duration_days' => $this->duration_days,
            'features' => $this->features,
            'is_active' => $this->is_active,
            'is_popular' => $this->is_popular,
        ];
    }
}
