<?php

return [
    [
        'name' => 'Districts',
        'flag' => 'district.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'district.create',
        'parent_flag' => 'district.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'district.edit',
        'parent_flag' => 'district.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'district.destroy',
        'parent_flag' => 'district.index',
    ],
];
