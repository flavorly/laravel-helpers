<?php

use Flavorly\LaravelHelpers\Helpers\Saloon\FixtureExtended;
use Illuminate\Database\Eloquent\Relations\Relation;

if (! function_exists('tokenize')) {
    /**
     * Tokenize an array of multiple arguments into a string.
     */
    function tokenize(mixed ...$args): string
    {
        return implode('_', $args);
    }
}

if (! function_exists('get_morph_map_for')) {
    /**
     * Get the class name from a class string.
     */
    function get_morph_map_for(string $class, mixed $default = null): string|int
    {
        return collect(Relation::$morphMap)->flip()->get($class, $class) ?? $default;
    }
}

if (! function_exists('mock_fixture')) {
    /**
     * Alias for the mock fixture response
     */
    function mock_fixture(string $fixtureName): FixtureExtended
    {
        return new FixtureExtended($fixtureName);
    }
}
