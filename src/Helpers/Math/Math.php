<?php

namespace Flavorly\LaravelHelpers\Helpers\Math;

use Brick\Math\BigDecimal;
use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;

final class Math
{
    public function __construct(
        /**
         * This is the actual number that we want to work with
         *
         * @var BigDecimal $number
         */
        protected float|string|int|BigDecimal $number,
        /**
         * This is the actual scale that we want to use to sums, etc, etc
         *
         * @var int $scale
         */
        protected int $scale = 2,
        /**
         * This is the scale that we want to use to store the numbers
         * So 100 with a scale of 2 will store 10000, we add the digits to the right to ensure we have enough space & precision
         *
         * @var int $storageScale
         */
        protected int $storageScale = 10,
        /**
         * How we want to round the numbers
         *
         * @var RoundingMode $roundingMode
         */
        public RoundingMode $roundingMode = RoundingMode::DOWN
    ) {
        if (! $number instanceof BigDecimal) {
            $this->number = BigDecimal::of($number);
        }
    }

    /**
     * A static factory method to create a new instance of the class.
     */
    public static function of(
        float|int|string|BigDecimal $number,
        ?int $scale = null,
        ?int $storageScale = null,
        ?RoundingMode $roundingMode = null
    ): Math {

        return new Math(
            $number,
            // @phpstan-ignore-next-line
            $scale ?? (int) config('laravel-helpers.math.scale', 10),
            // @phpstan-ignore-next-line
            $storageScale ?? (int) config('laravel-helpers.math.scale', 10),
            // @phpstan-ignore-next-line
            $roundingMode ?? config('laravel-helpers.math.rounding_mode', RoundingMode::DOWN)
        );
    }

    /**
     * @param  int|float|string|BigDecimal  ...$numbers
     *
     * @throws DivisionByZeroException
     * @throws MathException
     * @throws NumberFormatException
     */
    public static function average(...$numbers): Math
    {
        /** @var Math $sum */
        $sum = array_reduce(
            $numbers,
            fn ($carry, $num) => self::of($carry)->sum(BigDecimal::of($num)),
            BigDecimal::zero()
        );

        return self::of($sum->divide(count($numbers)));
    }

    /**
     * Converts a float, int or string to a BigDecimal
     *
     * @throws DivisionByZeroException
     * @throws NumberFormatException
     */
    public function toBigDecimal(float|int|string|BigDecimal $value): BigDecimal
    {
        if ($value instanceof BigDecimal) {
            return $value;
        }

        return BigDecimal::of($value);
    }

    /**
     * Sets the rounding mode up or down
     *
     * @return $this
     */
    public function roundingMode(RoundingMode $mode): Math
    {
        $this->roundingMode = $mode;

        return $this;
    }

    /**
     * Sets the rounding mode to down
     *
     * @return $this
     */
    public function roundDown(): Math
    {
        $this->roundingMode = RoundingMode::DOWN;

        return $this;
    }

    /**
     * Sets the rounding mode to up
     *
     * @return $this
     */
    public function roundUp(): Math
    {
        $this->roundingMode = RoundingMode::UP;

        return $this;
    }

    /**
     * Sets the scale of the number
     *
     * @return $this
     */
    public function scale(int $scale): Math
    {
        $this->scale = $scale;

        return $this;
    }

    /**
     * Sets the storage scale of the number
     *
     * @return $this
     */
    public function storageScale(int $storageScale): Math
    {
        $this->storageScale = $storageScale;

        return $this;
    }

