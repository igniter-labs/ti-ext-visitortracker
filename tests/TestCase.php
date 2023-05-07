<?php

namespace IgniterLabs\VisitorTracker\Tests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Igniter\Flame\ServiceProvider::class,
            \IgniterLabs\VisitorTracker\Extension::class,
        ];
    }
}
