<?php

namespace Flavorly\LaravelHelpers\Helpers\Locker;

trait HasLocks
{
    /**
     * Saves the actual instance for Locker
     */
    protected ?Locker $locker = null;

    /**
     * Returns the Locker Instance
     */
    public function locker(): Locker
    {
        if ($this->locker) {
            return $this->locker;
        }

        // @phpstan-ignore-next-line
        $this->locker = new Locker($this);

        return $this->locker;
    }
}
