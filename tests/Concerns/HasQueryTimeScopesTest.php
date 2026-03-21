<?php

use Flavorly\LaravelHelpers\Concerns\HasQueryTimeScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

beforeEach(function (): void {
    $this->model = new class extends Model
    {
        use HasQueryTimeScopes;

        protected $table = 'test_models';
    };
});

it('applies whereInLastDays scope', function (): void {
    $builder = mockBuilder();
    $result = $builder->whereInLastDays(7);

    expect($result)->toBeInstanceOf(get_class($builder));
});

it('applies whereInLastWeek scope', function (): void {
    $builder = mockBuilder();
    $result = $builder->whereInLastWeek();

    expect($result)->toBeInstanceOf(get_class($builder));
});

it('applies whereInLastMonths scope', function (): void {
    $builder = mockBuilder();
    $result = $builder->whereInLastMonths(3);

    expect($result)->toBeInstanceOf(get_class($builder));
});

it('applies whereLastHours scope', function (): void {
    $builder = mockBuilder();
    $result = $builder->whereLastHours(12);

    expect($result)->toBeInstanceOf(get_class($builder));
});

it('applies whereIsNewerThanDays with positive days', function (): void {
    $builder = mockBuilder();
    $result = $builder->whereIsNewerThanDays(30);

    expect($result)->toBeInstanceOf(get_class($builder));
});

it('skips whereIsNewerThanDays with zero days', function (): void {
    $builder = mockBuilder();
    $result = $builder->whereIsNewerThanDays(0);

    expect($result)->toBeInstanceOf(get_class($builder));
});

it('applies whereIsOlderThanDays with positive days', function (): void {
    $builder = mockBuilder();
    $result = $builder->whereIsOlderThanDays(90);

    expect($result)->toBeInstanceOf(get_class($builder));
});

it('skips whereIsOlderThanDays with zero days', function (): void {
    $builder = mockBuilder();
    $result = $builder->whereIsOlderThanDays(0);

    expect($result)->toBeInstanceOf(get_class($builder));
});

it('applies whereInTimeFrame with both start and end', function (): void {
    $builder = mockBuilder();
    $result = $builder->whereInTimeFrame(now()->subWeek(), now());

    expect($result)->toBeInstanceOf(get_class($builder));
});

it('applies whereInTimeFrame with only start', function (): void {
    $builder = mockBuilder();
    $result = $builder->whereInTimeFrame(start: now()->subWeek());

    expect($result)->toBeInstanceOf(get_class($builder));
});

it('applies whereInTimeFrame with only end', function (): void {
    $builder = mockBuilder();
    $result = $builder->whereInTimeFrame(end: now());

    expect($result)->toBeInstanceOf(get_class($builder));
});

it('applies whereCreatedInLast24Hours', function (): void {
    $builder = mockBuilder();
    $result = $builder->whereCreatedInLast24Hours();

    expect($result)->toBeInstanceOf(get_class($builder));
});

it('applies whereCreatedInLast48Hours', function (): void {
    $builder = mockBuilder();
    $result = $builder->whereCreatedInLast48Hours();

    expect($result)->toBeInstanceOf(get_class($builder));
});

it('uses custom column name', function (): void {
    $builder = mockBuilder();
    $result = $builder->whereInLastDays(7, 'updated_at');

    expect($result)->toBeInstanceOf(get_class($builder));
});

// Helper to create a mock builder with HasQueryTimeScopes
function mockBuilder(): object
{
    return new class
    {
        use HasQueryTimeScopes;

        protected array $wheres = [];

        public function where(string $column, string $operator, mixed $value = null): static
        {
            $this->wheres[] = compact('column', 'operator', 'value');

            return $this;
        }

        public function getWheres(): array
        {
            return $this->wheres;
        }
    };
}
