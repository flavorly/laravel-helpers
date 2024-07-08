<?php

use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\NegativeNumberException;
use Brick\Math\RoundingMode;
use Flavorly\LaravelHelpers\Helpers\Math\Math;

beforeEach(function () {
    config()->set('helpers.math.scale', 2);
    config()->set('helpers.math.storage_scale', 10);
    config()->set('helpers.math.rounding_mode', RoundingMode::DOWN);
});

it('performs basic sum operations', function ($initial, $addend, $expected, $scale = null) {
    $math = Math::of($initial, $scale);
    $result = $math->sum($addend)->toFloat();
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
    $result = Math::of(5)
        ->sum(3)
        ->sum(2)
        ->sum(1.5)
        ->toString();

    expect($result)->toBe('11.50');
});

it('handles different scales and rounding modes correctly', function () {
    // Test with HALF_DOWN (default)
    $resultHalfDown = Math::of(1.23456)
        ->scale(4)
        ->roundDown()
        ->sum(2.34567)
        ->toFloat();
    expect($resultHalfDown)->toBe(3.5802);

    // Test with HALF_UP
    $resultHalfUp = Math::of(1.23456)
        ->scale(4)
        ->roundUp()
        ->sum(2.34567)
        ->toFloat();
    expect($resultHalfUp)->toBe(3.5803);

    // Check the exact value before rounding
    $resultExact = Math::of(1.23456)
        ->scale(10)
        ->sum(2.34567)
        ->toString();
    expect($resultExact)->toBe('3.5802300000');

    // Test rounding at different scales
    $resultScale3 = Math::of(1.23456)
        ->scale(3)
        ->roundDown()
        ->sum(2.34567)
        ->toFloat();
    expect($resultScale3)->toBe(3.580);

    $resultScale5 = Math::of(1.23456)
        ->scale(5)
        ->roundDown()
        ->sum(2.34567)
        ->toFloat();
    expect($resultScale5)->toBe(3.58023);
});

it('handles edge cases correctly', function () {
    // Testing numbers that are exactly at the rounding point
    $resultEdge = Math::of(1.23455)
        ->scale(4)
        ->roundUp()
        ->sum(2.34565)
        ->toFloat();
    expect($resultEdge)->toBe(3.5802);

    // Testing negative numbers
    $resultNegative = Math::of(-1.23456)
        ->scale(4)
        ->roundDown()
        ->sum(-2.34567)
        ->toFloat();
    expect($resultNegative)->toBe(-3.5802);
});

it('preserves immutability in sum operations', function () {
    $initial = Math::of(5);
    $result1 = $initial->sum(3);
    $result2 = $initial->sum(2);

    expect($result1->toString())->toBe('8.00');
    expect($result2->toString())->toBe('7.00');
    expect($initial->toString())->toBe('5.00');
});

it('handles very small numbers correctly', function () {
    // Default scale (2)
    $result1 = Math::of(0.001)->sum(0.002)->toString();
    expect($result1)->toBe('0.00');

    // Scale 3
    $result2 = Math::of(0.001)
        ->scale(3)
        ->sum(0.002)
        ->toString();
    expect($result2)->toBe('0.003');

    // Scale 4
    $result3 = Math::of(0.0001)
        ->scale(4)
        ->sum(0.0002)
        ->toString();
    expect($result3)->toBe('0.0003');

    // Scale 5 with subtraction
    $result4 = Math::of(0.00001)
        ->scale(5)
        ->subtract(0.00002)
        ->toString();
    expect($result4)->toBe('-0.00001');

    // Scale 6 with multiplication
    $result5 = Math::of(0.000001)
        ->scale(6)
        ->multiply(1000)
        ->toString();
    expect($result5)->toBe('0.001000');

    // Scale 7 with division
    $result6 = Math::of(0.0000001)
        ->scale(7)
        ->divide(0.1)
        ->toString();
    expect($result6)->toBe('0.0000010');

    // Handling very small numbers with rounding
    $result7 = Math::of(0.0000001)
        ->scale(6)
        ->roundUp()
        ->toString();
    expect($result7)->toBe('0.000001');

    $result8 = Math::of(0.0000001)
        ->scale(6)
        ->roundDown()
        ->toString();
    expect($result8)->toBe('0.000000');

    $result9 = Math::of(0.0000005)
        ->scale(6)
        ->roundUp()
        ->toString();
    expect($result9)->toBe('0.000001');

    $result10 = Math::of(0.0000005)
        ->scale(6)
        ->roundDown()
        ->toString();
    expect($result10)->toBe('0.000000');

    // Testing the default HALF_DOWN behavior
    $result11 = Math::of(0.0000005)
        ->scale(6)
        ->toString();
    expect($result11)->toBe('0.000000');

    $result12 = Math::of(0.0000006)
        ->scale(6)
        ->toString();
    expect($result12)->toBe('0.000000');
});

