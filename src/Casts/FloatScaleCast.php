<?php

namespace Flavorly\LaravelHelpers\Casts;

use Brick\Math\Exception\MathException;
use Brick\Math\Exception\RoundingNecessaryException;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<float, string|float>
 */
final class FloatScaleCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, string>  $attributes
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?float
    {
        if ($value === null) {
            return null;
        }

        // @phpstan-ignore-next-line
        return math($value)->ensureScale()->toFloat();
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, string>  $attributes
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return math($value)->ensureScale()->toString();
    }
}
