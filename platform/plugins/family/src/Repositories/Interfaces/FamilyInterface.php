<?php

namespace Botble\Family\Repositories\Interfaces;

use Botble\Blog\Models\Post;
use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface FamilyInterface extends RepositoryInterface
{
    public function getFamilyFilter(array $params);

}