it('performs basic arithmetic operations correctly', function () {
    // Subtraction
    expect(Math::of(10)->subtract(3)->toFloat())->toBe(7.00);
    expect(Math::of(5.5)->subtract(2.2)->toFloat())->toBe(3.30);
    expect(Math::of(1)->subtract(1.1)->toFloat())->toBe(-0.10);

    // Multiplication
    expect(Math::of(5)->multiply(3)->toFloat())->toBe(15.00);
    expect(Math::of(2.5)->multiply(2.5)->toFloat())->toBe(6.25);
    expect(Math::of(100)->multiply(0.1)->toFloat())->toBe(10.00);

    // Division
    // Division with different scales
    expect(Math::of(1)->scale(3)->divide(3)->toFloat())->toBe(0.333);
    expect(Math::of(1)->scale(4)->divide(3)->toFloat())->toBe(0.3333);
    expect(Math::of(1)->scale(5)->divide(3)->toFloat())->toBe(0.33333);

    // Division with rounding
    expect(Math::of(1)->scale(2)->divide(3)->toFloat())->toBe(0.33);
    expect(Math::of(1)->scale(2)->roundUp()->divide(3)->toFloat())->toBe(0.34);

    // More complex division scenarios
    expect(Math::of(10)->scale(4)->divide(3)->toFloat())->toBe(3.3333);
    expect(Math::of(2)->scale(3)->divide(3)->toFloat())->toBe(0.666);

    // Division resulting in repeating decimals
    expect(Math::of(1)->scale(6)->divide(7)->toFloat())->toBe(0.142857);
    expect(Math::of(1)->scale(8)->divide(6)->toFloat())->toBe(0.16666666);

    // Division by very small numbers
    expect(Math::of(1)->scale(4)->divide(0.0001)->toFloat())->toBe(10000.0000);

    // Division of very small numbers
    expect(Math::of(0.0001)->scale(8)->divide(0.0001)->toFloat())->toBe(1.00000000);

    // Power
    expect(Math::of(2)->pow(3)->toFloat())->toBe(8.00);
    expect(Math::of(3)->pow(2)->toFloat())->toBe(9.00);
    expect(Math::of(10)->pow(0)->toFloat())->toBe(1.00);

    // Combining operations
    $result = Math::of(10)
        ->subtract(2)  // 8
        ->multiply(3)  // 24
        ->divide(4)    // 6
        ->pow(2)       // 36
        ->toFloat();

    // Due to potential floating-point precision issues, let's use a delta
    expect($result)->toBe(36.00, 2);

    // Alternatively, we can use toString() for exact comparison
    expect(Math::of(10)
        ->subtract(2)
        ->multiply(3)
        ->divide(4)
        ->pow(2)
        ->toString()
    )->toBe('36.00');

    // Operations with negative numbers
    expect(Math::of(-5)->subtract(3)->toFloat())->toBe(-8.00);
    expect(Math::of(-5)->multiply(-2)->toFloat())->toBe(10.00);
    expect(Math::of(-10)->divide(2)->toFloat())->toBe(-5.00);
    expect(Math::of(-2)->pow(3)->toFloat())->toBe(-8.00);

    // Operations with very small numbers
    expect(Math::of(0.1)->subtract(0.09)->scale(3)->toFloat())->toBe(0.010);
    expect(Math::of(0.1)->multiply(0.1)->scale(3)->toFloat())->toBe(0.010);
    expect(Math::of(0.01)->scale(4)->divide(10)->toFloat())->toBe(0.0010);

    // Operations with very large numbers
    expect(Math::of(1000000)->multiply(1000000)->toFloat())->toBe(1000000000000.00);
    expect(Math::of(1000000000000)->divide(1000000)->toFloat())->toBe(1000000.00);
});

