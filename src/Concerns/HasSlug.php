<?php

namespace Flavorly\LaravelHelpers\Concerns;

use Flavorly\LaravelHelpers\Contracts\ImplementsSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Usage on Model:
 *
 * use Flavorly\LaravelHelpers\Concerns\HasSlug;
 *
 * public function getSlugAttributes(): array
 * {
 *     return [
 *         'name', 'relation.slug', // relation property
 *         // or 'name', 'id', etc
 *     ];
 * }
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function (Model&ImplementsSlug $model) {
            $model->slug = Str::uuid()->toString();
        });

        static::created(function (Model&ImplementsSlug $model) {
            $model->update([
                'slug' => Str::slug(collect($model->getSlugAttributes())
                    ->map(fn (string $attribute) => $model->$attribute)
                    ->implode(' ')),
            ]);
        });

        static::updating(function (Model&ImplementsSlug $model) {
            $attributes = $model->getSlugAttributes();
            if ($model->isDirty($attributes)) {
                $model->slug = Str::slug(collect($model->getSlugAttributes())
                    ->map(fn (string $attribute) => $model->$attribute)
                    ->implode(' '));
            }
        });
    }
}
