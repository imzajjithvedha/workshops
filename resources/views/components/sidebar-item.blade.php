@props(['href', 'active' => false, 'icon', 'label', 'count' => null])

<a href="{{ $href }}" class="sidebar-item {{ $active ? 'active' : '' }}">
    <i class="bi {{ $icon }}"></i>{{ $label }}
    @if (!is_null($count) && $count > 0)
        <span class="sidebar-badge">{{ $count }}</span>
    @endif
</a>
