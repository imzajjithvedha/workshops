@extends('layouts.backend')

@section('title', $workshop->title)

@section('content')
    @php
        $left = $workshop->seatsLeft();
        $taken = $workshop->seatsTaken();
    @endphp

    <div class="page dashboard">
        <div class="back-top">
            <div>
                <div class="flex items-center gap-3 mb-0.5">
                    <span class="workshop-code">{{ $workshop->code }}</span>
                    <x-status-badge :status="$workshop->status" />
                </div>
                <p class="back-title">{{ $workshop->title }}</p>
                <p class="back-description">with {{ $workshop->instructor }}</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('workshops.index') }}" class="back-back-button">
                    <i class="bi bi-arrow-left"></i>
                    <span>Back to Workshops</span>
                </a>
                @can('update', $workshop)
                    <a href="{{ route('workshops.edit', $workshop) }}" class="back-add-button">
                        <i class="bi bi-pencil-square"></i>
                        <span>Edit</span>
                    </a>
                @endcan
            </div>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5 mb-3 sm:mb-5">
            <div class="box">
                <i class="bi bi-ticket-perforated"></i>
                <div>
                    <p class="title">SEATS LEFT</p>
                    @if ($left === 0)
                        <p class="value text-(--color-red)!">Full</p>
                    @else
                        <p class="value">{{ sprintf('%02d', $left) }} <span>of {{ $workshop->capacity }}</span></p>
                    @endif
                </div>
            </div>

            <div class="box">
                <i class="bi bi-people"></i>
                <div>
                    <p class="title">REGISTERED</p>
                    <p class="value">{{ sprintf('%02d', $taken) }} <span>active</span></p>
                </div>
            </div>

            <div class="box">
                <i class="bi bi-calendar-event"></i>
                <div>
                    <p class="title">DATE</p>
                    <p class="value text-lg!">{{ $workshop->starts_at->format('D, d M') }}</p>
                    <p class="text-sm font-semibold">{{ $workshop->starts_at->format('g:i A') }} – {{ $workshop->ends_at->format('g:i A') }}</p>
                </div>
            </div>

            <div class="box">
                <i class="bi bi-geo-alt"></i>
                <div>
                    <p class="title">LOCATION</p>
                    <p class="value text-lg!">{{ $workshop->location }}</p>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-3 sm:gap-5 mb-3 sm:mb-5">
            <div class="main-box lg:col-span-2">
                <p class="box-title">About This Workshop</p>
                <p class="box-description mb-3">
                    Added by {{ $workshop->creator->name }} on {{ $workshop->created_at->format('d M Y') }}
                    @if ($workshop->editor)
                        <span class="text-(--color-primary)">·</span> last edited by {{ $workshop->editor->name }} {{ $workshop->updated_at->diffForHumans() }}
                    @endif
                </p>

                <div class="workshop-facts mb-3">
                    <p><i class="bi bi-person-video3"></i>{{ $workshop->instructor }}</p>
                    <p><i class="bi bi-clock"></i>{{ $workshop->starts_at->format('l, d F Y') }}, {{ $workshop->starts_at->format('g:i A') }} – {{ $workshop->ends_at->format('g:i A') }}</p>
                    <p><i class="bi bi-geo-alt"></i>{{ $workshop->location }}</p>
                </div>

                @if ($workshop->description)
                    <p class="text-sm leading-7 text-gray-700">{{ $workshop->description }}</p>
                @endif
            </div>

            <div class="main-box">
                <p class="box-title">Register an Attendee</p>
                <p class="box-description mb-3">Name and email, as given on the phone or at the desk.</p>

                @if (! $workshop->isOpenForRegistration())
                    <p class="back-info">
                        <i class="bi bi-info-circle"></i>
                        {{ $workshop->hasStarted() ? 'This workshop has started, so registration is closed.' : "This workshop is {$workshop->status->label()}, so registration is closed." }}
                    </p>
                @elseif ($left === 0)
                    <p class="back-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        This workshop is full. A seat opens when a registration is cancelled.
                    </p>
                @else
                    @can('register', $workshop)
                        <form action="{{ route('registrations.store', $workshop) }}" method="POST">
                            @csrf
                            <div class="grid mb-3">
                                <label class="label" for="attendee_name">Name<span class="asterisk">*</span></label>
                                <input type="text" id="attendee_name" name="attendee_name" class="input" value="{{ old('attendee_name') }}" placeholder="Attendee's full name" required>
                                <x-input-error field="attendee_name" />
                            </div>
                            <div class="grid mb-4">
                                <label class="label" for="attendee_email">Email<span class="asterisk">*</span></label>
                                <input type="email" id="attendee_email" name="attendee_email" class="input" value="{{ old('attendee_email') }}" placeholder="name@example.com" required>
                                <x-input-error field="attendee_email" />
                            </div>
                            <button type="submit" class="submit-button">Register Attendee</button>
                        </form>
                    @endcan
                @endif
            </div>
        </div>

        <div class="tabs">
            <a href="{{ route('workshops.show', $workshop) }}" class="tab {{ $tab === 'attendees' ? 'active' : '' }}">
                Attendees ({{ $taken }})
            </a>
            <a href="{{ route('workshops.show', [$workshop, 'tab' => 'history']) }}" class="tab {{ $tab === 'history' ? 'active' : '' }}">
                History
            </a>
        </div>

        @if ($tab === 'attendees')
            @include('workshops._attendees')
        @else
            @include('workshops._history')
        @endif

        @include('workshops._cancel-modal')
    </div>
@endsection

@push('after-scripts')
    @vite(['resources/js/index.js'])
@endpush
