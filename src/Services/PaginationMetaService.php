<?php

declare(strict_types=1);

namespace Stackrats\LaravelScaffoldFeature\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Stackrats\LaravelScaffoldFeature\Helpers\Pagination\BuildPaginationMeta\Actions\BuildPaginationMetaAction;

class PaginationMetaService
{
    public function build(
        LengthAwarePaginator $paginator
    ): array {
        return [
            'meta' => (new BuildPaginationMetaAction())->handle($paginator),
        ];
    }
}
