<?php

use Flavorly\LaravelHelpers\Macros\CollectionMacros;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

beforeEach(function (): void {
    CollectionMacros::register();
});

it('paginates a collection', function (): void {
    $collection = collect(range(1, 50));
    $paginated = $collection->paginate(10, 1);

    expect($paginated)
        ->toBeInstanceOf(LengthAwarePaginator::class)
        ->total()->toBe(50)
        ->perPage()->toBe(10)
        ->currentPage()->toBe(1);

    expect($paginated->items())->toHaveCount(10);
});

it('paginates with custom page', function (): void {
    $collection = collect(range(1, 50));
    $paginated = $collection->paginate(10, 2);

    expect($paginated)
        ->currentPage()->toBe(2)
        ->total()->toBe(50);

    expect($paginated->items())->toHaveCount(10);
});

it('handles pagination beyond last page', function (): void {
    $collection = collect(range(1, 5));
    $paginated = $collection->paginate(10, 2);

    expect($paginated->items())->toHaveCount(0);
    expect($paginated->total())->toBe(5);
});

it('orders collection by ids', function (): void {
    $items = collect([
        (object) ['id' => 1, 'name' => 'Alice'],
        (object) ['id' => 2, 'name' => 'Bob'],
        (object) ['id' => 3, 'name' => 'Charlie'],
    ]);

    $ordered = $items->orderByIds([3, 1, 2]);

    expect($ordered->pluck('name')->all())->toBe(['Charlie', 'Alice', 'Bob']);
});

it('orders collection by ids with custom field', function (): void {
    $items = collect([
        (object) ['code' => 'a', 'name' => 'Alice'],
        (object) ['code' => 'b', 'name' => 'Bob'],
        (object) ['code' => 'c', 'name' => 'Charlie'],
    ]);

    $ordered = $items->orderByIds(['c', 'a', 'b'], 'code');

    expect($ordered->pluck('name')->all())->toBe(['Charlie', 'Alice', 'Bob']);
});

it('converts collection to json response', function (): void {
    $collection = collect(['foo' => 'bar', 'baz' => 'qux']);
    $response = $collection->toJsonResponse();

    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getData(true))->toBe(['foo' => 'bar', 'baz' => 'qux']);
});
