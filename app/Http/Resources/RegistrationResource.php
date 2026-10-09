<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'workshop_id' => $this->workshop_id,
            'attendee_name' => $this->attendee_name,
            'attendee_email' => $this->attendee_email,
            'status' => $this->status->value,
            'registered_at' => $this->registered_at->toIso8601String(),
            'registered_by' => new UserSummaryResource($this->whenLoaded('registeredBy')),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'cancelled_by' => new UserSummaryResource($this->whenLoaded('cancelledBy')),
            'cancellation_reason' => $this->cancellation_reason,
        ];
    }
}
