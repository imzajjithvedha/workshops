<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkshopResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'instructor' => $this->instructor,
            'location' => $this->location,
            'starts_at' => $this->starts_at->toIso8601String(),
            'ends_at' => $this->ends_at->toIso8601String(),
            'capacity' => $this->capacity,
            'seats_taken' => $this->seatsTaken(),
            'seats_left' => $this->seatsLeft(),
            'status' => $this->status->value,
            'open_for_registration' => $this->isOpenForRegistration(),
            'created_by' => new UserSummaryResource($this->whenLoaded('creator')),
            'updated_by' => new UserSummaryResource($this->whenLoaded('editor')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
