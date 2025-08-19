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
        'family_number',
        'father_name',
        'mother_name',
        'apartment_id',
        'family_code',
        'village',
        'current_location',
        'base_location',
        'father_career',
        'mother_career',
        'address',
        'region_id',
        'city_id',
        'village_id',
        'housing_type',
        'father_certificate',
        'mother_certificate',
        'phone',
        'email',
        'notes',
        'status',
        'family_members',
        'martyrs_names',
        'missing_names',
        'disabled_or_widows_names',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'family_number' => SafeContent::class,
        'family_members' => 'array',
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
