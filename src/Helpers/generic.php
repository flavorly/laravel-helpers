<?php

use Flavorly\LaravelHelpers\Helpers\Saloon\FixtureExtended;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\UploadedFile;

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

if (! function_exists('data_get_fallback')) {
    /**
     * Get an item from an array or object using dot notation with multiple fallback keys.
     *
     * @param  array<int|string,mixed>  $target
     * @param  array<int,string>  $keys
     */
    function data_get_fallback(array $target, array $keys, mixed $default = null): mixed
    {
        foreach ($keys as $key) {
            $result = data_get($target, $key);
            if ($result !== null) {
                return $result;
            }
        }

        return $default;
    }
}

if (! function_exists('url_to_upload_file')) {
    /**
     * Takes an external URL and transforms it into a UploadedFile.
     */
    function url_to_upload_file(string $url): UploadedFile
    {
        $info = pathinfo($url);
        $contents = file_get_contents($url);
        $file = '/tmp/'.$info['basename'];
        file_put_contents($file, $contents);

        return new UploadedFile($file, $info['basename']);
    }
}
