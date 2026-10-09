@forelse ($items as $item)
    <tr>
        <td>{{ $item->id }}</td>
        <td>{{ $item->created_at->format('d M Y, g:i A') }}</td>
        <td>{{ $item->user?->name ?? 'System' }}</td>
        <td>{{ $item->label() }}</td>
        <td>{{ $item->subjectName() }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="py-10 text-center text-(--color-primary)">Nothing recorded yet.</td>
    </tr>
@endforelse