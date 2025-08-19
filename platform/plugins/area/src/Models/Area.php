<?php

namespace Botble\Area\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\District\Models\District;
use Botble\Building\Models\Building;

class Area extends BaseModel
{
    protected $table = 'areas';

    protected $fillable = [
        'name',
        'district_id',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function buildings()
    {
        return $this->hasMany(Building::class);
    }

}
