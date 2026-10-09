<?php

namespace App\Http\Requests\WorkshopRequests;

use App\Enums\WorkshopStatus;
use App\Models\Workshop;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Create and update share the rules; the policy decides who may do either.
 */
class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request. Checked
     * before validation, so a forbidden API call gets 403, not 422.
     */
    public function authorize(): bool
    {
        $workshop = $this->route('workshop');

        return $workshop instanceof Workshop
            ? $this->user()->can('update', $workshop)
            : $this->user()->can('create', Workshop::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtoupper(trim((string) $this->input('code'))),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => [
                'bail',
                'required',
                'max:30',
                'regex:/^[A-Z0-9-]+$/',
                Rule::unique('workshops')->ignore($this->route('workshop')),
            ],
            'title' => ['bail', 'required', 'max:200'],
            'description' => ['bail', 'nullable', 'max:2000'],
            'instructor' => ['bail', 'required', 'max:200'],
            'location' => ['bail', 'required', Rule::in(config('workshops.locations'))],
            'starts_at' => ['bail', 'required', 'date'],
            'ends_at' => ['bail', 'required', 'date', 'after:starts_at'],
            'capacity' => ['bail', 'required', 'integer', 'min:1', 'max:1000'],
            'status' => ['bail', 'required', Rule::enum(WorkshopStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'code.regex' => 'Use only letters, numbers and dashes, e.g. POT-101.',
            'ends_at.after' => 'The end time must be after the start time.',
        ];
    }
}
