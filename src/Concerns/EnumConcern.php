<?php

namespace Flavorly\LaravelHelpers\Concerns;

use Flavorly\LaravelHelpers\Data\OptionData;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait EnumConcern
{
    /**
     * Check if the current enum value is equal to the given value.
     *
     * @param  static  ...$others
     */
    public function equals(self ...$others): bool
    {
        foreach ($others as $other) {
            if ($this->value === $other->value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the current enum value is not equal to the given value.
     *
     * @param  static  ...$others
     */
    public function notEquals(self ...$others): bool
    {
        return ! $this->equals(...$others);
    }

    /**
     * Choose your pill, red or blue
     * @param  EnumConcern  ...$others
     * @return bool
     */
    public function is(self ...$others): bool
    {
        return $this->equals(...$others);
    }


    /**
     * Choose your pill, red or blue
     * @param  EnumConcern  ...$others
     * @return bool
     */
    public function isNot(self ...$others): bool
    {
        return ! $this->equals(...$others);
    }

    /**
     * Get the label translated
     * if the enum has a getLabels method it will use that instead
     */
    public function getLabel(): ?string
    {
        if ($labels = static::getLabels()) {
            return $labels[$this->value] ?? null;
        }

        return trans($this->getTranslationNamespace().'.'.$this->value);
    }

    /**
     * Override this method to provide custom labels
     */
    public static function getLabels(): ?array
    {
        return null;
    }

    /**
     * @return Collection<int, mixed>
     */
    public static function toOptions(?callable $map = null, ?callable $filter = null): Collection
    {
        $map = $map ?? fn (self $type) => new OptionData($type->getLabel() ?? '', $type->value);

        return collect(self::cases())
            ->when($filter, fn ($collection) => $collection->filter($filter))
            ->map($map);
    }

    /**
     * Often we require to pull the translation label from the enum value from other key
     */
    public function getTranslationNamespace(): string
    {
        $namespace = Str::snake(class_basename($this));

        return $this->packagePrefix().'enums.'.(Str::finish($namespace, '_enum') ? Str::replaceLast('_enum', '', $namespace) : $namespace);
    }

    /**
     * Should this be used inside a package, specify here the package prefix
     */
    public function packagePrefix(): string
    {
        return '';
    }

    /**
     * Converts the enum to an array
     */
    public static function toArray(): array
    {
        return array_map(
            fn (self $type) => $type->getLabel(),
            self::cases()
        );
    }

    /**
     * Converts the enum to a collection
     *
     * @return Collection<int, mixed>
     */
    public static function toCollection(): Collection
    {
        return collect(self::toArray());
    }

    /**
     * Converts the enum to an array of values
     */
    public static function toValues(): array
    {
        return array_map(
            fn (self $type) => $type->value,
            self::cases()
        );
    }

    /**
     * Attempts to get a enum from a label
     */
    public static function tryFromLabel(string $label): ?static
    {
        return collect(self::cases())
            ->filter(fn (self $type) => mb_strtolower($type->getLabel() ?? '') === mb_strtolower($label))
            ->first();
    }

    /**
     * Checks if the enum contains the given type
     */
    public static function contains(self $type): bool
    {
        return in_array($type, self::cases());
    }

    /**
     * Returns a random enum
     */
    public static function random(): static
    {
        return self::cases()[array_rand(self::cases())];
    }

    /**
     * Returns the first enum that matches the given value
     */
    public static function firstWhere(string $value, mixed $default = null): ?static
    {
        return collect(self::cases())
            ->first(fn (self $type) => $type->value === $value) ?? $default;
    }
}
