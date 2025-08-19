<?php

namespace Botble\Building\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Person\Models\Person;
use Botble\Area\Models\Area;
use Botble\Apartment\Models\Apartment;

class Building extends BaseModel
{
    protected $table = 'buildings';

    protected $fillable = ['name', 'latitude', 'longitude', 'address', 'description','area_id',
        'building_number',
        'floors_count',];

    public function persons()
    {
        return $this->hasMany(Person::class);
    }

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function apartments()
    {
        return $this->hasMany(Apartment::class);
    }

}
