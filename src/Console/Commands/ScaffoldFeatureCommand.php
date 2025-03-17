<?php

declare(strict_types=1);

namespace Stackrats\LaravelScaffoldFeature\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Stackrats\LaravelScaffoldFeature\Services\ScaffoldFeatureService;
use Stackrats\LaravelScaffoldFeature\Dtos\ScaffoldFeatureDto;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class ScaffoldFeatureCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scaffold:feature';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold new feature directories & files from prompts';

    /**
     * @var ScaffoldFeatureService
     */
    protected $scaffoldFeatureService;

    /**
     * Create a new command instance.
     */
    public function __construct(ScaffoldFeatureService $scaffoldFeatureService)
    {
        parent::__construct();

        $this->scaffoldFeatureService = $scaffoldFeatureService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = $this->getConfig();

        // Prompt for the parent directory using select
        $rootDir = $this->promptForRootDirectory($config['root_dirs']);

        // Prompt for subdirectory
        $subDir = $this->promptForSubdirectory($config['validation']['dir_pattern']);

        // Combine paths
        $parentDir = $this->combineDirectoryPaths($rootDir, $subDir);

        // Validate the combined path
        if (!$this->validateDirectoryPath($parentDir, $config['validation']['dir_pattern'])) {
            $this->error('Directory path must be in PascalCase or start with underscore.');
            return;
        }

        // Prompt for feature name
        $featureName = $this->promptForFeatureName($config['validation']['feature_pattern']);

        // Confirm directory creation
        if (!$this->confirmFeatureCreation($parentDir, $featureName)) {
            $this->info('Operation cancelled.');
            return;
        }

        // Prompt for API method and options
        $apiMethod = $this->promptForApiMethod();
        $additionalOption = $this->promptForAdditionalOptions($apiMethod);

        // Get available directories and prompt for selection
        $availableDirectories = $this->scaffoldFeatureService->getAvailableDirectories($apiMethod, $additionalOption);
        $selectedDirectories = $this->promptForDirectories($availableDirectories);

        // Create DTO for scaffolding
        $scaffoldDTO = new ScaffoldFeatureDto(
            $parentDir,
            $featureName,
            $apiMethod,
            $additionalOption,
            $selectedDirectories
        );

        try {
            // Generate the feature
            $this->scaffoldFeatureService->scaffold($scaffoldDTO, $this);
            $this->info("Feature {$parentDir}/{$featureName} scaffolded successfully!");
        } catch (\Exception $e) {
            $this->error("Error scaffolding feature: {$e->getMessage()}");
        }
    }

    /**
     * Get configuration from config file.
     */
    protected function getConfig(): array
    {
        $config = config('laravel-scaffold-feature');

        return [
            'root_dirs' => $config['root_dirs'] ?? [
                'Features/'         => 'App/Features/',
                'Shared/Features/'  => 'App/Shared/Features/',
                '/'                 => 'App/',
            ],
            'validation' => [
                'dir_pattern' => $config['validation']['dir_pattern'] ??
                    '/^(?:[A-Z][a-zA-Z0-9]*|_[A-Z][a-zA-Z0-9]*)(?:\/(?:[A-Z][a-zA-Z0-9]*|_[A-Z][a-zA-Z0-9]*))*$/',
                'feature_pattern' => $config['validation']['feature_pattern'] ?? '/^[A-Z][a-zA-Z0-9]*$/',
            ],
        ];
    }

    /**
     * Prompt for root directory.
     */
    protected function promptForRootDirectory(array $rootDirs): string
    {
        return select(
            label: 'Select the root directory:',
            options: $rootDirs,
            default: 'App/Features/',
        );
    }

    /**
     * Prompt for subdirectory.
     */
    protected function promptForSubdirectory(string $dirPattern): string
    {
        return text(
            label: 'Enter subdirectory (optional):',
            placeholder: 'E.g. KnowledgeBase',
            required: false,
            validate: fn (string $value) => empty($value) || preg_match($dirPattern, $value)
                ? null
                : 'Subdirectory must be in PascalCase or start with underscore.'
        );
    }

    /**
     * Combine directory paths.
     */
    protected function combineDirectoryPaths(string $rootDir, ?string $subDir): string
    {
        return rtrim($rootDir, '/') . ($subDir ? '/' . trim($subDir, '/') : '');
    }

    /**
     * Validate directory path.
     */
    protected function validateDirectoryPath(string $path, string $pattern): bool
    {
        return (bool) preg_match($pattern, trim($path, '/'));
    }

    /**
     * Prompt for feature name.
     */
    protected function promptForFeatureName(string $featurePattern): string
    {
        return text(
            label: 'Enter the feature name:',
            placeholder: 'E.g. CreatePostSubmission',
            required: 'Feature name is required.',
            validate: fn (string $value) => preg_match($featurePattern, $value)
                ? null
                : 'Feature name must be in PascalCase.'
        );
    }

    /**
     * Confirm feature creation.
     */
    protected function confirmFeatureCreation(string $parentDir, string $featureName): bool
    {
        return confirm(
            label: "Create feature directory for {$parentDir}/{$featureName}?",
            default: true
        );
    }

    /**
     * Prompt for API method.
     */
    protected function promptForApiMethod(): string
    {
        return select(
            label: 'Select the API route method:',
            options: ['post', 'get', 'put', 'delete'],
            default: 'post'
        );
    }

    /**
     * Prompt for additional options if needed.
     */
    protected function promptForAdditionalOptions(string $apiMethod): ?string
    {
        if ($apiMethod === 'get') {
            return select(
                label: 'Select the GET action return type:',
                options: ['model', 'collection', 'paginate'],
                default: 'model'
            );
        }

        return null;
    }

    /**
     * Prompt for directories to include.
     */
    protected function promptForDirectories(array $availableDirectories): array
    {
        return multiselect(
            label: 'Select the directories to include for this feature:',
            options: $availableDirectories,
            default: $availableDirectories,
            required: 'You must select at least one directory.'
        );
    }
}
