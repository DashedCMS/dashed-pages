<?php

namespace Qubiqx\QcommercePages\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Qubiqx\QcommercePages\QcommercePages
 */
class QcommercePages extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'qcommerce-pages';
    }
}
