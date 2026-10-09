@props(['status'])

<span class="status-badge {{ $status->badgeClass() }}">
    {{ $status->label() }}
</span>
