<?php

namespace Botble\Apartment\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Building\Models\Building;
use Botble\Family\Models\Family;

class Apartment extends BaseModel
{
    protected $table = 'apartments';

    protected $fillable = [
        'name',
        'building_id',
        'apartment_number',
        'floor_number',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function families()
    {
        return $this->hasMany(Family::class);
    }

}
