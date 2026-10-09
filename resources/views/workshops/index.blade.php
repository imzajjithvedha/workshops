@extends('layouts.backend')

@section('title', 'Workshops')

@section('content')
    @php
        $today = today()->toDateString();
        $weekEnd = today()->endOfWeek()->toDateString();
        $yesterday = today()->subDay()->toDateString();
        $is = fn (?string $from, ?string $to, bool $seats = false) =>
            ($filter['from'] ?? '') === (string) $from
            && ($filter['to'] ?? '') === (string) $to
            && ! empty($filter['has_seats']) === $seats;
        $dateInput = fn (string $field) => empty($filter[$field]) ? 'text' : 'date';
    @endphp

    <div class="page">
        <div class="back-top">
            <div>
                <p class="back-title">Workshops</p>
                <p class="back-description">Find a workshop, check its seats and register attendees.</p>
            </div>

            @can('create', App\Models\Workshop::class)
                <a href="{{ route('workshops.create') }}" class="back-add-button">
                    <i class="bi bi-plus-lg"></i>
                    <span>Add New Workshop</span>
                </a>
            @endcan
        </div>

        <div class="filter-chips">
            <a href="{{ route('workshops.index') }}" class="filter-chip {{ $is($today, null) ? 'active' : '' }}">Upcoming</a>
            <a href="{{ route('workshops.index', ['from' => $today, 'to' => $weekEnd, 'has_seats' => 1]) }}" class="filter-chip {{ $is($today, $weekEnd, true) ? 'active' : '' }}">This Week · Seats Left</a>
            <a href="{{ route('workshops.index', ['from' => $today, 'to' => $today]) }}" class="filter-chip {{ $is($today, $today) ? 'active' : '' }}">Today</a>
            <a href="{{ route('workshops.index', ['from' => '', 'to' => $yesterday]) }}" class="filter-chip {{ $is(null, $yesterday) ? 'active' : '' }}">Past</a>
            <a href="{{ route('workshops.index', ['from' => '', 'to' => '']) }}" class="filter-chip {{ $is(null, null) ? 'active' : '' }}">All Dates</a>
        </div>

        <form method="GET" action="{{ route('workshops.index') }}" id="filter-form" class="filter-form">
            <input type="text" name="search" value="{{ $filter['search'] ?? '' }}" placeholder="Search workshops" title="Search by code, title or instructor" class="input filter-input">

            <input type="{{ $dateInput('from') }}" name="from" value="{{ $filter['from'] ?? '' }}" placeholder="From date" aria-label="From date"
                onfocus="this.type='date'" onblur="if (!this.value) this.type='text'" class="input filter-input">

            <input type="{{ $dateInput('to') }}" name="to" value="{{ $filter['to'] ?? '' }}" placeholder="To date" aria-label="To date"
                onfocus="this.type='date'" onblur="if (!this.value) this.type='text'" class="input filter-input">

            <select name="status" class="input filter-input">
                <option value="">- All Status -</option>
                @foreach (App\Enums\WorkshopStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(($filter['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>

            <select name="location" class="input filter-input">
                <option value="">- All Locations -</option>
                @foreach (config('workshops.locations') as $location)
                    <option value="{{ $location }}" @selected(($filter['location'] ?? '') === $location)>{{ $location }}</option>
                @endforeach
            </select>

            <label class="filter-check">
                <input type="checkbox" name="has_seats" value="1" class="w-4 h-4 accent-(--color-primary)" @checked(! empty($filter['has_seats']))>
                Seats available
            </label>

            <a href="{{ route('workshops.index') }}" class="clear-filter-button">
                <i class="bi bi-x-circle"></i>
                <span>Clear</span>
            </a>
        </form>

        <div class="relative data-table-container custom-scrollbar">
            <x-table-loader />

            <table class="data-table">
                <thead>
                    <tr>
                        <th>CODE</th>
                        <th>WORKSHOP</th>
                        <th>DATE &amp; TIME</th>
                        <th>LOCATION</th>
                        <th>SEATS</th>
                        <th>STATUS</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @include('workshops._table')
                </tbody>
            </table>
        </div>

        <div class="mt-3 sm:mt-5">
            {{ $items->links() }}
        </div>
    </div>
@endsection

@push('after-scripts')
    @vite(['resources/js/index.js'])
@endpush
