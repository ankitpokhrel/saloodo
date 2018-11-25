<?php

use Carbon\Carbon;
use Laravel\Lumen\Testing\TestCase;

abstract class UnitTestCase extends TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    /**
     * Set up.
     */
    public function setUp()
    {
        parent::setUp();

        Carbon::setTestNow('2018-11-11 00:00:00');
    }
}
