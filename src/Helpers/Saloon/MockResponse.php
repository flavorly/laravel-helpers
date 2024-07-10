<?php

namespace Flavorly\LaravelHelpers\Helpers\Saloon;

use Closure;
use Saloon\Exceptions\FixtureMissingException;
use Saloon\Http\Faking\MockResponse as BaseMockResponse;
use Saloon\Repositories\Body\StringBodyRepository;

class MockResponse extends BaseMockResponse
{
    /**
     * Returns a MockResponse with the given fixture name and placeholders.
     *
     * @param  array<string, mixed>  $placeholders
     * @param  Closure(array<string, mixed>|mixed):array<string, mixed>|null  $transform
     *
     * @throws FixtureMissingException
     */
    public static function fixturesWithPlaceholders(
        string $fixtureName,
        array $placeholders = [],
        ?Closure $transform = null,
    ): ?BaseMockResponse {

        if (empty($placeholders)) {
            return BaseMockResponse::fixture($fixtureName)->getMockResponse();
        }
        // Replace the data
        $response = BaseMockResponse::fixture($fixtureName)->getMockResponse();
        $body = $response?->body() ?? '';
        foreach ($placeholders as $key => $value) {
            // @phpstan-ignore-next-line
            $body = str_replace($key, $value, $body);
        }

        // Transform the body
        if ($transform) {
            $body = json_encode($transform(json_decode($body, true)));
        }

        // @phpstan-ignore-next-line
        invade($response)->body = new StringBodyRepository($body);

        return $response;
    }
}
