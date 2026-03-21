<?php

use Flavorly\LaravelHelpers\Casts\TrimString;
use Illuminate\Database\Eloquent\Model;

beforeEach(function (): void {
    $this->cast = new TrimString;
    $this->model = new class extends Model {};
});

it('trims all whitespace on get', function (): void {
    $result = $this->cast->get($this->model, 'name', '  hello   world  ', []);

    expect($result)->toBe('helloworld');
});

it('trims all whitespace on set', function (): void {
    $result = $this->cast->set($this->model, 'name', '  hello   world  ', []);

    expect($result)->toBe('helloworld');
});

it('handles null values on get', function (): void {
    $result = $this->cast->get($this->model, 'name', null, []);

    expect($result)->toBe('');
});

it('handles null values on set', function (): void {
    $result = $this->cast->set($this->model, 'name', null, []);

    expect($result)->toBe('');
});

it('handles strings without whitespace', function (): void {
    $result = $this->cast->get($this->model, 'name', 'hello', []);

    expect($result)->toBe('hello');
});

it('handles empty string', function (): void {
    $result = $this->cast->get($this->model, 'name', '', []);

    expect($result)->toBe('');
});

it('removes tabs and newlines', function (): void {
    $result = $this->cast->get($this->model, 'name', "hello\tworld\n", []);

    expect($result)->toBe('helloworld');
});
