<?php

namespace Flavorly\LaravelHelpers\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait RecyclesFactories
{
    /**
     * Find an existing model or create a new one.
     *
     * @return Model|Builder<Model>
     */
    public function recycleOrCreate(array $attributes = [], array $uniqueVia = []): Model|Builder
    {
        $query = $this->newModel()->newQuery();

        $attributes = array_merge(
            array_map(function ($resolver) {
                return $resolver();
            }, $this->parentResolvers()),
            $attributes
        );

        if (! empty($attributes)) {
            $query = $query->where($attributes);
        }

        $rawAttributes = $this->getRawAttributes(null);

        $attributesToSearch = collect($rawAttributes)
            ->only($uniqueVia)
            ->toArray();

        if (! empty($attributesToSearch)) {
            $query->orWhere($attributesToSearch);
        }

        return $query
            ->when(empty($attributesToSearch), function ($query) {
                return $query->inRandomOrder();
            })
            ->firstOr(function () use ($rawAttributes) {
                return $this->createOne($rawAttributes);
            });
    }
}
