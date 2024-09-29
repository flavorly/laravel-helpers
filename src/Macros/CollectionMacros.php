<?php

namespace Flavorly\LaravelHelpers\Macros;

use Flavorly\LaravelHelpers\Contracts\RegistersMacros;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class CollectionMacros implements RegistersMacros
{
    public static function register(): void
    {
        self::paginate();
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
}
