<?php

declare(strict_types=1);

namespace Stackrats\LaravelScaffoldFeature\Helpers\Pagination\BuildPaginationMeta\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Stackrats\LaravelScaffoldFeature\Helpers\Pagination\BuildPaginationMeta\Data\PaginationMetaData;

class BuildPaginationMetaAction
{
    public function handle(
        LengthAwarePaginator $paginator
    ): PaginationMetaData {
        return new PaginationMetaData(
            currentPage: $paginator->currentPage(),
            lastPage: $paginator->lastPage(),
            perPage: $paginator->perPage(),
            totalItems: $paginator->total(),
        );
    }
}
