<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSubscriptionResource extends JsonResource
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
            'user_id' => $this->user_id,
            'plan' => new \App\Http\Resources\SubscriptionPlanResource($this->whenLoaded('plan')),
            'transaction_id' => $this->transaction_id ?? '',
            'transaction_status' => $this->transaction_status ?? '',
            'status' => $this->status,
            'trial_ends_at' => $this->trial_ends_at?->toDateTimeString(),
            'ends_at' => $this->ends_at?->toDateTimeString(),
        ];
    }
}
