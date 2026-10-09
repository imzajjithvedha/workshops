<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'action' => $this->action,
            'changes' => $this->changes,
            'user' => new UserSummaryResource($this->whenLoaded('user')),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
