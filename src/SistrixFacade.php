<?php

namespace Fschulze\Sistrix;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\Analytics\Analytics
 */
class SistrixFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-sistrix';
    }
}