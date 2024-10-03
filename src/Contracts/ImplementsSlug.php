<?php

namespace Flavorly\LaravelHelpers\Contracts;

interface ImplementsSlug
{
    /**
     * Get list of attributes that should be used to generate the slug
     *
     * @return string[]
     */
    public function getSlugAttributes(): array;
}
