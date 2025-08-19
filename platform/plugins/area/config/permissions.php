<?php

return [
    [
        'name' => 'Areas',
        'flag' => 'area.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'area.create',
        'parent_flag' => 'area.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'area.edit',
        'parent_flag' => 'area.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'area.destroy',
        'parent_flag' => 'area.index',
    ],
];
