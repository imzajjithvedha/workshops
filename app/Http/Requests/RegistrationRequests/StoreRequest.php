<?php

namespace App\Http\Requests\RegistrationRequests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('register', $this->route('workshop'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'attendee_name' => ['bail', 'required', 'max:200'],
            'attendee_email' => ['bail', 'required', 'email', 'max:200'],
        ];
    }

    public function attributes(): array
    {
        return [
            'attendee_name' => 'name',
            'attendee_email' => 'email',
        ];
    }
}
