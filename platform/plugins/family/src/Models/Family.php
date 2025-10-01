<?php

namespace Botble\Family\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Apartment\Models\Apartment;
use Botble\Person\Models\Person;
use Botble\Building\Models\Building;

class Family extends BaseModel
{
    protected $table = 'families';

    protected $fillable = [
        'name',
        'head_name',
        'family_number',
        'floor_number',
        'street',
        'family_name',
        'family_code', // codes [A,B,C,D,E]
        'region_id',
        'building_id',
        'phone',
        'mobile',
        'notes',
        'status',
        'count_family_members',
        'is_featured_person',
        'featured_person',
        'house_type', // نوع ملكية العقار: ملك، ايجار، غير ذلك
        'nationality',
        'birth_place',
        'birth_date',
        'civil_registry',
        'national_id',
        'father_occupation',
        'need_review',
        'date_of_count',
        'smember_name',
        'cat',
        'house_number',
        'career',
        'car',
        'weapon',
        'family_members',
        'security_review',
        'created_by'
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'family_number' => SafeContent::class,
               'family_members' => 'array',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function persons()
    {
        return $this->hasMany(Person::class);
    }

}
