<?php

namespace Flavorly\LaravelHelpers\Concerns;

use Illuminate\Support\Collection;
use ReflectionClass;

trait HasDataPropertiesReflection
{
    private static array $paramCache = [];

    /**
     * @param  string[]  $only
     * @param  string[]  $except
     * @return Collection<int,string>
     */
    public static function getParams(array $only = [], array $except = []): Collection
    {
        $class = static::class;

        return collect(self::$paramCache[$class] ??= self::resolveParams())
            ->when(! empty($only), fn ($params) => $params->filter(fn ($param) => in_array($param, $only)))
            ->when(! empty($except), fn ($params) => $params->reject(fn ($param) => in_array($param, $except)))
            ->values();
    }

    /**
     * Get the params prefixed
     *
     * @param  string[]  $only
     * @param  string[]  $except
     * @return Collection<int,string>
     */
    public static function getParamsPrefixed(string $prefix, array $only = [], array $except = []): Collection
    {
        return collect(self::getParams())
            ->when(! empty($only), fn ($params) => $params->filter(fn ($param) => in_array($param, $only)))
            ->when(! empty($except), fn ($params) => $params->reject(fn ($param) => in_array($param, $except)))
            ->map(fn (string $param) => "$prefix.$param")
            ->values();
    }

    /**
     * Get the function params prefixed
     *
     * @return array<string,mixed>
     */
    private static function resolveParams(): array
    {
        /** @var ReflectionClass<static> $reflection */
        $reflection = new ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();

        return $constructor
            ? array_map(fn ($param) => $param->getName(), $constructor->getParameters())
            : [];
    }

    /**
     * Ensure we clear the cache on destruct
     */
    public function __destruct()
    {
        unset(self::$paramCache[static::class]);
    }
}
