<?php

namespace App\Http\Requests\UserRequests;

use Illuminate\Contracts\Validation\ValidationRule;

class StoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge($this->commonRules(), [
            'password' => ['bail', 'required', 'min:8', 'confirmed'],
        ]);
    }
}
