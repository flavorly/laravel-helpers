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
            array_map(fn ($resolver) => $resolver(), $this->parentResolvers()),
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
            ->when(empty($attributesToSearch), fn ($query) => $query->inRandomOrder())
            ->firstOr(fn () => $this->createOne($rawAttributes));
    }
}
