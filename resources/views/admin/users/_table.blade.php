@forelse($items as $item)
    <tr>
        <td>{{ $item->id }}</td>
        <td>
            {{ $item->name }}
            @if ($item->is(auth()->user()))
                <span class="text-xs text-(--color-dark-green) font-semibold">(you)</span>
            @endif
        </td>
        <td>{{ $item->email }}</td>
        <td>{{ $item->role->label() }}</td>
        <td><x-status-badge :status="$item->status" /></td>
        <td>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.edit', $item) }}" class="action-button action-btn-edit" title="Edit">
                    <i class="bi bi-pencil-square"></i>
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="py-10 text-center text-(--color-primary)">No results found.</td>
    </tr>
@endforelse
