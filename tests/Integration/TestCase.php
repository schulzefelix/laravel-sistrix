<?php

namespace Fschulze\Sistrix\Tests\Integration;

use Orchestra\Testbench\TestCase as Orchestra;
use Fschulze\Sistrix\SistrixFacade;
use Fschulze\Sistrix\SistrixServiceProvider;

abstract class TestCase extends Orchestra
{
    public function setUp()
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