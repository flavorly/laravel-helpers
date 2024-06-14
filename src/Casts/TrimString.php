<?php

namespace Flavorly\LaravelHelpers\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<string, string>
 */
class TrimString implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, string>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): string
    {
        // @phpstan-ignore-next-line
        return (string) preg_replace('/\s+/', '', $value ?? '');
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, string>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        return (string) preg_replace('/\s+/', '', $value ?? '');
    }
}
