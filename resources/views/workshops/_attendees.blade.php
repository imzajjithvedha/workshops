<div class="relative data-table-container custom-scrollbar">
    <table class="data-table">
        <thead>
            <tr>
                <th>ATTENDEE</th>
                <th>EMAIL</th>
                <th>REGISTERED</th>
                <th>BY</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($registrations as $registration)
                <tr>
                    <td>{{ $registration->attendee_name }}</td>
                    <td>{{ $registration->attendee_email }}</td>
                    <td>{{ $registration->registered_at->format('d M Y, g:i A') }}</td>
                    <td>{{ $registration->registeredBy->name }}</td>
                    <td>
                        @can('cancel', $registration)
                            <button type="button" class="action-button action-btn-delete cancel-btn" title="Cancel registration"
                                data-url="{{ route('registrations.cancel', [$workshop, $registration]) }}"
                                data-name="{{ $registration->attendee_name }}">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-(--color-primary)">No one is registered yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
