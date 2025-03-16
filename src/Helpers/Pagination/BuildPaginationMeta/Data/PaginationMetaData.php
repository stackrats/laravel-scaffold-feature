<?php

declare(strict_types = 1);

namespace Stackrats\LaravelScaffoldFeature\Helpers\Pagination\BuildPaginationMeta\Data;

use Spatie\LaravelData\Data;

class PaginationMetaData extends Data
{
    public function __construct(
        public int $currentPage,
        public int $lastPage,
        public int $perPage,
        public int $totalItems,
    ) {}
}
