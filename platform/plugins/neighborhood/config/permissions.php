<?php

return [
    [
        'name' => 'Neighborhoods',
        'flag' => 'neighborhood.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'neighborhood.create',
        'parent_flag' => 'neighborhood.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'neighborhood.edit',
        'parent_flag' => 'neighborhood.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'neighborhood.destroy',
        'parent_flag' => 'neighborhood.index',
    ],
];
