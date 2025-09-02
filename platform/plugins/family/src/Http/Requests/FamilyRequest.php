<?php

namespace Botble\Family\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class FamilyRequest extends Request
{
    public function rules(): array
    {
        return [
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
