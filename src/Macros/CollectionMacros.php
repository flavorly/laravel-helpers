<?php

namespace Flavorly\LaravelHelpers\Macros;

use Flavorly\LaravelHelpers\Contracts\RegistersMacros;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class CollectionMacros implements RegistersMacros
{
    public static function register(): void
    {
        self::paginate();
        self::orderByIds();
        self::toJsonResponse();
    }

    public static function paginate(): void
    {
        // Response Macros
        if (! Collection::hasMacro('paginate')) {
            /**
             * Paginate the collection.
             *
             * @param  int  $perPage
             * @param  int  $page
             * @param  array  $options
             * @return LengthAwarePaginator
             */
            Collection::macro('paginate', function (
                int $perPage = 15,
                ?int $page = null,
                string $pageName = 'page',
                array $options = []
            ): LengthAwarePaginator {
                $page = $page ?: (Paginator::resolveCurrentPage($pageName) ?: 1);

                /**
                 * @var Collection<int, mixed> $this
                 */
                return new LengthAwarePaginator(
                    $this->forPage($page, $perPage)->toArray(),
                    $this->count(),
                    $perPage,
                    $page,
                    [
                        ...$options,
                        'path' => LengthAwarePaginator::resolveCurrentPath(),
                        'query' => request()->query(),
                    ]
                );
            });
        }
    }

    public static function orderByIds(): void
    {
        if (! Collection::hasMacro('orderByIds')) {
            /**
             * Order the collection by the given ids.
             *
             * @param  array|Collection  $ids
             * @param  string  $idField
             * @return \Illuminate\Support\Collection
             */
            Collection::macro('orderByIds', function ($ids, string $idField = 'id'): Collection {
                $flippedIds = array_flip($ids instanceof Collection ? $ids->all() : $ids);

                /**
                 * @var Collection<int, mixed> $this
                 */
                return $this->sortBy(fn ($item) => $flippedIds[$item->{$idField}] ?? PHP_INT_MAX)->values();
            });
        }
    }

    public static function toJsonResponse(): void
    {
        Collection::macro('toJsonResponse', function (): JsonResponse {
            return response()->json($this);
        });
    }
}
