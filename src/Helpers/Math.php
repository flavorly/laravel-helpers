<?php

namespace Flavorly\LaravelHelpers\Helpers;

use Brick\Math\BigDecimal;
use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;

/**
 * A simple wrapper around Brick\Math that ensures a better interface
 * But also providers a correct conversion from float to integer
 * in order to be saved in the database without any issues
 * Please see Brick\Money for more information about the concept
 * on why we use Integers instead of floats
 */
class Math
{
    public function __construct(
        protected int $floatScale,
        protected int $integerScale = 20,
    ) {}

    /**
     * Converts a float into a integer based on the given scale
     *
     * @throws MathException
     */
    public function floatToInt(float|int|string $value): string
    {
        $decimalPlaces = $this->powTen($this->floatScale);

        return $this->round(
            $this->mul(
                $value,
                $decimalPlaces,
                $this->floatScale
            )
        );
    }

    /**
     * Converts a big integer into a float based on the given scale
     *
     * @throws MathException
     */
    public function intToFloat(float|int|string $value): string
    {
        $decimalPlaces = $this->powTen($this->floatScale);

        return $this->div($value, $decimalPlaces, $this->floatScale);
    }

    /**
     * Sums to big integers
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function addInteger(float|int|string $first, float|int|string $second, ?int $scale = null): string
    {
        return $this->add(
            $this->floatToInt($first),
            $this->floatToInt($second),
        );
    }

    /**
     * Adds a percentage to a big integer
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function addPercentageInteger(float|int|string $number, float|int|string $percentage, ?int $scale = null): string
    {
        $intNumber = $this->floatToInt($number);
        $percentageValue = $this->div($this->mul($intNumber, $this->floatToInt($percentage)), $this->floatToInt(100), $scale);

        return $this->add($intNumber, $percentageValue);
    }

    /**
     * Subtracts two big integers
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function subInteger(float|int|string $first, float|int|string $second, ?int $scale = null): string
    {
        return $this->sub(
            $this->floatToInt($first),
            $this->floatToInt($second)
        );
    }

    /**
     * Subtracts a percentage from a big integer
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function subtractPercentageInteger(float|int|string $number, float|int|string $percentage, ?int $scale = null): string
    {
        $intNumber = $this->floatToInt($number);
        $percentageValue = $this->div($this->mul($intNumber, $this->floatToInt($percentage)), $this->floatToInt(100), $scale);

        return $this->sub($intNumber, $percentageValue);
    }

    /**
     * Divides two big integers
     *
     * @throws MathException
     */
    public function divInteger(float|int|string $first, float|int|string $second, ?int $scale = null): string
    {
        return $this->div(
            $this->floatToInt($first),
            $this->floatToInt($second),
            $scale ?? $this->floatScale
        );
    }

    /**
     * Multiplies two big integers
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function mulInteger(float|int|string $first, float|int|string $second, ?int $scale = null): string
    {
        return $this->mul(
            $this->floatToInt($first),
            $this->floatToInt($second)
        );
    }

    /**
     * Raises a big integer to the power of another big integer
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function powInteger(float|int|string $first, float|int|string $second, ?int $scale = null): string
    {
        return $this->pow(
            $this->floatToInt($first),
            $this->floatToInt($second),
        );
    }

    /**
     * Powers a big integer to the power of ten
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function powTenInteger(float|int|string $number): string
    {
        return $this->powTen($this->floatToInt($number));
    }

    /**
     * Ceils a big integer
     *
     * @throws MathException
     */
    public function ceilInteger(float|int|string $number): string
    {
        return $this->ceil($this->floatToInt($number));
    }

    /**
     * Floors a big integer
     *
     * @throws MathException
     */
    public function floorInteger(float|int|string $number): string
    {
        return $this->floor($this->floatToInt($number));
    }

    /**
     * Rounds a big integer
     *
     * @throws MathException
     */
    public function roundInteger(float|int|string $number, int $precision = 0): string
    {
        return $this->round($this->floatToInt($number), $precision);
    }

    /**
     * Absolutes a big integer
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function absInteger(float|int|string $number): string
    {
        return $this->abs($this->floatToInt($number));
    }

    /**
     * Negatives a big integer
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function negativeInteger(float|int|string $number): string
    {
        return $this->negative($this->floatToInt($number));
    }

    /**
     * Compares two big integers
     *
     * @throws MathException
     */
    public function compareInteger(float|int|string $first, float|int|string $second): int
    {
        return $this->compare($this->floatToInt($first), $this->floatToInt($second));
    }

