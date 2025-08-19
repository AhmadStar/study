<?php

return [
    [
        'name' => 'Apartments',
        'flag' => 'apartment.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'apartment.create',
        'parent_flag' => 'apartment.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'apartment.edit',
        'parent_flag' => 'apartment.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'apartment.destroy',
        'parent_flag' => 'apartment.index',
    ],
];
