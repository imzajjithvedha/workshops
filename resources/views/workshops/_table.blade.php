@forelse($items as $item)
    <tr>
        <td><span class="workshop-code">{{ $item->code }}</span></td>
        <td>
            <a href="{{ route('workshops.show', $item) }}" class="text-link">{{ $item->title }}</a>
            <p class="text-xs text-gray-500">with {{ $item->instructor }}</p>
        </td>
        <td>
            {{ $item->starts_at->format('D, d M Y') }}
            <p class="text-xs text-gray-500">{{ $item->starts_at->format('g:i A') }} – {{ $item->ends_at->format('g:i A') }}</p>
        </td>
        <td>{{ $item->location }}</td>
        <td><x-seats :workshop="$item" /></td>
        <td><x-status-badge :status="$item->status" /></td>
        <td>
            <div class="flex items-center gap-3">
                <a href="{{ route('workshops.show', $item) }}" class="action-button action-btn-view" title="Attendees & registration">
                    <i class="bi bi-people"></i>
                </a>
                @can('update', $item)
                    <a href="{{ route('workshops.edit', $item) }}" class="action-button action-btn-edit" title="Edit">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="py-10 text-center text-(--color-primary)">No workshops match these filters.</td>
    </tr>
@endforelse
