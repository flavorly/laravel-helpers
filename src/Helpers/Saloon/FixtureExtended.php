<?php

namespace Flavorly\LaravelHelpers\Helpers\Saloon;

use Closure;
use Saloon\Data\RecordedResponse;
use Saloon\Exceptions\FixtureMissingException;
use Saloon\Http\Faking\Fixture;
use Saloon\Http\Faking\MockResponse;
use Saloon\MockConfig;
use Saloon\Repositories\Body\JsonBodyRepository;

class FixtureExtended extends Fixture
{
    /**
     * @var array <string, mixed>
     */
    protected array $pathPlaceholders = [];

    /**
     * @var array <string, mixed>
     */
    protected array $textPlaceholders = [];

    /**
     * @var array <int, Closure>
     */
    protected array $transformers = [];

    /**
     * Replace a path in a json response
     */
    public function replacePath(string $path, mixed $value): static
    {
        $this->pathPlaceholders[$path] = $value;

        return $this;
    }

    /**
     * Replace multiple paths in the body response
     *
     * @param  array<string,mixed>  $paths
     */
    public function replacePaths(array $paths): static
    {
        $this->pathPlaceholders = array_merge($this->pathPlaceholders, $paths);

        return $this;
    }

    /**
     * Replace a string in the body response
     */
    public function replaceString(string $key, mixed $value): static
    {
        $this->textPlaceholders[$key] = $value;

        return $this;
    }

    /**
     * Replace multiple strings in the body response
     *
     * @param  array<string,mixed>  $strings
     */
    public function replaceStrings(array $strings = []): static
    {
        $this->textPlaceholders = array_merge($this->textPlaceholders, $strings);

        return $this;
    }

    /**
     * Transform the resulting text body
     *
     * @param  Closure(string):string  $transform
     */
    public function transform(Closure $transform): static
    {
        $this->transformers[] = $transform;

        return $this;
    }

    /**
     * Get the resulting MockResponse
     */
    public function getMockResponse(): ?MockResponse
    {
        $storage = $this->storage;
        $fixturePath = $this->getFixturePath();

        if ($storage->exists($fixturePath)) {
            $recordedResponse = RecordedResponse::fromFile((string) $storage->get($fixturePath));

            // Replace the data
            $mockResponse = $recordedResponse->toMockResponse();

            $body = $mockResponse->body();
            $data = $body->all();

            // If we have a JSON response, we can replace the data's paths
            $jsonBody = is_a($body, JsonBodyRepository::class);
            if ($jsonBody) {
                // We have a json response
                foreach ($this->pathPlaceholders as $key => $value) {
                    data_set($data, $key, $value);
                }

                $data = json_encode($data);
            }

            // We have a string response
            foreach ($this->textPlaceholders as $key => $value) {
                // @phpstan-ignore-next-line
                $data = str_replace($key, $value, $data);
            }

            // Transform the body if we have transformers
            foreach ($this->transformers as $transform) {
                $data = $transform($data);
            }

            return new MockResponse(
                $jsonBody ? json_decode($data, true) : $data,
                $recordedResponse->statusCode,
                $recordedResponse->headers
            );
        }

        if (MockConfig::isThrowingOnMissingFixtures() === true) {
            throw new FixtureMissingException($fixturePath);
        }

        return null;
    }
}