    /**
     * Adds a value to the current number
     *
     * @throws DivisionByZeroException
     * @throws NumberFormatException
     * @throws MathException
     */
    public function sum(float|int|string $value): Math
    {
        return self::of(
            $this->number->plus($this->toBigDecimal($value)),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    /**
     * Subtracts a value from the current number
     *
     * @throws DivisionByZeroException
     * @throws NumberFormatException
     * @throws MathException
     */
    public function subtract(float|int|string $value): Math
    {
        return self::of(
            $this->number->minus($this->toBigDecimal($value)),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    /**
     * Multiplies the current number by a value
     *
     * @throws DivisionByZeroException
     * @throws NumberFormatException
     * @throws MathException
     */
    public function multiply(float|int|string $value): Math
    {
        return self::of(
            $this->number->multipliedBy($this->toBigDecimal($value)),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    /**
     * Divides the current number by a value
     *
     * @throws DivisionByZeroException
     * @throws MathException
     * @throws NumberFormatException
     */
    public function divide(float|int|string $value): Math
    {
        return self::of(
            $this->number->dividedBy($this->toBigDecimal($value), $this->scale, $this->roundingMode),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    /**
     * Raises the current number to the power of an exponent
     */
    public function pow(int $exponent): Math
    {
        return self::of(
            $this->number->power($exponent),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    /**
     * Rounds the current number to the given precision
     *
     * @throws RoundingNecessaryException
     */
    public function round(int $precision = 0, ?RoundingMode $roundingMode = null): Math
    {
        return self::of(
            $this->number->toScale($precision, $roundingMode ?? $this->roundingMode),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    /**
     * Rounds the current number up to the nearest
     *
     * @throws MathException
     */
    public function ceil(): Math
    {
        return self::of(
            $this->number->dividedBy(BigDecimal::one(), 0, RoundingMode::CEILING),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    /**
     * Rounds the current number down to the nearest
     *
     * @throws MathException
     */
    public function floor(): Math
    {
        return self::of(
            $this->number->dividedBy(BigDecimal::one(), 0, RoundingMode::FLOOR),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    /**
     * Returns the absolute value of the current number
     */
    public function absolute(): Math
    {
        return self::of(
            $this->number->abs(),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    /**
     * Returns the negative value of the current number
     *
     * @return $this|self
     */
    public function negative(): Math
    {
        if ($this->number->isNegative()) {
            return $this;
        }

        return self::of(
            $this->number->negated(),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    /**
     * Adds a percentage to the current number
     *
     * @throws DivisionByZeroException
     * @throws MathException
     * @throws NumberFormatException
     */
    public function addPercentage(float|int|string $percentage): Math
    {
        $percentageValue = $this
            ->number
            ->multipliedBy($this->toBigDecimal($percentage))
            ->dividedBy(100, $this->scale, $this->roundingMode);

        return self::of(
            $this->number->plus($percentageValue),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    /**
     * Subtracts a percentage from the current number
     *
     * @throws DivisionByZeroException
     * @throws MathException
     * @throws NumberFormatException
     */
    public function subtractPercentage(float|int|string $percentage): Math
    {
        $percentageValue = $this
            ->number
            ->multipliedBy($this->toBigDecimal($percentage))
            ->dividedBy(100, $this->scale, $this->roundingMode);

        return self::of(
            $this->number->minus($percentageValue),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    /**
     * Compares the current number to another value
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function compare(float|int|string $value): int
    {
        $other = self::of(
            $value,
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );

        return $this->number->compareTo($other->number->toBigDecimal());
    }

    /**
     * Checks if the current number is less than another value
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function isLessThan(float|int|string $value): bool
    {
        return $this->compare($value) < 0;
    }

    /**
     * Checks if the current number is less than or equal to another value
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function isLessThanOrEqual(float|int|string $value): bool
    {
        return $this->compare($value) <= 0;
    }

    /**
     * Checks if the current number is greater than another value
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function isGreaterThan(float|int|string $value): bool
    {
        return $this->compare($value) > 0;
    }

    /**
     * Checks if the current number is greater than or equal to another value
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function isGreaterThanOrEqual(float|int|string $value): bool
    {
        return $this->compare($value) >= 0;
    }

    /**
     * Calculates the specified percentage of the current number.
     *
     * @throws DivisionByZeroException
     * @throws MathException
     * @throws NumberFormatException
     */
    public function toPercentageOf(float|int|string $percentage): Math
    {
        $percentageValue = $this->toBigDecimal($percentage)->dividedBy(100, $this->scale, $this->roundingMode);

        return self::of(
            $this->number->multipliedBy($percentageValue),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    /**
     * Get the percentage of the current number compared to the given total
     *
     * @throws DivisionByZeroException
     * @throws MathException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     */
    public function percentageOf(float|int|string|BigDecimal $total): float
    {
        $percentage = $this->number
            ->multipliedBy(BigDecimal::of(100))
            ->dividedBy($this->toBigDecimal($total), $this->scale, $this->roundingMode);

        return $percentage->toScale($this->scale, $this->roundingMode)->toFloat();
    }

    /**
     * Calculates the percentage difference between two numbers
     *
     * @throws DivisionByZeroException
     * @throws MathException
     * @throws NumberFormatException
     */
    public function differenceInPercentage(float|int|string|BigDecimal $value): float
    {
        $original = $this->number;
        $comparisonValue = $this->toBigDecimal($value);
        $difference = $original->minus($comparisonValue)->abs();

        if ($original->isZero() && $comparisonValue->isZero()) {
            return 0.0;
        }

        if ($original->isZero()) {
            return 100.0;
        }

        $percentage = $difference
            ->multipliedBy(100)
            ->dividedBy($original->abs(), $this->scale, $this->roundingMode);

        return $percentage->toScale($this->scale, $this->roundingMode)->toFloat();
    }

    /**
     * Checks if the current number is equal to another value
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function isEqual(float|int|string $value): bool
    {
        return $this->compare($value) === 0;
    }

    /**
     * Checks if the current number is zero
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function isZero(): bool
    {
        return $this->number->toScale($this->scale, $this->roundingMode)->isZero();
    }

    /**
     * Checks if the current number is not zero
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function isNotZero(): bool
    {
        return ! $this->isZero();
    }

    /**
     * Ensures the scale of the current number
     *
     * @throws RoundingNecessaryException
     */
    public function ensureScale(): Math
    {
        return self::of(
            $this->number->toScale($this->scale, $this->roundingMode),
            $this->scale,
            $this->storageScale,
            $this->roundingMode,
        );
    }

    /**
     * Returns the current number as an integer to save on storage
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function toStorageScale(): int
    {
        $decimalPlaces = BigDecimal::of(10)->power($this->storageScale);
        $numberRounded = $this->ensureScale();

        return $numberRounded
            ->multiply($decimalPlaces)
            ->ensureScale()
            ->toInt();
    }

    /**
     * Returns the current number from the storage as a Math object
     *
     * @throws DivisionByZeroException
     * @throws MathException
     * @throws NumberFormatException
     */
    public function fromStorage(): Math
    {
        $decimalPlaces = BigDecimal::of(10)->power($this->storageScale);

        return self::of(
            $this->number->dividedBy($decimalPlaces, $this->scale, $this->roundingMode),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    /**
     * Returns the current number as a BigDecimal
     */
    public function toNumber(): BigDecimal
    {
        if ($this->number instanceof BigDecimal) {
            return $this->number;
        }

        return BigDecimal::of($this->number);
    }

    /**
     * Returns the current number as an integer
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function toInt(): int
    {
        return $this->number->toScale(0, $this->roundingMode)->toInt();
    }

    /**
     * Returns the current number as a float
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function toFloat(): float
    {
        return $this->number->toScale($this->scale, $this->roundingMode)->toFloat();
    }

    /**
     * Returns the current number as a string
     *
     * @throws RoundingNecessaryException
     */
    public function toString(): string
    {
        return $this->number->toScale($this->scale, $this->roundingMode)->__toString();
    }

    /**
     * Formats the current number to a string
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function format(string $thousandsSeparator = ',', string $decimalPoint = '.'): string
    {
        return number_format($this->toFloat(), $this->scale, $decimalPoint, $thousandsSeparator);
    }

    /**
     * Returns the current number as a string
     *
     * @throws RoundingNecessaryException
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
