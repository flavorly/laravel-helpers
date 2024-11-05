<?php

namespace Flavorly\LaravelHelpers\Concerns;

use Illuminate\Support\Str;

trait HasUUID
{
    /**
     * Boot the trait
     */
    public static function bootHasUUID(): void
    {
        static::creating(function ($model) {
            $model->uuid = $model->uuid ?? Str::uuid()->toString();
        });
    }
}
