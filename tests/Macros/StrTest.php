<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

it('converts lines to collection with basic string', function (): void {
    $result = Str::linesToCollection('apple,banana,cherry', ',');
    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->all()->toBe(['apple', 'banana', 'cherry']);
});

it('converts lines to collection with custom delimiter', function (): void {
    $result = Str::linesToCollection('apple|banana|cherry', '|');
    expect($result->all())->toBe(['apple', 'banana', 'cherry']);
});

it('removes duplicates by default', function (): void {
    $result = Str::linesToCollection('apple,banana,apple,cherry', ',');
    expect($result->all())->toBe(['apple', 'banana', 'cherry']);
});

it('keeps duplicates when unique is false', function (): void {
    $result = Str::linesToCollection('apple,banana,apple,cherry', ',', false);
    expect($result->all())->toBe(['apple', 'banana', 'apple', 'cherry']);
});

it('trims whitespace from items', function (): void {
    $result = Str::linesToCollection('  apple  ,  banana  , cherry  ', ',');
    expect($result->all())->toBe(['apple', 'banana', 'cherry']);
});

it('filters out empty values', function (): void {
    $result = Str::linesToCollection('apple,,banana,,cherry', ',');
    expect($result->all())->toBe(['apple', 'banana', 'cherry']);
});

it('accepts array input', function (): void {
    $result = Str::linesToCollection(['apple', 'banana', 'apple', 'cherry']);
    expect($result->all())->toBe(['apple', 'banana', 'cherry']);
});

it('preserves case-sensitive duplicates', function (): void {
    $result = Str::linesToCollection('Apple,apple,APPLE,banana', ',');
    expect($result->all())->toBe(['Apple', 'apple', 'APPLE', 'banana']);
});

it('handles empty string', function (): void {
    $result = Str::linesToCollection('');
    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->all()->toBe([]);
});

it('handles single value', function (): void {
    $result = Str::linesToCollection('apple');
    expect($result->all())->toBe(['apple']);
});

it('handles only delimiters', function (): void {
    $result = Str::linesToCollection(',,,', ',');
    expect($result->all())->toBe([]);
});

it('handles only spaces', function (): void {
    $result = Str::linesToCollection('   ');
    expect($result->all())->toBe([]);
});

it('handles unicode characters', function (): void {
    $result = Str::linesToCollection('café,résumé,naïve', ',');
    expect($result->all())->toBe(['café', 'résumé', 'naïve']);
});
