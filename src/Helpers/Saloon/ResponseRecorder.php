<?php

namespace Flavorly\LaravelHelpers\Helpers\Saloon;

use Saloon\Contracts\ResponseMiddleware;
use Saloon\Http\Response;

class ResponseRecorder implements ResponseMiddleware
{
    public function __invoke(Response $response): void
    {
        ray('Response Received', $response);
    }
}
