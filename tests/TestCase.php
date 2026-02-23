<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Hard-abort if the test suite is about to run against the real database.
     * This fires before every single test method.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $connection = config('database.default');
        $driver     = config("database.connections.{$connection}.driver");
        $database   = config("database.connections.{$connection}.database");

        // Only SQLite :memory: is allowed during tests.
        if ($driver !== 'sqlite' || $database !== ':memory:') {
            $this->fail(
                "SAFETY ABORT: Tests must use SQLite :memory:, but the active "
                    . "connection is '{$connection}' (driver={$driver}, db={$database}). "
                    . "Check phpunit.xml and .env.testing to ensure DB_CONNECTION=sqlite "
                    . "and DB_DATABASE=:memory: are set."
            );
        }
    }
}
