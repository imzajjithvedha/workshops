<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn ($model) => app(AuditLogger::class)->created($model));
        static::updated(fn ($model) => app(AuditLogger::class)->updated($model));
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'subject')->latest('id');
    }

    public function auditAction(string $event): string
    {
        return Str::snake(class_basename($this)) . '.' . $event;
    }
}
