<p class="text-sm mb-3">Every registration ever made for this workshop, including cancelled ones. Records are never deleted.</p>

<div class="relative data-table-container custom-scrollbar mb-3 sm:mb-5">
    <table class="data-table">
        <thead>
            <tr>
                <th>ATTENDEE</th>
                <th>STATUS</th>
                <th>REGISTERED</th>
                <th>CANCELLED</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($registrations as $registration)
                <tr>
                    <td>
                        <p class="font-medium text-(--color-primary)">{{ $registration->attendee_name }}</p>
                        <p class="text-xs text-gray-500">{{ $registration->attendee_email }}</p>
                    </td>
                    <td><x-status-badge :status="$registration->status" /></td>
                    <td class="whitespace-nowrap">
                        {{ $registration->registered_at->format('d M Y, g:i A') }}
                        <p class="text-xs text-gray-500">by {{ $registration->registeredBy->name }}</p>
                    </td>
                    <td>
                        @if ($registration->cancelled_at)
                            <span class="whitespace-nowrap">{{ $registration->cancelled_at->format('d M Y, g:i A') }}</span>
                            <p class="text-xs text-gray-500">by {{ $registration->cancelledBy->name }}</p>
                            @if ($registration->cancellation_reason)
                                <p class="text-xs italic">"{{ $registration->cancellation_reason }}"</p>
                            @endif
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="py-10 text-center text-(--color-primary)">No registrations yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="main-box">
    <p class="box-title">Workshop Changes</p>
    <p class="box-description mb-3">Who changed what on this workshop, and when.</p>

    <div class="divide-y divide-(--color-border)">
        @forelse ($changes as $log)
            <div class="py-3 text-sm">
                <p>
                    <span class="font-semibold">{{ $log->label() }}</span>
                    by {{ $log->user?->name ?? 'System' }}
                    <span class="text-gray-500">· {{ $log->created_at->format('d M Y, g:i A') }}</span>
                </p>
            </div>
        @empty
            <p class="py-3 text-sm text-gray-500">No changes recorded.</p>
        @endforelse
    </div>
</div>
