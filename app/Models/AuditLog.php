<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['user_id', 'action', 'subject_type', 'subject_id', 'changes', 'created_at'])]
class AuditLog extends Model
{
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'changes' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function label(): string
    {
        return ucfirst(str_replace(['.', '_'], ' ', $this->action));
    }

    public function subjectName(): string
    {
        $subject = $this->subject;

        return match (true) {
            $subject instanceof User => "{$subject->name} ({$subject->email})",
            $subject instanceof Workshop => "{$subject->code} · {$subject->title}",
            $subject instanceof Registration => "{$subject->attendee_name} on {$subject->workshop->code}",
            default => class_basename($this->subject_type) . " #{$this->subject_id}",
        };
    }
}
