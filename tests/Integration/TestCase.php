<?php

namespace SchulzeFelix\Sistrix\Tests\Integration;

use Orchestra\Testbench\TestCase as Orchestra;
use SchulzeFelix\Sistrix\SistrixFacade;
use SchulzeFelix\Sistrix\SistrixServiceProvider;

abstract class TestCase extends Orchestra
{
    public function setUp() :void
    {
        parent::setUp();
    }
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SistrixServiceProvider::class,
        ];
    }
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Sistrix' => SistrixFacade::class,
        ];
    }
}