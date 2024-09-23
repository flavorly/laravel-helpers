<?php

namespace Flavorly\LaravelHelpers\Concerns;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTime;
use DateTimeInterface;

trait HasQueryTimeScopes
{
    /**
     * @param  DateTimeInterface|Carbon|CarbonImmutable|DateTime|null  $start
     * @param  DateTimeInterface|Carbon|CarbonImmutable|DateTime|null  $end
     */
    public function whereInTimeFrame(mixed $start = null, mixed $end = null, string $column = 'created_at'): static
    {
        if ($start !== null) {
            $this->where($column, '>=', $start);
        }

        if ($end !== null) {
            $this->where($column, '<=', $end);
        }

        return $this;
    }

    public function whereIsNewerThanDays(float|int $days, string $column = 'created_at'): static
    {
        if ($days > 0) {
            return $this->where($column, '>=', now()->subDays($days));
        }

        return $this;
    }

    public function whereIsOlderThanDays(float|int $days, string $column = 'created_at'): static
    {
        if ($days > 0) {
            return $this->where($column, '<=', now()->subDays($days));
        }

        return $this;
    }

    public function whereInLastMonths(int $months = 1, string $column = 'created_at'): static
    {
        return $this->where($column, '>=', now()->subMonths($months));
    }

    public function whereInLastDays(int $days = 1, string $column = 'created_at'): static
    {
        return $this->where($column, '>=', now()->subDays($days));
    }

    public function whereInLastWeek(string $column = 'created_at'): static
    {
        return $this->where($column, '>=', now()->subWeek());
    }

    public function whereLastHours(float|int $hours = 1, string $column = 'created_at'): static
    {
        return $this->where($column, '>=', now()->subHours($hours));
    }

    public function whereCreatedInLast24Hours(): static
    {
        return $this->whereLastHours(24);
    }

    public function whereCreatedInLast48Hours(): static
    {
        return $this->whereLastHours(48);
    }
}
