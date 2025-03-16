<?php

declare(strict_types = 1);

namespace Stackrats\LaravelScaffoldFeature\Src\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait HasPaginationMeta
{
    protected function getPaginationMeta(
        LengthAwarePaginator $paginator
    ): array {
        return [
            'meta' => [
                'currentPage' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'perPage' => $paginator->perPage(),
                'totalItems' => $paginator->total(),
            ],
        ];
    }
}