    /**
     * Ensures the scale of a big integer
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function ensureScale(float|int|string $number): string
    {
        return $this->mul($number, 1);
    }

    /**
     * Sums two floats
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function add(float|int|string $first, float|int|string $second, ?int $scale = null): string
    {
        return (string) BigDecimal::of($first)
            ->plus(BigDecimal::of($second))
            ->toScale($scale ?? $this->floatScale, RoundingMode::DOWN);
    }

    /**
     * Adds a percentage to a number
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function addPercentage(float|int|string $number, float|int|string $percentage, ?int $scale = null): string
    {
        $percentageValue = $this->div($this->mul($number, $percentage), 100, $scale);

        return $this->add($number, $percentageValue, $scale);
    }

    /**
     * Subtracts two floats
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function sub(float|int|string $first, float|int|string $second, ?int $scale = null): string
    {
        return (string) BigDecimal::of($first)
            ->minus(BigDecimal::of($second))
            ->toScale($scale ?? $this->floatScale, RoundingMode::DOWN);
    }

    /**
     * Subtracts a percentage from a number
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function subtractPercentage(float|int|string $number, float|int|string $percentage, ?int $scale = null): string
    {
        $percentageValue = $this->div($this->mul($number, $percentage), 100, $scale);

        return $this->sub($number, $percentageValue, $scale);
    }

    /**
     * Divides two floats
     *
     * @throws MathException
     */
    public function div(float|int|string $first, float|int|string $second, ?int $scale = null): string
    {
        return (string) BigDecimal::of($first)
            ->dividedBy(BigDecimal::of($second), $scale ?? $this->floatScale, RoundingMode::DOWN);
    }

    /**
     * Multiplies two floats
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function mul(float|int|string $first, float|int|string $second, ?int $scale = null): string
    {
        return (string) BigDecimal::of($first)
            ->multipliedBy(BigDecimal::of($second))
            ->toScale($scale ?? $this->floatScale, RoundingMode::DOWN);
    }

    /**
     * Raises a float to the power of another float
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function pow(float|int|string $first, float|int|string $second, ?int $scale = null): string
    {
        return (string) BigDecimal::of($first)
            ->power((int) $second)
            ->toScale($scale ?? $this->floatScale, RoundingMode::DOWN);
    }

    /**
     * Powers a float to the power of ten
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function powTen(float|int|string $number): string
    {
        return $this->pow(10, $number);
    }

    /**
     * Ceils a float
     *
     * @throws MathException
     */
    public function ceil(float|int|string $number): string
    {
        return (string) BigDecimal::of($number)
            ->dividedBy(BigDecimal::one(), 0, RoundingMode::CEILING);
    }

    /**
     * Floors a float
     *
     * @throws MathException
     */
    public function floor(float|int|string $number): string
    {
        return (string) BigDecimal::of($number)
            ->dividedBy(BigDecimal::one(), 0, RoundingMode::FLOOR);
    }

    /**
     * Rounds a float
     *
     * @throws MathException
     */
    public function round(float|int|string $number, int $precision = 0): string
    {
        return (string) BigDecimal::of($number)
            ->dividedBy(BigDecimal::one(), $precision, RoundingMode::HALF_UP);
    }

    /**
     * Absolutes a float
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function abs(float|int|string $number, ?int $scale = null): string
    {
        return (string) BigDecimal::of($number)->abs()->toScale($scale ?? $this->floatScale, RoundingMode::DOWN);
    }

    /**
     * Negatives a float
     *
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function negative(float|int|string $number, ?int $scale = null): string
    {
        $number = BigDecimal::of($number);
        if ($number->isNegative()) {
            return (string) $number->toScale($scale ?? $this->floatScale, RoundingMode::DOWN);
        }

        return (string) BigDecimal::of($number)
            ->toScale($scale ?? $this->floatScale, RoundingMode::DOWN)
            ->negated();
    }

    /**
     * Compares two floats
     *
     * @throws MathException
     */
    public function compare(float|int|string $first, float|int|string $second): int
    {
        return BigDecimal::of($first)->compareTo(BigDecimal::of($second));
    }

    /**
     * Check if its zero
     *
     * @throws DivisionByZeroException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     */
    public function isZero(float|int|string $number, ?int $scale = null): bool
    {
        return BigDecimal::of($number)
            ->toScale($scale ?? $this->floatScale, RoundingMode::DOWN)
            ->isZero();
    }

    /**
     * Check if its not zero
     *
     * @throws DivisionByZeroException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     */
    public function isNotZero(float|int|string $number, ?int $scale = null): bool
    {
        return ! $this->isZero($number, $scale);
    }

    /**
     * Returns the representation of the number as a string
     *
     * @throws DivisionByZeroException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     */
    public function toToNumber(float|int|string $number, ?int $scale = null): BigDecimal
    {
        return BigDecimal::of($number)
            ->toScale($scale ?? $this->floatScale, RoundingMode::DOWN);
    }

    /**
     * Check if two numbers are equal
     *
     * @throws DivisionByZeroException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     */
    public function isEqual(float|int|string $first, float|int|string $second, ?int $scale = null): bool
    {
        $firstScaled = BigDecimal::of($first)
            ->toScale($scale ?? $this->floatScale, RoundingMode::DOWN);

        $secondScaled = BigDecimal::of($second)
            ->toScale($scale ?? $this->floatScale, RoundingMode::DOWN);

        return $firstScaled->isEqualTo($secondScaled);
    }
}
