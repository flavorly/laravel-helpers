<?php

use Brick\Math\BigDecimal;
use Flavorly\LaravelHelpers\Helpers\Math\Math;
use Flavorly\LaravelHelpers\Helpers\Saloon\FixtureExtended;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

if (! function_exists('tokenize')) {
    /**
     * Tokenize an array of multiple arguments into a string.
     */
    function tokenize(mixed ...$args): string
    {
        return implode(':', $args);
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
        $cleanUrl = strtok($url, '?');
        // @phpstan-ignore-next-line
        $filename = basename($cleanUrl);
        $path = tempnam(sys_get_temp_dir(), Str::uuid());

        Http::withUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36')
            ->withHeaders([
                'sec-ch-ua-platform' => '"macOS"',
                'sec-ch-ua-mobile' => '?0',
                'sec-ch-ua' => '"Chromium";v="128", "Not;A=Brand";v="24", "Google Chrome";v="128"',
            ])
            ->throw(fn () => throw new Exception('Could not download file'))
            ->sink($path)
            ->get($url);

        return new UploadedFile($path, $filename);
    }
}

if (! function_exists('math')) {

    /**
     * Helper to quickly create a math object based on the project config.
     */
    function math(float|int|string|BigDecimal $number): Math
    {
        return Math::of(
            $number,
            Config::integer('app.decimal_scale', Config::integer('laravel-helpers.math.decimal_scale', 10)),
            Config::integer('app.decimal_integer', Config::integer('laravel-helpers.math.decimal_integer', 10)),
        );
    }
}
