<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WorkshopService
{
    public function create(array $data, User $by): Workshop
    {
        return Workshop::create([
            ...$data,
            'code' => Str::upper($data['code']),
            'created_by' => $by->id,
        ]);
    }

    public function update(Workshop $workshop, array $data, User $by): Workshop
    {
        return DB::transaction(function () use ($workshop, $data, $by) {
            $workshop = Workshop::query()->lockForUpdate()->findOrFail($workshop->id);

            $taken = $workshop->activeRegistrations()->count();

            if ((int) $data['capacity'] < $taken) {
                throw new BusinessRuleException("Capacity can't be lower than the {$taken} seats already taken. Cancel registrations first.");
            }

            $workshop->update([
                ...$data,
                'code' => Str::upper($data['code']),
                'updated_by' => $by->id,
            ]);

            return $workshop;
        });
    }
}
