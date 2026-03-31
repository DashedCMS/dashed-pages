<?php

namespace Dashed\DashedPages\Policies;

use Dashed\DashedCore\Policies\BaseResourcePolicy;

class PagePolicy extends BaseResourcePolicy
{
    protected function resourceName(): string
    {
        return 'Page';
    }
}
