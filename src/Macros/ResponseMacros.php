<?php

namespace Flavorly\LaravelHelpers\Macros;

use Flavorly\LaravelHelpers\Contracts\RegistersMacros;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\ResponseFactory;

class ResponseMacros implements RegistersMacros
{
    public static function register(): void
    {
        self::json();
    }

    public static function json(): void
    {
        // Response Macros
        if (! ResponseFactory::hasMacro('failed')) {
            ResponseFactory::macro('failed', function (
                array $data = [],
                $status = 400,
                array $headers = [],
                $options = 0
            ): JsonResponse {
                return response()->json(array_merge([
                    'status' => 'fail',
                    'success' => false,
                    'code' => $status,
                ], $data), $status, $headers, $options);
            });
        }

        if (! ResponseFactory::hasMacro('success')) {
            ResponseFactory::macro('success', function (
                array $data = [],
                $status = 200,
                array $headers = [],
                $options = 0
            ): JsonResponse {
                return response()->json(array_merge([
                    'status' => 'ok',
                    'success' => true,
                    'code' => $status,
                ], $data), $status, $headers, $options);
            });
        }
    }
}
