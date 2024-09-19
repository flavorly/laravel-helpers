<?php

namespace Flavorly\LaravelHelpers\Helpers\Saloon;

use Saloon\Contracts\RequestMiddleware;
use Saloon\Http\PendingRequest;

class RequestRecorder implements RequestMiddleware
{
    public function __invoke(PendingRequest $pendingRequest): void
    {
        ray('Dispatching Request', $pendingRequest);
    }
}
