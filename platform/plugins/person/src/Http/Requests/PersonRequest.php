<?php

namespace Botble\Person\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class PersonRequest extends Request
{
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:220'],
            'gender' => ['nullable', 'string'],
            'birth_date' => ['nullable', 'date'],
            'marital_status' => ['nullable', 'string'],
            'health_status' => ['nullable', 'string'],
            'education_level' => ['nullable', 'string'],
            'job' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(BaseStatusEnum::values())],
        ];
    }
}
