@php
    $is_edit = isset($user);
    $is_self = $is_edit && $user->is(auth()->user());
    $roles = [
        App\Enums\UserRole::STAFF->value => 'Registers and cancels attendees; views workshops and history.',
        App\Enums\UserRole::MANAGER->value => 'Everything staff can do, plus adds and edits workshops.',
        App\Enums\UserRole::ADMIN->value => 'Creates accounts and sets roles. No workshop access.',
    ];
@endphp

<div class="back-top">
    <div>
        <p class="back-title">{{ $is_edit ? 'Edit User' : 'Add New User' }}</p>
        <p class="back-description">{{ $is_edit ? 'Update the details below and save changes.' : 'Fill in the details below to create a new user.' }}</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="back-back-button">
        <i class="bi bi-arrow-left"></i>
        <span>Back to Users</span>
    </a>
</div>

<div class="back-form">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-5 mb-3 sm:mb-5">
        <div class="grid">
            <label class="label" for="name">Name<span class="asterisk">*</span></label>
            <input type="text" id="name" name="name" class="input" placeholder="Name" value="{{ old('name', $is_edit ? $user->name : '') }}" required>
            <x-input-error field="name" />
        </div>

        <div class="grid">
            <label class="label" for="email">Email<span class="asterisk">*</span></label>
            <input type="email" id="email" name="email" class="input" placeholder="Email" value="{{ old('email', $is_edit ? $user->email : '') }}" required>
            <x-input-error field="email" />
        </div>
    </div>

    @if ($is_self)
        <p class="back-warning mb-3 sm:mb-5">
            <i class="bi bi-shield-lock"></i>
            You can't change your own role or deactivate your own account, so the centre always keeps an admin.
        </p>
        <input type="hidden" name="role" value="{{ $user->role->value }}">
        <input type="hidden" name="status" value="{{ $user->status->value }}">
    @else
        <div class="grid mb-3 sm:mb-5">
            <label class="label">Role<span class="asterisk">*</span></label>
            <div class="grid gap-3 md:grid-cols-3">
                @foreach ($roles as $value => $help)
                    <label class="flex gap-2.5 items-start bg-white border border-(--color-border) p-3 cursor-pointer transition duration-300 hover:bg-(--color-light-white) has-checked:border-(--color-primary) has-checked:border-2">
                        <input type="radio" name="role" value="{{ $value }}" class="mt-1.5 cursor-pointer accent-(--color-dark-green)" @checked(old('role', $is_edit ? $user->role->value : '') === $value) required>
                        <span>
                            <span class="block text-sm sm:text-[15px] font-semibold text-(--color-primary)">{{ App\Enums\UserRole::from($value)->label() }}</span>
                            <span class="block text-xs text-gray-500 leading-5">{{ $help }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            <x-input-error field="role" />
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-5 mb-3 sm:mb-5">
        <div class="grid relative">
            <label class="label" for="password">
                @if ($is_edit)
                    Password
                    <span class="text-sm font-normal text-(--color-dark-green) ms-1">(leave blank to keep current)</span>
                @else
                    Password<span class="asterisk">*</span>
                @endif
            </label>

            <input type="password" class="input" id="password" name="password" placeholder="Enter a password" autocomplete="new-password" {{ $is_edit ? '' : 'required' }}>
            <span class="bi bi-eye-slash-fill toggle-password"></span>
            <x-input-error field="password"></x-input-error>
        </div>

        <div class="grid relative">
            <label class="label" for="password_confirmation">
                Confirm Password
                @unless ($is_edit)
                    <span class="asterisk">*</span>
                @endunless
            </label>

            <input type="password" id="password_confirmation" name="password_confirmation" class="input" placeholder="Confirm password" autocomplete="new-password" {{ $is_edit ? '' : 'required' }}>
            <span class="bi bi-eye-slash-fill toggle-password"></span>
        </div>
    </div>

    @unless ($is_self)
        <div class="grid mb-3 sm:mb-5">
            <label class="label">Status<span class="asterisk">*</span></label>
            <div class="flex gap-3 sm:gap-5 sm:items-center flex-col sm:flex-row">
                @foreach (App\Enums\Status::cases() as $status)
                    <label class="flex items-center gap-1 text-sm sm:text-[15px] text-(--color-primary) cursor-pointer">
                        <input
                            type="radio"
                            name="status"
                            value="{{ $status->value }}"
                            {{ old('status', $is_edit ? $user->status->value : App\Enums\Status::ACTIVE->value) === $status->value ? 'checked' : '' }}
                            class="cursor-pointer accent-(--color-dark-green)"
                            required
                        >
                        {{ $status->label() }}
                    </label>
                @endforeach
            </div>
            <x-input-error field="status" />
        </div>
    @endunless

    <button type="submit" class="submit-button">Submit</button>
</div>