it('compares numbers correctly', function () {
    // isLessThan
    expect(Math::of(5)->isLessThan(10))->toBeTrue();
    expect(Math::of(10)->isLessThan(5))->toBeFalse();
    expect(Math::of(5)->isLessThan(5))->toBeFalse();

    // isLessThanOrEqual
    expect(Math::of(5)->isLessThanOrEqual(10))->toBeTrue();
    expect(Math::of(5)->isLessThanOrEqual(5))->toBeTrue();
    expect(Math::of(10)->isLessThanOrEqual(5))->toBeFalse();

    // isGreaterThan
    expect(Math::of(10)->isGreaterThan(5))->toBeTrue();
    expect(Math::of(5)->isGreaterThan(10))->toBeFalse();
    expect(Math::of(5)->isGreaterThan(5))->toBeFalse();

    // isGreaterThanOrEqual
    expect(Math::of(10)->isGreaterThanOrEqual(5))->toBeTrue();
    expect(Math::of(5)->isGreaterThanOrEqual(5))->toBeTrue();
    expect(Math::of(5)->isGreaterThanOrEqual(10))->toBeFalse();

    // isEqual
    expect(Math::of(5)->isEqual(5))->toBeTrue();
    expect(Math::of(5)->isEqual(10))->toBeFalse();

    // Comparing with different types
    expect(Math::of(5)->isEqual('5'))->toBeTrue();
    expect(Math::of(5.0)->isEqual(5))->toBeTrue();

    // Comparing with small differences
    expect(Math::of(0.1 + 0.2)->isEqual(0.3))->toBeTrue();

    // Comparing with different scales
    expect(Math::of(1)->scale(2)->isEqual(1.00))->toBeTrue();
    expect(Math::of(1)->scale(2)->isEqual(1.001))->toBeFalse();

    // Comparing negative numbers
    expect(Math::of(-5)->isLessThan(-3))->toBeTrue();
    expect(Math::of(-3)->isGreaterThan(-5))->toBeTrue();

    expect(Math::of(10.0313131)->scale(10)->isLessThan(9.0313131))->toBeFalse();
});

it('performs utility operations correctly', function () {
    expect(Math::of(-5)->absolute()->toFloat())->toBe(5.0);
    expect(Math::of(3)->negative()->toFloat())->toBe(-3.0);
    expect(Math::of(3.7)->ceil()->toFloat())->toBe(4.0);
    expect(Math::of(3.2)->floor()->toFloat())->toBe(3.0);
    expect(Math::of('3.14159')->round(2)->toString())->toBe('3.14');
});

it('handles percentage operations correctly', function () {
    $math = Math::of(100);

    // Testing addition of percentage
    $newMath = $math->addPercentage(50); // Adding 50%
    expect($newMath->toFloat())->toBe(150.0);

    // Testing subtraction of percentage
    $newMath = $math->subtractPercentage(50); // Subtracting 50%
    expect($newMath->toFloat())->toBe(50.0);
});

it('can chain multiple different operations', function () {
    $result = Math::of(100)
        ->addPercentage(10)  // 110
        ->multiply(2)        // 220
        ->subtract(20)       // 200
        ->divide(2)          // 100
        ->sum(50)            // 150
        ->roundUp()          // Should round up here if needed
        ->toFloat();

    expect($result)->toBe(150.0);
});

it('handles errors correctly', function () {
    $this->expectException(DivisionByZeroException::class);

    Math::of(100)->divide(0)->toFloat();

    $this->expectException(TypeError::class);

    Math::of('invalid')->sum('oops');

    $this->expectException(NegativeNumberException::class);

    Math::of(-100)->absolute()->negative()->toInt();
});

