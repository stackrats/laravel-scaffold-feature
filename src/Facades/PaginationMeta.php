<?php

declare(strict_types=1);

namespace Stackrats\LaravelScaffoldFeature\Facades;

use Illuminate\Support\Facades\Facade;
use Stackrats\LaravelScaffoldFeature\Services\PaginationMetaService;

class PaginationMeta extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PaginationMetaService::class;
    }
}
