<?php

namespace Flavorly\LaravelHelpers\Tests;

use Flavorly\LaravelHelpers\LaravelHelpersServiceProvider;
use Flavorly\LaravelHelpers\Macros\StrMacros;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Flavorly\\LaravelHelpers\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        StrMacros::register();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelHelpersServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
