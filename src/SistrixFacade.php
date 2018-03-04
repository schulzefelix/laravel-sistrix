<?php

namespace SchulzeFelix\Sistrix;

use Illuminate\Support\Facades\Facade;


class SistrixFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-sistrix';
    }
}