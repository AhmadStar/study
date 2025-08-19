<?php

namespace Botble\Family\Http\Requests;

use Botble\Base\Rules\EmailRule;
use Botble\Family\Models\Family;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class RegisterFamilyRequest extends Request
{
    public function rules(): array
    {
        return [
            'family_number' => [
                'required',
                'string',
                'max:120',
                'min:2',
                Rule::unique('families', 'family_number') // Add unique validation
            ],
            'father_name' => ['required', 'string', 'max:120', 'min:2'],
            'mother_name' => ['required', 'string', 'max:120', 'min:2'],
        ];
    }

    // Optional: Custom error messages
    public function messages(): array
    {
        return [
            'family_number.unique' => 'This family number already exists. Please use a different one.',
        ];
    }
}
