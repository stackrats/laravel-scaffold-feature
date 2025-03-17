<?php

declare(strict_types=1);

namespace Tests\Console\Commands;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Stackrats\LaravelScaffoldFeature\Services\ScaffoldFeatureService;
use Stackrats\LaravelScaffoldFeature\Dtos\ScaffoldFeatureDto;
use Stackrats\LaravelScaffoldFeature\Templates\TemplateManagerFactory;

beforeEach(function () {
    $this->testDir = __DIR__ . '/../../scaffold-feature-output';
    if (! File::exists($this->testDir)) {
        File::makeDirectory($this->testDir, 0755, true);
    }

    // Override the application's app path so that app_path() returns our package output directory.
    app()->useAppPath($this->testDir);

    // Override configuration so the command uses our package directory.
    config([
        'laravel-scaffold-feature.root_dirs' => [
            'TestFeatures/' => $this->testDir . '/TestFeatures/',
        ],
        'laravel-scaffold-feature.validation.dir_pattern' =>
            '/^(?:[A-Z][a-zA-Z0-9]*|_[A-Z][a-zA-Z0-9]*)(?:\/(?:[A-Z][a-zA-Z0-9]*|_[A-Z][a-zA-Z0-9]*))*$/',
        'laravel-scaffold-feature.validation.feature_pattern' => '/^[A-Z][a-zA-Z0-9]*$/',
    ]);
});

afterEach(function () {
    // Comment out or remove cleanup if you want to inspect the created dirs and files.
    File::deleteDirectory($this->testDir);
});

it('creates directories and files when running scaffold:feature command', function () {
    App::instance(ScaffoldFeatureService::class, new class (new TemplateManagerFactory()) extends ScaffoldFeatureService {
        public function __construct($dummyFactory)
        {
            parent::__construct($dummyFactory);
        }

        public function scaffold(ScaffoldFeatureDto $dto, $command): void
        {
            // Mimic the actual service behavior by using app_path().
            $basePath = app_path(($dto->parentDir ? "/{$dto->parentDir}" : '') . "/{$dto->featureName}");
            // Create the feature directory.
            File::makeDirectory($basePath, 0755, true);
            // Create a dummy file to simulate file generation.
            File::put($basePath . '/dummy.txt', 'dummy content');
        }

        public function getAvailableDirectories(string $apiMethod, ?string $additionalOption): array
        {
            // Return a dummy list of directories.
            return ['DummyDirectory'];
        }
    });

    // Simulate the interactive command.
    $this->artisan('scaffold:feature')
        ->expectsQuestion('Select the root directory:', 'TestFeatures/')
        ->expectsQuestion('Enter subdirectory (optional):', 'SubDir')
        ->expectsQuestion('Enter the feature name:', 'MyFeature')
        ->expectsConfirmation("Create feature directory for TestFeatures/SubDir/MyFeature?", 'yes')
        ->expectsChoice('Select the API route method:', 'post', ['post', 'get', 'put', 'delete'])
        ->expectsChoice(
            'Select the directories to include for this feature:',
            ['DummyDirectory'],
            ['DummyDirectory']
        )
        ->assertSuccessful();

    // Build the expected feature directory path and file path.
    $featureDir = $this->testDir . '/TestFeatures/SubDir/MyFeature';
    $dummyFile  = $featureDir . '/dummy.txt';

    // Assert that the feature directory was created.
    expect(File::exists($featureDir))->toBeTrue();
    // Assert that the dummy file was created.
    expect(File::exists($dummyFile))->toBeTrue();
});
