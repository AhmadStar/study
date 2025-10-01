<?php

namespace Botble\Street\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Area\Models\Area;

class Street extends BaseModel
{
    protected $table = 'streets';

    protected $fillable = [
        'name',
        'area_id',
        'status',
        'shape', // <— add this
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

}
