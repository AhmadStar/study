<?php

namespace Botble\District\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\City\Models\City;
use Botble\Area\Models\Area;

class District extends BaseModel
{
    protected $table = 'districts';

    protected $fillable = [
        'name',
        'city_id',
        'population_estimate',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }
}
