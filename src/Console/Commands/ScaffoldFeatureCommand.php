<?php

namespace Stackrats\LaravelScaffoldFeature\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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
     * Execute the console command.
     */
    public function handle()
    {
        // Available root directories
        $rootDirs = [
            'Features/' => 'App/Features/',
            'Shared/Features/' => 'App/Shared/Features/',
            '/' => 'App/',
        ];

        // Directory validation regex - PascalCase or start with underscore
        $dirPattern = '/^(?:[A-Z][a-zA-Z0-9]*|_[A-Z][a-zA-Z0-9]*)(?:\/(?:[A-Z][a-zA-Z0-9]*|_[A-Z][a-zA-Z0-9]*))*$/';
        // Feature name validation regex - PascalCase
        $featurePattern = '/^[A-Z][a-zA-Z0-9]*$/';

        // Prompt for the parent directory using select
        $rootDir = select(
            label: 'Select the root directory:',
            options: $rootDirs,
            default: 'App/Features/',
        );

        $subDir = text(
            label: 'Enter subdirectory (optional):',
            placeholder: 'E.g. KnowledgeBase',
            required: false,
            validate: fn (string $value) => empty($value) || preg_match($dirPattern, $value) ? null : 'Subdirectory must be in PascalCase or start with underscore.'
        );

        // Combine paths
        $parentDir = rtrim($rootDir, '/') . ($subDir ? '/' . trim($subDir, '/') : '');

        // Validate final path
        if (! preg_match($dirPattern, trim($parentDir, '/'))) {
            $this->error('Directory path must be in PascalCase or start with underscore.');

            return;
        }

        // Prompt for the feature name
        $featureName = text(
            label: 'Enter the feature name:',
            placeholder: 'E.g. CreatePostSubmission',
            required: 'Feature name is required.',
            validate: fn (string $value) => preg_match($featurePattern, $value) ? null : 'Feature name must be in PascalCase.'
        );

        // Confirm directory creation
        $confirmCreation = confirm(
            label: "Create feature directory for {$parentDir}/{$featureName}?",
            default: true
        );

        if (! $confirmCreation) {
            $this->info('Operation cancelled.');

            return;
        }

        // Prompt for API route method
        $apiMethod = select(
            label: 'Select the API route method:',
            options: ['post', 'get', 'put', 'delete'],
            default: 'post'
        );

        $additionalOption = null;
        if ($apiMethod === 'get') {
            // Additional prompt for GET methods
            $additionalOption = select(
                label: 'Select the GET action return type:',
                options: ['model', 'collection', 'paginate'],
                default: 'model'
            );
        }

        // Dynamically filter directories based on the API method and additional options
        $availableDirectories = $this->getAvailableDirectories($apiMethod, $additionalOption);

        // Prompt to select directories
        $directories = multiselect(
            label: 'Select the directories to include for this feature:',
            options: $availableDirectories,
            default: $availableDirectories,
            required: 'You must select at least one directory.'
        );

        // Base path
        $basePath = app_path(($parentDir ? "/{$parentDir}" : '') . "/{$featureName}");

        // Generate files for the selected directories
        foreach ($directories as $directory) {
            $this->generateFilesForDirectory($directory, $basePath, $parentDir, $featureName, $apiMethod, $additionalOption);
        }

        $this->info("Feature {$parentDir}/{$featureName} scaffolded successfully!");
    }

    /**
     * Get available directories based on API method and additional options.
     */
    protected function getAvailableDirectories(string $apiMethod, ?string $additionalOption): array
    {
        // Customize available directories based on the API method
        $directories = [
            'post' => ['Actions', 'Controllers', 'Handlers', 'Data', 'Data/Requests', 'Data/Responses', 'Routes', 'Tests'],
            'get' => ['Actions', 'Controllers', 'Data/Requests', 'Data/Responses', 'Routes', 'Tests'],
            'put' => ['Actions', 'Controllers', 'Handlers', 'Data', 'Data/Requests', 'Data/Responses', 'Routes', 'Tests'],
            'delete' => ['Actions', 'Controllers', 'Data/Requests', 'Routes', 'Tests'],
        ];

        // Return the filtered directories for the selected method
        return $directories[$apiMethod] ?? [];
    }

    /**
     * Generate files for a specific directory based on templates.
     */
    protected function generateFilesForDirectory(
        string $directory,
        string $basePath,
        string $parentDir,
        string $featureName,
        string $apiMethod,
        ?string $additionalOption
    ): void {
        // Define a configuration map for templates based on API method and directory
        $templateConfig = [
            'post' => [
                'Actions' => [
                    "{$featureName}Action.php" => 'action.stub',
                    "Build{$featureName}RspAction.php" => 'build_rsp_action.stub',
                ],
                'Controllers' => [
                    "{$featureName}Controller.php" => 'controller.stub',
                ],
                'Handlers' => [
                    "{$featureName}Handler.php" => 'handler.stub',
                ],
                'Data' => [
                    "{$featureName}ActionDto.php" => 'dto.stub',
                ],
                'Data/Requests' => [
                    "{$featureName}Req.php" => 'req.stub',
                ],
                'Data/Responses' => [
                    "{$featureName}Rsp.php" => 'rsp.stub',
                    "{$featureName}Data.php" => 'rsp_data.stub',
                ],
                'Routes' => [
                    'api.php' => 'routes.stub',
                ],
                'Tests' => [
                    "{$featureName}ActionTest.php" => 'test.stub',
                ],
            ],
            'get' => [
                'Actions' => [
                    "{$featureName}Action.php" => 'action.stub',
                    "Build{$featureName}RspAction.php" => 'build_rsp_action.stub',
                ],
                'Controllers' => [
                    "{$featureName}Controller.php" => 'controller.stub',
                ],
                'Data/Requests' => [
                    "{$featureName}Req.php" => 'req.stub',
                ],
                'Data/Responses' => [
                    "{$featureName}Rsp.php" => 'rsp.stub',
                    "{$featureName}Data.php" => 'rsp_data.stub',
                ],
                'Routes' => [
                    'api.php' => 'routes.stub',
                ],
                'Tests' => [
                    "{$featureName}ActionTest.php" => 'test.stub',
                ],
            ],
            'put' => [
                'Actions' => [
                    "{$featureName}Action.php" => 'action.stub',
                    "Build{$featureName}RspAction.php" => 'build_rsp_action.stub',
                ],
                'Controllers' => [
                    "{$featureName}Controller.php" => 'controller.stub',
                ],
                'Handlers' => [
                    "{$featureName}Handler.php" => 'handler.stub',
                ],
                'Data' => [
                    "{$featureName}ActionDto.php" => 'dto.stub',
                ],
                'Data/Requests' => [
                    "{$featureName}Req.php" => 'req.stub',
                ],
                'Data/Responses' => [
                    "{$featureName}Rsp.php" => 'rsp.stub',
                    "{$featureName}Data.php" => 'rsp_data.stub',
                ],
                'Routes' => [
                    'api.php' => 'routes.stub',
                ],
                'Tests' => [
                    "{$featureName}ActionTest.php" => 'test.stub',
                ],
            ],
            'delete' => [
                'Actions' => [
                    "{$featureName}Action.php" => 'action.stub',
                ],
                'Controllers' => [
                    "{$featureName}Controller.php" => 'controller.stub',
                ],
                'Data/Requests' => [
                    "{$featureName}Req.php" => 'req.stub',
                ],
                'Routes' => [
                    'api.php' => 'routes.stub',
                ],
                'Tests' => [
                    "{$featureName}ActionTest.php" => 'test.stub',
                ],
            ],
        ];

        $directories = [
            'Actions',
            'Controllers',
            'Handlers',
            'Data',
            'Data/Requests',
            'Data/Responses',
            'Routes',
        ];
        $pathTemplate = ($apiMethod === 'get' && $additionalOption)
            ? "{$apiMethod}/{$additionalOption}/%s"
            : "{$apiMethod}/%s";

        foreach ($directories as $dir) {
            if (isset($templateConfig[$apiMethod][$dir])) {
                $templateConfig[$apiMethod][$dir] = array_map(
                    fn ($template) => sprintf($pathTemplate, $template),
                    $templateConfig[$apiMethod][$dir]
                );
            }
        }

        // Get the template configuration for the current API method
        $methodTemplates = $templateConfig[$apiMethod] ?? [];

        // If the directory is not in the method's templates, return
        if (! isset($methodTemplates[$directory])) {
            return;
        }

        // Get the templates for the current directory
        $templates = $methodTemplates[$directory];

        $dirPath = "{$basePath}/{$directory}";
        File::makeDirectory($dirPath, 0755, true);

        foreach ($templates as $fileName => $templateFile) {
            $this->createFileFromTemplate($dirPath, $fileName, $templateFile, $parentDir, $featureName, $directory);
        }
    }

    /**
     * Create a file from a template.
     */
    protected function createFileFromTemplate(
        string $dirPath,
        string $fileName,
        string $templateFile,
        string $parentDir,
        string $featureName,
        string $directory
    ): void {
        $templatePath = resource_path('templates/vendor/laravel-scaffold-feature/'.$templateFile);

        if (! File::exists($templatePath)) {
            // Fall back to default template path in the package
            $templatePath = __DIR__ . '/../../../resources/templates/scaffold-feature/' . $templateFile;
        }

        if (! File::exists($templatePath)) {
            $this->error("Template file {$templateFile} not found!");

            return;
        }

        $content = File::get($templatePath);

        // Trim any leading or trailing slashes from the parent directory
        $parentDir = trim($parentDir, '/');

        $parentNamespace = $parentDir ? str_replace('/', '\\', $parentDir) : null;

        // Dynamically construct the full namespace with conditional slash
        $fullNamespace = $parentNamespace
            ? "{$parentNamespace}\\{$featureName}"
            : $featureName;

        // Replace placeholders in the template
        $replacements = [
            '{{NAMESPACE}}' => str_replace('/', '\\', 'App\\' . ($parentDir ? "{$parentDir}\\" : '') . "{$featureName}\\{$directory}"),
            '{{PARENT_NAMESPACE}}' => $parentNamespace ? "{$parentNamespace}" : '',
            '{{FULL_NAMESPACE}}' => $fullNamespace,
            '{{CLASS_NAME}}' => $this->getClassName($fileName, $featureName),
            '{{FEATURE_NAME}}' => $featureName,
            '{{FEATURE_NAME_LCFIRST}}' => Str::lcfirst($featureName),
            '{{FEATURE_NAME_LOWERCASE_KEBAB}}' => Str::kebab($featureName),
        ];

        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        // Save the generated file
        $filePath = "{$dirPath}/" . $this->getFileName($fileName, $featureName);
        File::put($filePath, $content);

        $this->info("Created: {$filePath}");
    }

    /**
     * Get the appropriate stub for the given file name and API configuration.
     */
    protected function getTemplateStub(
        string $fileName,
        ?string $apiMethod = null,
        ?string $additionalOption = null
    ): string {
        // Map the stub location based on API method and additional option
        $pathParts = array_filter([
            $apiMethod,
            $additionalOption,
            $fileName,
        ]);

        return implode('/', $pathParts);
    }

    /**
     * Get the class name for the file.
     */
    protected function getClassName(string $fileName, string $featureName): string
    {
        return str_replace('FileName', $featureName, pathinfo($fileName, PATHINFO_FILENAME));
    }

    /**
     * Get the final file name.
     */
    protected function getFileName(string $fileName, string $featureName): string
    {
        return str_replace('FileName', $featureName, $fileName);
    }
}
