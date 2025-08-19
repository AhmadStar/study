<?php

return [
    [
        'name' => 'Families',
        'flag' => 'family.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'family.create',
        'parent_flag' => 'family.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'family.edit',
        'parent_flag' => 'family.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'family.destroy',
        'parent_flag' => 'family.index',
    ],
];
