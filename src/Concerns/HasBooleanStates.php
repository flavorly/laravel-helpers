<?php

namespace Flavorly\LaravelHelpers\Concerns;

trait HasBooleanStates
{
    /**
     * If the process was success or failed
     * DEFAULT: Failed!
     */
    protected bool $success = false;

    /**
     * Mark the process as success
     */
    public function setAsSuccess(): static
    {
        $this->success = true;

        return $this;
    }

    /**
     * Mark the process as failed
     */
    public function setAsFailed(): static
    {
        $this->success = false;

        return $this;
    }

    /**
     * Check if it was successful
     */
    public function ok(): bool
    {
        return $this->success;
    }

    /**
     * Check if it was failed
     */
    public function failed(): bool
    {
        return ! $this->success;
    }
}
