<?php

use Brick\Math\RoundingMode;
use Flavorly\LaravelHelpers\Helpers\Math\Math;

beforeEach(function (): void {
    config()->set('laravel-helpers.math.scale', 2);
    config()->set('laravel-helpers.math.storage_scale', 10);
    config()->set('laravel-helpers.math.rounding_mode', RoundingMode::DOWN);
});

it('tokenizes arguments into colon-separated string', function (): void {
    expect(tokenize('foo', 'bar', 'baz'))->toBe('foo:bar:baz');
    expect(tokenize('single'))->toBe('single');
    expect(tokenize(1, 2, 3))->toBe('1:2:3');
});

it('creates math instance with math() helper', function (): void {
    $result = math(100);

    expect($result)->toBeInstanceOf(Math::class);
    expect($result->toFloat())->toBe(100.00);
});

it('performs operations with math() helper', function (): void {
    expect(math(100)->sum(50)->toFloat())->toBe(150.00);
    expect(math(200)->subtract(50)->toFloat())->toBe(150.00);
});

it('gets fallback value from nested data', function (): void {
    $data = ['user' => ['name' => 'John', 'email' => 'john@example.com']];

    expect(data_get_fallback($data, ['user.name']))->toBe('John');
    expect(data_get_fallback($data, ['user.phone', 'user.email']))->toBe('john@example.com');
    expect(data_get_fallback($data, ['user.phone', 'user.address'], 'default'))->toBe('default');
});

it('returns null when no fallback keys match', function (): void {
    $data = ['foo' => 'bar'];

    expect(data_get_fallback($data, ['baz', 'qux']))->toBeNull();
});

it('returns default when no fallback keys match', function (): void {
    $data = ['foo' => 'bar'];

    expect(data_get_fallback($data, ['baz'], 'fallback'))->toBe('fallback');
});
