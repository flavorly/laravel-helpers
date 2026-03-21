<?php

use Flavorly\LaravelHelpers\Data\OptionData;
use Flavorly\LaravelHelpers\Tests\Fixtures\TestStatus;
use Illuminate\Support\Collection;

it('checks equality with equals()', function (): void {
    expect(TestStatus::Active->equals(TestStatus::Active))->toBeTrue();
    expect(TestStatus::Active->equals(TestStatus::Inactive))->toBeFalse();
    expect(TestStatus::Active->equals(TestStatus::Inactive, TestStatus::Active))->toBeTrue();
});

it('checks inequality with notEquals()', function (): void {
    expect(TestStatus::Active->notEquals(TestStatus::Inactive))->toBeTrue();
    expect(TestStatus::Active->notEquals(TestStatus::Active))->toBeFalse();
});

it('checks with is() and isNot() aliases', function (): void {
    expect(TestStatus::Active->is(TestStatus::Active))->toBeTrue();
    expect(TestStatus::Active->isNot(TestStatus::Inactive))->toBeTrue();
    expect(TestStatus::Active->is(TestStatus::Inactive))->toBeFalse();
    expect(TestStatus::Active->isNot(TestStatus::Active))->toBeFalse();
});

it('gets label from getLabels()', function (): void {
    expect(TestStatus::Active->getLabel())->toBe('Currently Active');
    expect(TestStatus::Inactive->getLabel())->toBe('Not Active');
    expect(TestStatus::Pending->getLabel())->toBe('Awaiting Review');
});

it('converts to options collection', function (): void {
    $options = TestStatus::toOptions();

    expect($options)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(OptionData::class);

    expect($options->first()->label)->toBe('Currently Active');
    expect($options->first()->value)->toBe('active');
});

it('filters options with callback', function (): void {
    $options = TestStatus::toOptions(
        filter: fn (TestStatus $status): bool => $status !== TestStatus::Pending,
    );

    expect($options)->toHaveCount(2);
});

it('converts to array of labels', function (): void {
    $array = TestStatus::toArray();

    expect($array)->toBeArray()->toHaveCount(3);
});

it('converts to values array', function (): void {
    $values = TestStatus::toValues();

    expect($values)->toBe(['active', 'inactive', 'pending']);
});

it('converts to collection', function (): void {
    $collection = TestStatus::toCollection();

    expect($collection)->toBeInstanceOf(Collection::class)
        ->toHaveCount(3);
});

it('finds enum by label with tryFromLabel()', function (): void {
    expect(TestStatus::tryFromLabel('Currently Active'))->toBe(TestStatus::Active);
    expect(TestStatus::tryFromLabel('currently active'))->toBe(TestStatus::Active);
    expect(TestStatus::tryFromLabel('nonexistent'))->toBeNull();
});

it('checks if enum contains a case', function (): void {
    expect(TestStatus::contains(TestStatus::Active))->toBeTrue();
});

it('returns a random enum case', function (): void {
    $random = TestStatus::random();

    expect($random)->toBeInstanceOf(TestStatus::class);
});

it('finds first matching value with firstWhere()', function (): void {
    expect(TestStatus::firstWhere('active'))->toBe(TestStatus::Active);
    expect(TestStatus::firstWhere('nonexistent'))->toBeNull();
    expect(TestStatus::firstWhere('nonexistent', TestStatus::Pending))->toBe(TestStatus::Pending);
});
