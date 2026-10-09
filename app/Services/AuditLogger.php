<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    private const EXCLUDED = [
        'id', 'password', 'remember_token', 'created_at', 'updated_at',
        'created_by', 'updated_by', 'registered_by', 'cancelled_by', 'active_email', 'workshop_id',
    ];

    public function created(Model $model): AuditLog
    {
        $changes = [];

        foreach ($this->filter($model->getAttributes()) as $field => $value) {
            $changes[$field] = [null, $value];
        }

        return $this->record($model->auditAction('created'), $model, $changes);
    }

    public function updated(Model $model): ?AuditLog
    {
        $changes = [];
        $previous = $model->getPrevious();

        foreach ($this->filter($model->getChanges()) as $field => $new) {
            $changes[$field] = [$previous[$field] ?? null, $new];
        }

        if ($model->wasChanged('password')) {
            $changes['password'] = ['(hidden)', '(changed)'];
        }

        if (! $changes) {
            return null;
        }

        return $this->record($model->auditAction('updated'), $model, $changes);
    }

    public function record(string $action, Model $subject, array $changes = []): AuditLog
    {
        return AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'changes' => $changes ?: null,
            'created_at' => now(),
        ]);
    }

    private function filter(array $attributes): array
    {
        return array_diff_key($attributes, array_flip(self::EXCLUDED));
    }
}
