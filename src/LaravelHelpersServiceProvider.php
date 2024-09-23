<?php

namespace Flavorly\LaravelHelpers;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelHelpersServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('helpers')
            ->hasConfigFile('laravel-helpers')
            ->hasCommands([
                Commands\HorizonWipeCommand::class,
            ]);
    }

    public function bootingPackage(): void
    {
        // Booting the package
    }

    public function registeringPackage(): void {}
}
