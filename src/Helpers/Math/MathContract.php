<?php

namespace Flavorly\LaravelHelpers\Helpers\Math;

use Brick\Math\BigDecimal;

interface MathContract
{
    public function floatToInt(float|int|string $value): BigDecimal;

    public function intToFloat(float|int|string $value): BigDecimal;

    public function add(float|int|string $first, float|int|string $second, ?int $scale = null): BigDecimal;

    public function addPercentage(float|int|string $number, float|int|string $percentage, ?int $scale = null): BigDecimal;

    public function sub(float|int|string $first, float|int|string $second, ?int $scale = null): BigDecimal;

    public function subtractPercentage(float|int|string $number, float|int|string $percentage, ?int $scale = null): BigDecimal;

    public function div(float|int|string $first, float|int|string $second, ?int $scale = null): BigDecimal;

    public function mul(float|int|string $first, float|int|string $second, ?int $scale = null): BigDecimal;

    public function pow(float|int|string $first, float|int|string $second, ?int $scale = null): BigDecimal;

    public function powTen(float|int|string $number): BigDecimal;

    public function ceil(float|int|string $number): BigDecimal;

    public function floor(float|int|string $number): BigDecimal;

    public function round(float|int|string $number, int $precision = 0): BigDecimal;

    public function abs(float|int|string $number, ?int $scale = null): BigDecimal;

    public function negative(float|int|string $number, ?int $scale = null): BigDecimal;

    public function compare(float|int|string $first, float|int|string $second): int;

    public function ensureScale(float|int|string $number): BigDecimal;

    public function isZero(float|int|string $number, ?int $scale = null): bool;

    public function isNotZero(float|int|string $number, ?int $scale = null): bool;

    public function toToNumber(float|int|string $number, ?int $scale = null): BigDecimal;

    public function isEqual(float|int|string $first, float|int|string $second, ?int $scale = null): bool;
}
