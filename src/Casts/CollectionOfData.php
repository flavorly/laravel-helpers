<?php

namespace Flavorly\LaravelHelpers\Casts;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Spatie\LaravelData\Contracts\BaseData as BaseDataContract;

final class CollectionOfData implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array<int, class-string>  $arguments
     * @return CastsAttributes<Collection<array-key, mixed>, iterable<array-key, mixed>>
     */
    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class($arguments) implements CastsAttributes
        {
            /** @param array<int|string, mixed> $arguments */
            public function __construct(protected array $arguments) {}

            public function get($model, $key, $value, $attributes)
            {
                if (! isset($attributes[$key])) {
                    return;
                }

                $data = Json::decode($attributes[$key]);

                $collectionClass = $this->arguments[1] ?? Collection::class;
                $dataClass = $this->arguments[0] ?? null;

                if (! is_string($dataClass)) {
                    throw new InvalidArgumentException('The provided class must be a string class name.');
                }

                if (! is_string($collectionClass)) {
                    throw new InvalidArgumentException('The provided class must be a string class name for the collection type.');
                }

                if (! is_a($collectionClass, Collection::class, true)) {
                    throw new InvalidArgumentException('The provided class must extend ['.Collection::class.'].');
                }

                if (! is_a($dataClass, BaseDataContract::class, true)) {
                    throw new InvalidArgumentException('The provided class must implement ['.BaseDataContract::class.'].');
                }

                return is_array($data)
                    ? (new $collectionClass($data))->map(fn ($item) => $dataClass::from($item))
                    : null;
            }

            public function set($model, $key, $value, $attributes)
            {
                return [$key => Json::encode($value)];
            }
        };
    }

    /**
     * Specify the collection for the cast.
     *
     * @param  class-string  $dataClass
     * @param  class-string  $collectionClass
     */
    public static function using(string $dataClass, string $collectionClass = Collection::class): string
    {
        return self::class.':'.$dataClass.','.$collectionClass;
    }
}
