<?php

namespace Botble\Family\Repositories\Caches;

use Botble\Family\Repositories\Eloquent\FamilyRepository;

/**
 * @deprecated
 */
class FamilyCacheDecorator extends FamilyRepository
{
    /**
     * {@inheritDoc}
     */
    public function getFamilyFilter(array $params)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
