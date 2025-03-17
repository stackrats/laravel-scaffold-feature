<?php

declare(strict_types=1);

namespace Stackrats\LaravelScaffoldFeature\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Stackrats\LaravelScaffoldFeature\Helpers\Pagination\BuildPaginationMeta\Actions\BuildPaginationMetaAction;
use Stackrats\LaravelScaffoldFeature\Helpers\Pagination\BuildPaginationMeta\Data\PaginationMetaData;

class PaginationMetaService
{
    public function build(
        LengthAwarePaginator $paginator
    ): PaginationMetaData {
        return (new BuildPaginationMetaAction())->handle($paginator);
    }
}
