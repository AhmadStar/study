<?php

namespace Botble\Person\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;

class Person extends BaseModel
{
    protected $table = 'people';

    protected $fillable = [
        'family_id',
        'name',
        'gender',
        'birth_date',
        'marital_status',
        'date_of_birth',
        'relationship',
        'national_id',
        'occupation',
        'health_status',
        'education_level',
        'job',
        'notes',
    ];

    protected $casts = [
        'name' => SafeContent::class,
        'marital_status' => SafeContent::class,
        'health_status' => SafeContent::class,
        'education_level' => SafeContent::class,
        'job' => SafeContent::class,
        'notes' => SafeContent::class,
        'birth_date' => 'date',
        'status' => BaseStatusEnum::class,
    ];
}
