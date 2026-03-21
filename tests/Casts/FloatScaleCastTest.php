<?php

use Brick\Math\RoundingMode;
use Flavorly\LaravelHelpers\Casts\FloatScaleCast;
use Illuminate\Database\Eloquent\Model;

beforeEach(function (): void {
    config()->set('app.decimal_scale', 2);
    config()->set('app.decimal_integer', 10);
    config()->set('laravel-helpers.math.rounding_mode', RoundingMode::DOWN);

    $this->cast = new FloatScaleCast;
    $this->model = new class extends Model {};
});

it('casts value to float on get', function (): void {
    $result = $this->cast->get($this->model, 'price', '123.456', []);

    expect($result)->toBe(123.45);
});

it('returns null for null value on get', function (): void {
    $result = $this->cast->get($this->model, 'price', null, []);

    expect($result)->toBeNull();
});

it('casts value to string on set', function (): void {
    $result = $this->cast->set($this->model, 'price', 123.456, []);

    expect($result)->toBe('123.45');
});

it('returns null for null value on set', function (): void {
    $result = $this->cast->set($this->model, 'price', null, []);

    expect($result)->toBeNull();
});

it('handles integer values', function (): void {
    $result = $this->cast->get($this->model, 'price', '100', []);

    expect($result)->toBe(100.00);
});

it('handles zero', function (): void {
    $result = $this->cast->get($this->model, 'price', '0', []);

    expect($result)->toBe(0.00);
});
