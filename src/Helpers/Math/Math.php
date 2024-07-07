<?php

namespace Flavorly\LaravelHelpers\Helpers\Math;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

final class Math
{
    protected BigDecimal $number;
    protected int $scale;

    public function __construct(
        protected int $defaultScale = 2,
        protected RoundingMode $roundingMode = RoundingMode::HALF_DOWN
    ) {}

    public function of(float|int|string $number, ?int $scale = null): self
    {
        return (new Math($this->defaultScale, $this->roundingMode))->init($number, $scale);
    }

    protected function init(float|int|string $number, ?int $scale = null): self
    {
        $this->number = BigDecimal::of($number);
        $this->scale = $scale ?? $this->defaultScale;
        return $this;
    }

    protected function toBigDecimal(float|int|string $value): BigDecimal
    {
        // We cant use toScale here because it will round the number, and we want to keep the rounding mode
        return BigDecimal::of($value);
    }

    public function roundingMode(RoundingMode $mode): self
    {
        $this->roundingMode = $mode;
        return $this;
    }

    public function sum(float|int|string $value): self
    {
        $this->of($this->number->plus($this->toBigDecimal($value)));
        return $this;
    }

    public function subtract(float|int|string $value): self
    {
        $this->of($this->number->minus($this->toBigDecimal($value)));
        return $this;
    }

    public function multiply(float|int|string $value): self
    {
        $this->of($this->number->multipliedBy($this->toBigDecimal($value)));
        return $this;
    }

    public function divide(float|int|string $value): self
    {
        $this->of($this->number->dividedBy($this->toBigDecimal($value), $this->scale, $this->roundingMode));
        return $this;
    }

    public function pow(int $exponent): self
    {
        $this->of($this->number->power($exponent));
        return $this;
    }

    public function round(int $precision = 0, ?RoundingMode $roundingMode = null): self
    {
        $this->of($this->number->toScale($precision, $roundingMode ?? $this->roundingMode));
        return $this;
    }

    public function ceil(): self
    {
        $this->of($this->number->dividedBy(BigDecimal::one(), 0, RoundingMode::CEILING));
        return $this;
    }

    public function floor(): self
    {
        $this->of($this->number->dividedBy(BigDecimal::one(), 0, RoundingMode::FLOOR));
        return $this;
    }

    public function absolute(): self
    {
        $this->number = $this->number->abs();
        return $this;
    }

    public function negative(): self
    {
        if($this->number->isNegative()){
            return $this;
        }
        $this->number = $this->number->negated();
        return $this;
    }

    public function addPercentage(float|int|string $percentage): self
    {
        $percentageValue = $this
            ->number
            ->multipliedBy($this->toBigDecimal($percentage))
            ->dividedBy(100, $this->scale, $this->roundingMode);

        $newNumber = $this->number->plus($percentageValue);
        return $this->of($newNumber, $this->scale);
    }

    public function subtractPercentage(float|int|string $percentage): self
    {
        $percentageValue = $this
            ->number
            ->multipliedBy($this->toBigDecimal($percentage))
            ->dividedBy(100, $this->scale, $this->roundingMode);

        $newNumber = $this->number->minus($percentageValue);
        return $this->of($newNumber, $this->scale);
    }

    public function compare(float|int|string $value): int
    {
        return $this->number->compareTo($this->toBigDecimal($value));
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
        return $this->number->toInt();
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
