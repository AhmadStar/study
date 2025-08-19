<?php

namespace Botble\City\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\District\Models\District;

class City extends BaseModel
{
    protected $table = 'cities';

    protected $fillable = [
        'name',
        'country',
        'population_estimate',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
    ];

    public function districts()
    {
        return $this->hasMany(District::class);
    }
}
