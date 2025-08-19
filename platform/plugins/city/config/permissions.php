<?php

return [
    [
        'name' => 'Cities',
        'flag' => 'city.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'city.create',
        'parent_flag' => 'city.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'city.edit',
        'parent_flag' => 'city.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'city.destroy',
        'parent_flag' => 'city.index',
    ],
];
