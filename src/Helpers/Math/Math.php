<?php

namespace Flavorly\LaravelHelpers\Helpers\Math;

use Brick\Math\BigDecimal;
use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\NumberFormatException;
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
        protected RoundingMode $roundingMode = RoundingMode::DOWN
    ) {
        if(!$number instanceof BigDecimal){
            $this->number = BigDecimal::of($number);
        }
    }

    /**
     * A static factory method to create a new instance of the class.
     *
     * @param  float|int|string  $number
     * @param  int|null  $scale
     * @param  int|null  $storageScale
     * @param  RoundingMode|null  $roundingMode
     * @return Math
     */
    public static function of(
        float|int|string $number,
        ?int $scale = null,
        ?int $storageScale = null,
        ?RoundingMode $roundingMode = null
    ): self
    {
        return new self(
            $number,
            $scale ?? 2,
            $storageScale ?? 10,
            $roundingMode ?? RoundingMode::DOWN
        );
    }

    public function toBigDecimal(float|int|string $value): BigDecimal
    {
        return BigDecimal::of($value);
    }

    public function roundingMode(RoundingMode $mode): self
    {
        $this->roundingMode = $mode;
        return $this;
    }

    public function roundDown(): self
    {
        $this->roundingMode = RoundingMode::DOWN;
        return $this;
    }

    public function roundUp(): self
    {
        $this->roundingMode = RoundingMode::UP;
        return $this;
    }

    public function scale(int $scale): self
    {
        $this->scale = $scale;
        return $this;
    }

    public function storageScale(int $storageScale): self
    {
        $this->storageScale = $storageScale;

        return $this;
    }

    public function sum(float|int|string $value): self
    {
        return self::of(
            $this->number->plus($this->toBigDecimal($value)),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    public function subtract(float|int|string $value): self
    {
        return self::of(
            $this->number->minus($this->toBigDecimal($value)),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    public function multiply(float|int|string $value): self
    {
        return self::of(
            $this->number->multipliedBy($this->toBigDecimal($value)),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    public function divide(float|int|string $value): self
    {
        return self::of(
            $this->number->dividedBy($this->toBigDecimal($value), $this->scale, $this->roundingMode),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    public function pow(int $exponent): self
    {
        return self::of(
            $this->number->power($exponent),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    public function round(int $precision = 0, ?RoundingMode $roundingMode = null): self
    {
        return self::of(
            $this->number->toScale($precision, $roundingMode ?? $this->roundingMode),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    public function ceil(): self
    {
        return self::of(
            $this->number->dividedBy(BigDecimal::one(), 0, RoundingMode::CEILING),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    public function floor(): self
    {
        return self::of(
            $this->number->dividedBy(BigDecimal::one(), 0, RoundingMode::FLOOR),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    public function absolute(): self
    {
        return self::of(
            $this->number->abs(),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    public function negative(): self
    {
        if($this->number->isNegative()){
            return $this;
        }

        return self::of(
            $this->number->negated(),
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
    }

    public function addPercentage(float|int|string $percentage): self
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

    public function subtractPercentage(float|int|string $percentage): self
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

    public function compare(float|int|string $value): int
    {
        // TODO: Check this.
        $other = self::of(
            $value,
            $this->scale,
            $this->storageScale,
            $this->roundingMode
        );
        return $this->number->compareTo($other->number->toBigDecimal());
    }

    public function isLessThan(float|int|string $value): bool
    {
        return $this->compare($value) < 0;
    }

    public function isLessThanOrEqual(float|int|string $value): bool
    {
        return $this->compare($value) <= 0;
    }

    public function isGreaterThan(float|int|string $value): bool
    {
        return $this->compare($value) > 0;
    }

    public function isGreaterThanOrEqual(float|int|string $value): bool
    {
        return $this->compare($value) >= 0;
    }

    public function isEqual(float|int|string $value): bool
    {
        return $this->compare($value) === 0;
    }

    public function isZero(): bool
    {
        return $this->number->toScale($this->scale, $this->roundingMode)->isZero();
    }

    public function isNotZero(): bool
    {
        return !$this->isZero();
    }

    public function toInt(): int
    {
        return $this->number->toScale(0, $this->roundingMode)->toInt();
    }

    public function toFloat(): float
    {
        return $this->number->toScale($this->scale, $this->roundingMode)->toFloat();
    }

    public function toString(): string
    {
        return $this->number->toScale($this->scale, $this->roundingMode)->__toString();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
