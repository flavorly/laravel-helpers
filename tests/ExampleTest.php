<?php

use Brick\Math\RoundingMode;
use Flavorly\LaravelHelpers\Helpers\Math\Math;

beforeEach(function () {
    $this->math = new Math();
});

it('performs basic sum operations', function ($initial, $addend, $expected, $scale = null) {
    $math = $scale !== null ? new Math($scale) : $this->math;
    $result = $math->of($initial)->sum($addend)->toFloat();
    expect($result)->toBe($expected);
})->with([
    'integer addition' => [5, 3, 8.00],
    'float addition' => [5.5, 3.3, 8.80],
    'negative number addition' => [5, -3, 2.00],
    'string number addition' => ['5.5', '3.3', 8.80],
    'large number addition' => [1000000, 2000000, 3000000.00],
    'small number addition' => [0.001, 0.002, 0.00], // Rounds to 0.00 with default scale
    'custom scale addition' => [0.001, 0.002, 0.0030, 4],
    'different type addition' => [5, '3.3', 8.30],
    'zero addition' => [5, 0, 5.00],
    'addition resulting in negative' => [5, -10, -5.00],
]);

it('can chain sum operations', function () {
    $result = $this->math->of(5)
        ->sum(3)
        ->sum(2)
        ->sum(1.5)
        ->toString();

    expect($result)->toBe('11.50');
});

it('handles different scales and rounding modes correctly', function () {
    // Test with HALF_DOWN (default)
    $mathHalfDown = new Math(4, RoundingMode::HALF_DOWN);
    $resultHalfDown = $mathHalfDown->of(1.23456)->sum(2.34567)->toFloat();
    expect($resultHalfDown)->toBe(3.5802);

    // Let's see what happens with HALF_UP
    $mathHalfUp = new Math(4, RoundingMode::HALF_UP);
    $resultHalfUp = $mathHalfUp->of(1.23456)->sum(2.34567)->toFloat();
    expect($resultHalfUp)->toBe(3.5802);

    // Let's check the exact value before rounding
    $mathExact = new Math(10, RoundingMode::UNNECESSARY);
    $resultExact = $mathExact->of(1.23456)->sum(2.34567)->toString();
    expect($resultExact)->toBe('3.5802300000');

    // Test rounding at different scales
    $mathScale3 = new Math(3, RoundingMode::HALF_DOWN);
    $resultScale3 = $mathScale3->of(1.23456)->sum(2.34567)->toFloat();
    expect($resultScale3)->toBe(3.580);

    $mathScale5 = new Math(5, RoundingMode::HALF_DOWN);
    $resultScale5 = $mathScale5->of(1.23456)->sum(2.34567)->toFloat();
    expect($resultScale5)->toBe(3.58023);
});

it('preserves immutability in sum operations', function () {
    $math = new Math();
    $initial = $math->of(5);
    $result1 = $initial->sum(3);
    $result2 = $initial->sum(2);

    expect($result1->toString())->toBe('8.00');
    expect($result2->toString())->toBe('7.00');
    expect($initial->toString())->toBe('5.00');
});

it('handles very small numbers correctly', function () {
    $result = $this->math->of(0.001)->sum(0.002)->toString();
    expect($result)->toBe('0.00'); // With default scale 2

    $math = new Math(3);
    $result = $math->of(0.001)->sum(0.002)->toString();
    expect($result)->toBe('0.003'); // With scale 3
});
