<?php

declare(strict_types=1);

namespace Stackrats\LaravelScaffoldFeature\Tests;

use Orchestra\Testbench\TestCase;
use Stackrats\LaravelScaffoldFeature\ScaffoldFeatureServiceProvider;

class PackageTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ScaffoldFeatureServiceProvider::class,
        ];
    }
}
