<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActivityLogController extends Controller
{
    public const AREAS = [
        'accounts' => User::class,
        'workshops' => Workshop::class,
        'registrations' => Registration::class,
    ];

    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        $area = self::AREAS[$request->query('area')] ?? null;

        $items = AuditLog::query()
                ->when($area, fn ($q, $model) => $q->where('subject_type', (new $model)->getMorphClass()))
                ->with([
                    'user',
                    'subject' => fn (MorphTo $morph) => $morph->morphWith([Registration::class => ['workshop']]),
                ])
                ->latest('id')
                ->paginate(20)
                ->withQueryString();

        return view('admin.activity-logs.index', [
            'items' => $items,
            'filter' => $request->all(),
        ]);
    }
}