it('maintains precision with very large numbers', function () {
    $largeNumber = Math::of('999999999999999999999999999999')->sum('1')->scale(0);
    expect($largeNumber->toString())->toBe('1000000000000000000000000000000');

    $multiplied = $largeNumber->multiply('100000000000000000000')->scale(0);
    expect($multiplied->toString())->toBe('100000000000000000000000000000000000000000000000000');
});

it('converts to different formats correctly', function () {
    $math = Math::of(1234.5678);

    expect($math->toInt())->toBe(1234);
    expect($math->toFloat())->toBe(1234.56);
    expect($math->toString())->toBe('1234.56');
});

it('allows changing scale and rounding mode mid-calculation', function () {
    $math = Math::of(100.123456)
        ->scale(5)  // Now scale is set first
        ->multiply(2)
        ->roundingMode(RoundingMode::UP)  // Rounding after multiplication
        ->sum(0.00002);  // Tiny sum here

    expect($math->toString())->toBe('200.24694');  // Adjusted expected outcome
});

it('handles operations with mixed scales correctly', function () {
    $num1 = Math::of('123.456', 3); // Scale 3
    $num2 = Math::of('0.7891', 4);  // Scale 4

    $result = $num1->sum($num2);
    expect($result->toString())->toBe('124.245'); // Expecting concatenated scale 4
});

it('can convert to storage scale', function () {
    $storage_scale = 10;
    $storage_value = Math::of(100.123456)->storageScale($storage_scale)->toStorageScale();
    $decode_value = Math::of($storage_value)->storageScale($storage_scale)->fromStorage()->toFloat();

    expect($decode_value)->toBe(100.12)
        ->and($storage_value)->toBe(1001200000000);
});

it('give the percentage of the number', function () {
    expect(Math::of(100)->toPercentageOf(50)->toFloat())->toBe(50.0);
    expect(Math::of(100)->toPercentageOf(30)->toFloat())->toBe(30.0);
    expect(Math::of(123.45)->toPercentageOf(50)->toFloat())->toBe(61.72);
    expect(Math::of(99.99)->toPercentageOf(10)->toFloat())->toBe(9.99);
});

it('calculates percentage difference correctly', function () {

    expect(Math::of(100)->differenceInPercentage(50))->toBe(50.0);
    expect(Math::of(50)->differenceInPercentage(100))->toBe(100.0);

    expect(Math::of(100.5)->differenceInPercentage(50.1))->toBe(50.14);
    expect(Math::of(50.1)->differenceInPercentage(100.5))->toBe(100.59);
});

test('average calculation', function () {
    // Integers
    expect(Math::average(2, 3, 4, 5)->toFloat())->toBe(3.5);
    expect(Math::average(0, 100)->toFloat())->toBe(50.0);

    // Floats
    expect(Math::average(2.5, 3.5)->toFloat())->toBe(3.0);
    expect(Math::average(3.33, 3.33)->toFloat())->toBe(3.33);
});

test('percentage of calculation', function () {
    // Integers
    expect(Math::of(50)->percentageOf(100))->toBe(50.00);
    expect(Math::of(25)->percentageOf(100))->toBe(25.00);

    // Floats
    expect(Math::of(1)->percentageOf(3))->toBe(33.33);
    expect(Math::of(2)->percentageOf(3))->toBe(66.66);
});

it('respects config scale and rounding mode', function () {
    // Mock config values
    config(['helpers.math.scale' => 4]);
    config(['helpers.math.rounding_mode' => RoundingMode::UP]);

    $math = Math::of(1.23456789);

    expect($math->toFloat())->toBe(1.2346);
    expect($math->roundingMode)->toBe(RoundingMode::UP);

    // Reset config to default
    config(['helpers.math.scale' => 2]);
    config(['helpers.math.rounding_mode' => RoundingMode::DOWN]);

    $defaultMath = Math::of(1.23456789);

    expect($defaultMath->toFloat())->toBe(1.23);
    expect($defaultMath->roundingMode)->toBe(RoundingMode::DOWN);
});
