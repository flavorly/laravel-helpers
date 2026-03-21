<?php

use Flavorly\LaravelHelpers\Concerns\HasBooleanStates;

beforeEach(function (): void {
    $this->subject = new class
    {
        use HasBooleanStates;
    };
});

it('defaults to failed state', function (): void {
    expect($this->subject->ok())->toBeFalse();
    expect($this->subject->failed())->toBeTrue();
});

it('can be set to success', function (): void {
    $this->subject->setAsSuccess();

    expect($this->subject->ok())->toBeTrue();
    expect($this->subject->failed())->toBeFalse();
});

it('can be set back to failed', function (): void {
    $this->subject->setAsSuccess();
    $this->subject->setAsFailed();

    expect($this->subject->ok())->toBeFalse();
    expect($this->subject->failed())->toBeTrue();
});

it('returns static for fluent chaining', function (): void {
    $result = $this->subject->setAsSuccess();

    expect($result)->toBe($this->subject);
});
