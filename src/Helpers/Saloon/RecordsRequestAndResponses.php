<?php

namespace Flavorly\LaravelHelpers\Helpers\Saloon;

use GuzzleHttp\TransferStats;
use Saloon\Config;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;

class RecordsRequestAndResponses
{
    /**
     * Little debug help to see the request and response pairs to Ray
     *
     * Usage: call this method in your AppServiceProvider
     */
    public static function saloonDebugging(): void
    {
        if (app()->hasDebugModeEnabled() && config('laravel-helpers.debug-requests', true)) {
            Config::globalMiddleware()->onRequest(function (PendingRequest $pendingRequest): void {
                if (function_exists('ray')) {
                    ray('Dispatching Request', $pendingRequest);
                }

                $pendingRequest->config()->set([
                    'on_stats' => function (TransferStats $stats): void {
                        // @codeCoverageIgnoreStart
                        if (function_exists('ray')) {
                            ray('[Guzzle Response Body]', (string) $stats->getResponse()?->getBody());
                        }
                        // @codeCoverageIgnoreEnd
                    },
                ]);
            });

            Config::globalMiddleware()->onResponse(function (Response $response): void {
                if (function_exists('ray')) {
                    ray('Response Received', $response);
                }
            });
        }
    }
}
