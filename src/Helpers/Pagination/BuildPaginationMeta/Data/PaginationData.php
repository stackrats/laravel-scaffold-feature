<?php

declare(strict_types=1);

namespace Stackrats\LaravelScaffoldFeature\Helpers\Pagination\BuildPaginationMeta\Data;

use Spatie\LaravelData\Data;

class PaginationData extends Data
{
    public function __construct(
        public PaginationMetaData $meta,
    ) {
    }
}
