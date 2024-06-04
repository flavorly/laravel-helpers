<?php

namespace Flavorly\LaravelHelpers\Contracts;

interface PrunesStaledCache
{
    /**
     * Invalidate the cache
     */
    public function pruneCache(): void;
}
