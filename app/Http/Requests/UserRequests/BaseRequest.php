<?php

namespace App\Http\Requests\UserRequests;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->route('user');

        return $user instanceof User
            ? $this->user()->can('update', $user)
            : $this->user()->can('create', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function commonRules(): array
    {
        return [
            'name' => ['bail', 'required', 'max:200'],
            'email' => [
                'bail',
                'required',
                'email',
                'max:100',
                Rule::unique('users')->ignore($this->route('user')),
            ],
            'role' => ['bail', 'required', Rule::enum(UserRole::class)],
            'status' => ['bail', 'required', Rule::enum(Status::class)],
        ];
    }
}
