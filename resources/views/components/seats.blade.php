@props(['workshop'])

@php
    $taken = $workshop->seatsTaken();
    $capacity = $workshop->capacity;
    $left = max(0, $capacity - $taken);
    $percent = $capacity > 0 ? min(100, round($taken / $capacity * 100)) : 100;
    $state = $left === 0 ? 'full' : ($left <= max(2, (int) ceil($capacity * 0.2)) ? 'low' : '');
@endphp

<div class="grid gap-1.5">
    <span class="whitespace-nowrap">
        @if ($left === 0)
            <span class="font-semibold text-(--color-red)">Full</span>
        @else
            <span class="font-semibold text-(--color-primary)">{{ sprintf('%02d', $left) }}</span> left
        @endif
        <span class="text-xs text-gray-500">({{ $taken }}/{{ $capacity }})</span>
    </span>
    <div class="seats-bar {{ $state }}"><span style="width: {{ $percent }}%"></span></div>
</div>
