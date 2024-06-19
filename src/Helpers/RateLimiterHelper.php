<?php

namespace Flavorly\LaravelHelpers\Helpers;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * Handles rate limiting using a cache store.
 *
 * @method self for(string $name, Closure $callback) Register a named limiter configuration.
 * @method ?Closure limiter(string $name) Get the given named rate limiter.
 * @method mixed attempt(int $maxAttempts, Closure $callback, int $decaySeconds = 60) Attempts to execute a callback if it's not limited.
 * @method bool tooManyAttempts(int $maxAttempts) Determine if the given key has been accessed too many times.
 * @method int increment(int $decaySeconds = 60, int $amount = 1) Increment the counter for a given key for a given decay time.
 * @method int decrement(int $decaySeconds = 60, int $amount = 1) Decrement the counter for a given key for a given decay time.
 * @method int attempts() Get the number of attempts for the given key.
 * @method bool resetAttempts() Reset the number of attempts for the given key.
 * @method int retriesLeft(int $maxAttempts) Get the number of retries left for the given key.
 * @method int availableIn() Get the number of seconds until the key is accessible again.
 * @method string cleanRateLimiterKey() Clean the rate limiter key from unicode characters.
 */
final class RateLimiterHelper
{
    use ForwardsCalls;

    public function __construct(
        protected string $key,
        protected string $by,
        protected bool $hashed = true
    ) {}

    /**
     * Forwards the call to the RateLimiter instance.
     *
     * @param  array<int|string, mixed>  $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $limiter = app(RateLimiter::class);
        $key = self::getKey($this->key, $this->by, $this->hashed);

        return $this->forwardCallTo($limiter, $name, [
            ...['key' => $key],
            ...$arguments,
        ]);
    }

    /**
     * Get the rate limiter key from the request like Laravel
     *
     * @see \Illuminate\Routing\Middleware\ThrottleRequests::resolveRequestSignature
     */
    public static function getKey(string $key, string $by, bool $hashed = true): string
    {
        if (! $hashed) {
            return $key.':'.$by;
        }

        return md5($key.$by);
    }
}
