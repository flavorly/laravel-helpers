<?php

namespace Flavorly\LaravelHelpers\Helpers\Saloon;

use GuzzleHttp\TransferStats;
use Saloon\Http\PendingRequest;

trait RecordsRequestAndResponses
{
    /**
     * Little debug help to see the request and response pairs to Ray
     */
    public function bootRecordsRequestAndResponses(PendingRequest $pendingRequest): void
    {
        if (app()->hasDebugModeEnabled() && config('laravel-helpers.debug-requests', true)) {
            $this->middleware()->onRequest(new RequestRecorder);
            $this->middleware()->onResponse(new ResponseRecorder);
            $this->config()->set([
                'on_stats' => function (TransferStats $stats) {
                    // @codeCoverageIgnoreStart
                    ray('[Guzzle Request ]', $stats->getRequest());
                    ray('[Guzzle Response ]', $stats->getResponse());
                    ray('[Guzzle Response Body ]', (string) $stats->getResponse()?->getBody());
                    // @codeCoverageIgnoreEnd
                },
            ]);
        }
    }
}
