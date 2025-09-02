<?php

namespace Botble\Family\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Apartment\Models\Apartment;
use Botble\Person\Models\Person;

class Family extends BaseModel
{
    protected $table = 'families';

    protected $fillable = [
        'name',
        'family_name',
        'family_number',
        'floor_number',
        'apartment_id',
        'family_code', // codes [A,B,C,D,E]
        'address',
        'region_id',
        'phone',
        'notes',
        'status',
        'count_family_members',
        'building_id',
        'is_featured_person',
        'featured_person',
        'house_type', // نوع ملكية العقار: ملك، ايجار، غير ذلك
        'is_empty',
        'need_review',

        'head_name', 'nationality', 'birth_place', 'birth_date',
        'civil_registry', 'national_id', 'father_occupation',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'family_number' => SafeContent::class,
        //        'family_members' => 'array',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function persons()
    {
        return $this->hasMany(Person::class);
    }
}
