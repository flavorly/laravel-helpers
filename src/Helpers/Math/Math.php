<?php

namespace Flavorly\LaravelHelpers\Helpers\Math;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

final class Math
{
    protected BigDecimal $number;

    public function __construct(
        protected int $scale = 2,
        protected RoundingMode $roundingMode = RoundingMode::DOWN
    ) {}

    public static function of(float|int|string $number, ?int $scale = null, ?RoundingMode $roundingMode = null): self
    {
        $instance = new self($scale ?? 2, $roundingMode ?? RoundingMode::DOWN);
        $instance->number = BigDecimal::of($number);
        return $instance;
    }

    public function toBigDecimal(float|int|string $value): BigDecimal
    {
        // We cant use toScale here because it will round the number, and we want to keep the rounding mode
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

    public function sum(float|int|string $value): self
    {
        $new = new self($this->scale, $this->roundingMode);
        $new->number = $this->number->plus(BigDecimal::of($value));
        return $new;
    }

    public function subtract(float|int|string $value): self
    {
        $new = new self($this->scale, $this->roundingMode);
        $new->number = $this->number->minus($this->toBigDecimal($value));
        return $new;
    }

    public function multiply(float|int|string $value): self
    {
        $new = new self($this->scale, $this->roundingMode);
        $new->number = $this->number->multipliedBy($this->toBigDecimal($value));
        return $new;
    }

    public function divide(float|int|string $value): self
    {
        $new = new self($this->scale, $this->roundingMode);
        $new->number = $this->number->dividedBy($this->toBigDecimal($value), $this->scale, $this->roundingMode);
        return $new;
    }

    public function pow(int $exponent): self
    {
        $new = new self($this->scale, $this->roundingMode);
        $new->number = $this->number->power($exponent);
        return $new;
    }

    public function round(int $precision = 0, ?RoundingMode $roundingMode = null): self
    {
        $new = new self($this->scale, $this->roundingMode);
        $new->number = $this->number->toScale($precision, $roundingMode ?? $this->roundingMode);

        return $new;
    }

    public function ceil(): self
    {
        $new = new self($this->scale, $this->roundingMode);
        $new->number = $this->number->dividedBy(BigDecimal::one(), 0, RoundingMode::CEILING);

        return $new;
    }

    public function floor(): self
    {
        $new = new self($this->scale, $this->roundingMode);
        $new->number = $this->number->dividedBy(BigDecimal::one(), 0, RoundingMode::FLOOR);

        return $new;
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

        $new = new self($this->scale, $this->roundingMode);
        $new->number = $newNumber;

        return $new;
    }

    public function subtractPercentage(float|int|string $percentage): self
    {
        $percentageValue = $this
            ->number
            ->multipliedBy($this->toBigDecimal($percentage))
            ->dividedBy(100, $this->scale, $this->roundingMode);

        $newNumber = $this->number->minus($percentageValue);

        $new = new self($this->scale, $this->roundingMode);
        $new->number = $newNumber;

        return $new;
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
