<?php

namespace Flavorly\LaravelHelpers\Contracts;

interface BootsServiceProviders
{
    /**
     * Ensures we have a boot method responsible
     * for booting anything into the correct service provider
     */
    public function boot(): void;

    /**
     * Ensures we have a register method responsible
     */
    public function register(): void;
}
