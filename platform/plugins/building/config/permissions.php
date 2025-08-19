<?php

return [
    [
        'name' => 'Buildings',
        'flag' => 'building.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'building.create',
        'parent_flag' => 'building.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'building.edit',
        'parent_flag' => 'building.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'building.destroy',
        'parent_flag' => 'building.index',
    ],
];
