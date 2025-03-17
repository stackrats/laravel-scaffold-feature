<?php

declare(strict_types=1);

namespace Stackrats\LaravelScaffoldFeatureTests\Console\Commands;

use Illuminate\Support\Facades\App;
use Stackrats\LaravelScaffoldFeature\Services\ScaffoldFeatureService;

it('can run the command successfully', function () {
    $mockService = \Mockery::mock(ScaffoldFeatureService::class);
    $mockService->shouldReceive('getAvailableDirectories')
        ->once()
        ->andReturn(['Directory1', 'Directory2']);
    $mockService->shouldReceive('scaffold')
        ->once()
        ->andReturnNull();

    App::instance(ScaffoldFeatureService::class, $mockService);

    $this->artisan('scaffold:feature')
        ->expectsQuestion('Select the root directory:', 'App/Features/')
        ->expectsQuestion('Enter subdirectory (optional):', 'KnowledgeBase')
        ->expectsQuestion('Enter the feature name:', 'CreatePostSubmission')
        ->expectsConfirmation("Create feature directory for App/Features/KnowledgeBase/CreatePostSubmission?", 'yes')
        ->expectsChoice('Select the API route method:', 'post', ['post', 'get', 'put', 'delete'])
        ->expectsChoice('Select the directories to include for this feature:', ['Directory1', 'Directory2'], ['Directory1', 'Directory2'])
        ->assertSuccessful();
});
