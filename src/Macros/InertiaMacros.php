<?php

namespace Flavorly\LaravelHelpers\Macros;

use Closure;
use Flavorly\LaravelHelpers\Contracts\RegistersMacros;
use Illuminate\Contracts\Support\Arrayable;
use Inertia\Inertia;

class InertiaMacros implements RegistersMacros
{
    public static function register(): void
    {
        self::hybridlyShare();
    }

    public static function hybridlyShare(): void
    {
        // Response Macros
        if (! Inertia::hasMacro('append')) {
            Inertia::macro('append', function (
                string|array|Arrayable $key,
                mixed $value = null
            ): Inertia {
                /** @var Inertia $this */
                // @phpstan-ignore-next-line
                $sharedValue = $this->shared($key, []);
                // We need to evaluate the close for resolving & merge the values
                if ($sharedValue instanceof Closure) {
                    $sharedValue = $sharedValue();
                }
                // @phpstan-ignore-next-line
                $this->share(
                    $key,
                    array_merge_recursive($sharedValue, [$value])
                );

                // @phpstan-ignore-next-line
                return $this;
            });
        }
    }
}
