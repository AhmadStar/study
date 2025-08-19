<?php

return [
    [
        'name' => 'People',
        'flag' => 'person.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'person.create',
        'parent_flag' => 'person.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'person.edit',
        'parent_flag' => 'person.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'person.destroy',
        'parent_flag' => 'person.index',
    ],
];
