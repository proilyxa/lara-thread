<?php

namespace Proilyxa\LaraThread\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Proilyxa\LaraThread\ServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-thread_table.php.stub';
        $migration->up();
        */
    }

    public function test_true()
    {
        $this->assertTrue(true);
    }
}
