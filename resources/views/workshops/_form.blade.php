@php
    $is_edit = isset($workshop);
    $value = fn (string $field, $default = '') => old($field, $is_edit ? $workshop->{$field} : $default);
    $dateValue = fn (string $field) => old($field, $is_edit ? $workshop->{$field}->format('Y-m-d\TH:i') : '');
    $taken = $is_edit ? $workshop->seatsTaken() : 0;
@endphp

<div class="back-top">
    <div>
        <p class="back-title">{{ $is_edit ? 'Edit Workshop' : 'Add New Workshop' }}</p>
        <p class="back-description">{{ $is_edit ? 'Update the details below and save changes.' : 'Fill in the details below to add a workshop to the catalogue.' }}</p>
    </div>
    <a href="{{ $is_edit ? route('workshops.show', $workshop) : route('workshops.index') }}" class="back-back-button">
        <i class="bi bi-arrow-left"></i>
        <span>{{ $is_edit ? 'Back to Workshop' : 'Back to Workshops' }}</span>
    </a>
</div>

<div class="back-form">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-5 mb-3 sm:mb-5">
        <div class="grid content-start">
            <label class="label" for="code">Workshop Code<span class="asterisk">*</span></label>
            <input type="text" id="code" name="code" class="input uppercase" value="{{ $value('code') }}" placeholder="e.g. POT-101" maxlength="30" required>
            <x-input-error field="code" />
        </div>

        <div class="grid content-start md:col-span-2">
            <label class="label" for="title">Title<span class="asterisk">*</span></label>
            <input type="text" id="title" name="title" class="input" value="{{ $value('title') }}" placeholder="e.g. Wheel Throwing for Beginners" required>
            <x-input-error field="title" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-5 mb-3 sm:mb-5">
        <div class="grid content-start">
            <label class="label" for="instructor">Instructor<span class="asterisk">*</span></label>
            <input type="text" id="instructor" name="instructor" class="input" value="{{ $value('instructor') }}" placeholder="Instructor's name" required>
            <x-input-error field="instructor" />
        </div>

        <div class="grid content-start">
            <label class="label" for="location">Location<span class="asterisk">*</span></label>
            <select id="location" name="location" class="input" required>
                <option value="">- Select location -</option>
                @foreach (config('workshops.locations') as $location)
                    <option value="{{ $location }}" @selected($value('location') === $location)>{{ $location }}</option>
                @endforeach
            </select>
            <x-input-error field="location" />
        </div>

        <div class="grid content-start">
            <label class="label" for="capacity">Capacity (seats)<span class="asterisk">*</span></label>
            <input type="number" id="capacity" name="capacity" class="input" value="{{ $value('capacity') }}" min="{{ max(1, $taken) }}" max="1000" placeholder="e.g. 20" required>
            @if ($taken > 0)
                <p class="text-xs text-(--color-dark-green) mt-1">{{ $taken }} {{ Str::plural('seat', $taken) }} {{ $taken === 1 ? 'is' : 'are' }} taken, so it can't go lower than that.</p>
            @endif
            <x-input-error field="capacity" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-5 mb-3 sm:mb-5">
        <div class="grid content-start">
            <label class="label" for="starts_at">Starts<span class="asterisk">*</span></label>
            <input type="datetime-local" id="starts_at" name="starts_at" class="input" value="{{ $dateValue('starts_at') }}" required>
            <x-input-error field="starts_at" />
        </div>

        <div class="grid content-start">
            <label class="label" for="ends_at">Ends<span class="asterisk">*</span></label>
            <input type="datetime-local" id="ends_at" name="ends_at" class="input" value="{{ $dateValue('ends_at') }}" required>
            <x-input-error field="ends_at" />
        </div>
    </div>

    <div class="grid mb-3 sm:mb-5">
        <label class="label" for="description">Description</label>
        <textarea id="description" name="description" rows="4" class="textarea" maxlength="2000" placeholder="What attendees will do, what to bring…">{{ $value('description') }}</textarea>
        <x-input-error field="description" />
    </div>

    <div class="grid mb-3 sm:mb-5">
        <label class="label">Status<span class="asterisk">*</span></label>
        <div class="flex gap-3 sm:gap-5 sm:items-center flex-col sm:flex-row">
            @foreach (App\Enums\WorkshopStatus::cases() as $status)
                <label class="flex items-center gap-1 text-sm sm:text-[15px] text-(--color-primary) cursor-pointer">
                    <input
                        type="radio"
                        name="status"
                        value="{{ $status->value }}"
                        {{ old('status', $is_edit ? $workshop->status->value : App\Enums\WorkshopStatus::SCHEDULED->value) === $status->value ? 'checked' : '' }}
                        class="cursor-pointer accent-(--color-dark-green)"
                        required
                    >
                    {{ $status->label() }}
                </label>
            @endforeach
        </div>
        <x-input-error field="status" />
    </div>

    <button type="submit" class="submit-button">Submit</button>
</div>
