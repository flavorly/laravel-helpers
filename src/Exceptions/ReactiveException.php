<?php

namespace Flavorly\LaravelHelpers\Exceptions;

use Closure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class ReactiveException extends Exception
{
    /**
     * Defines the type of channel.
     */
    protected string $type = 'exception';

    /**
     * Defines the title of exception.
     */
    protected string $title;

    /**
     * Defines the subject of exception.
     */
    protected string $subject;

    /**
     * Link to read more about this exception.
     */
    protected string $link;

    /**
     * Any additional context to this exception.
     *
     * @var array<string, mixed>
     */
    protected array $context = [];

    /**
     * Adds additional data to default api response.
     */
    protected array $appends = [];

    /**
     * Adds additional data to default api response.
     *
     * @var array<string, mixed>
     */
    protected array $headers = [];

    /**
     * To Execute after the exception was thrown.
     */
    protected Closure $whatsNext;

    /**
     * If the report should be sent to the exception handler.
     */
    protected bool $shouldReport = false;

    /**
     * GenericException constructor.
     */
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Append additional title
     *
     * @return $this
     */
    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Append additional messages
     *
     * @return $this
     */
    public function message(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Appends additional data to api response.
     *
     * @return $this
     */
    public function appends(array $data): static
    {
        $this->appends = $data;

        return $this;
    }

    /**
     * Add a subject.
     *
     * @return $this
     */
    public function subject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Add a Link to the exception.
     *
     * @return $this
     */
    public function link(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Add a type to the exception.
     *
     * @return $this
     */
    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Add a context to the exception.
     *
     * @return $this
     */
    public function then(Closure $closure): static
    {
        $this->whatsNext = $closure;

        return $this;
    }

    /**
     * Report the exception to handler
     *
     * @return $this
     */
    public function shouldReport(): static
    {
        $this->shouldReport = true;

        return $this;
    }

    /**
     * Dont report exception to handler
     *
     * @return $this
     */
    public function dontReport(): static
    {
        $this->shouldReport = false;

        return $this;
    }

    /**
     * Converts the exception to an array.
     * return array<string, mixed>
     */
    public function toArray(): array
    {
        if (app()->environment('live')) {
            return [
                'message' => $this->getMessage(),
                'kind' => $this->subject ?? '-',
                'help' => $this->link ?? config('app.url'),
            ];
        }

        return [
            'message' => $this->getMessage(),
            'kind' => $this->subject ?? '-',
            'help' => $this->link ?? config('app.url'),
            'meta' => [
                'file' => $this->getFile(),
                'line' => $this->getLine(),
                'type' => $this->getNameFormatted(),
            ],
        ];
    }

    /**
     * Converts the exception to json.
     */
    public function toJson(): JsonResponse
    {
        return response()->json(
            array_merge($this->toArray(), $this->appends),
            $this->getCode(),
            $this->headers,
        );
    }

    /**
     * Get the class name formatted.
     */
    protected function getNameFormatted(): mixed
    {
        $explode = explode('\\', get_class($this));
        $parts = array_values(array_slice($explode, -1));

        return data_get($parts, 0, get_class($this));
    }

    /**
     * Returns the response to Hybridly/Inertia
     */
    protected function toInertia(): void
    {
        // Implement this method with notifications, dialog, etc.
    }

    /**
     * Render the exception into an HTTP response or Hybridly Response.
     */
    public function render(Request $request): null|string|RedirectResponse|Response|bool
    {
        return $this->response($request);
    }

    /**
     * Report the exception.
     */
    public function report(): bool
    {
        return ! $this->shouldReport;
    }

    /**
     * Get the response based on the type
     */
    public function response(?Request $request = null): null|bool|string|RedirectResponse|Response
    {
        $request = $request ?? request();
        if ($request->isInertia()) {
            $this->toInertia();
            throw ValidationException::withMessages([$this->type => $this->getMessage()]);
        }

        if ($request->wantsJson()) {
            return $this->toJson();
        }

        return false;
    }
}
