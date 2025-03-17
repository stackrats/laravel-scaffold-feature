<?php

declare(strict_types=1);

namespace Stackrats\LaravelScaffoldFeatureTests\Facades;

use Illuminate\Pagination\LengthAwarePaginator;
use Stackrats\LaravelScaffoldFeature\Facades\PaginationMeta;
use Stackrats\LaravelScaffoldFeature\Helpers\Pagination\BuildPaginationMeta\Actions\BuildPaginationMetaAction;
use Stackrats\LaravelScaffoldFeature\Services\PaginationMetaService;

it('makes PaginationMeta facade available in the project', function () {
    $instance = PaginationMeta::getFacadeRoot();

    expect($instance)->toBeInstanceOf(PaginationMetaService::class);
});

it('builds pagination meta correctly', function () {
    // Create dummy data and a LengthAwarePaginator instance
    $data = range(1, 50);
    $paginator = new LengthAwarePaginator(
        array_slice($data, 0, 10),
        count($data),
        10,
        1
    );

    // Call the action
    $action = new BuildPaginationMetaAction();
    $metaData = $action->handle($paginator);

    // Assert expected values
    expect($metaData->currentPage)->toBe(1);
    expect($metaData->lastPage)->toBe((int)ceil(count($data) / 10));
    expect($metaData->perPage)->toBe(10);
    expect($metaData->totalItems)->toBe(count($data));
});

it('can build pagination meta using the facade', function () {
    // Create dummy data and a LengthAwarePaginator instance
    $data = range(1, 50);
    $paginator = new LengthAwarePaginator(
        array_slice($data, 0, 10),
        count($data),
        10,
        1
    );

    // Use the facade (which resolves the PaginationMetaService from the container)
    $metaData = PaginationMeta::build($paginator);

    // Assert that the meta data contains correct values
    expect($metaData->currentPage)->toBe(1);
    expect($metaData->lastPage)->toBe((int)ceil(count($data) / 10));
    expect($metaData->perPage)->toBe(10);
    expect($metaData->totalItems)->toBe(count($data));
});
