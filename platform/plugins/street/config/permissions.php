<?php

return [
    [
        'name' => 'Streets',
        'flag' => 'street.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'street.create',
        'parent_flag' => 'street.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'street.edit',
        'parent_flag' => 'street.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'street.destroy',
        'parent_flag' => 'street.index',
    ],
];
