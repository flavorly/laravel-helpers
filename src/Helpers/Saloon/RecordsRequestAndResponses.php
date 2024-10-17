<?php

namespace Flavorly\LaravelHelpers\Helpers\Saloon;

use GuzzleHttp\TransferStats;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;

trait RecordsRequestAndResponses
{
    /**
     * Little debug help to see the request and response pairs to Ray
     *
     * Usage: call this method in your Connector __construct, so it's only registered once
     */
    public function debugRequestsAndResponses(): void
    {
        if (app()->hasDebugModeEnabled() && config('laravel-helpers.debug-requests', true)) {
            $this->debugRequest(function (PendingRequest $pendingRequest, RequestInterface $psrRequest) {
                ray('Dispatching Request', $pendingRequest);
            });

            $this->debugResponse(function (Response $response, ResponseInterface $psrResponse) {
                ray('Response Received', $response);
            });

            $this->config()->set([
                'on_stats' => function (TransferStats $stats) {
                    // @codeCoverageIgnoreStart
                    ray('[Guzzle Response Body]', (string) $stats->getResponse()?->getBody());
                    // @codeCoverageIgnoreEnd
                },
            ]);
        }
    }
}
