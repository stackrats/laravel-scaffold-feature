<?php

declare(strict_types=1);

namespace Stackrats\LaravelScaffoldFeature\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Stackrats\LaravelScaffoldFeature\DTOs\ScaffoldFeatureDTO;
use Stackrats\LaravelScaffoldFeature\Templates\TemplateManagerFactory;

class ScaffoldFeatureService
{
    /**
     * @var TemplateManagerFactory
     */
    protected $templateManagerFactory;

    /**
     * Create a new feature scaffolder instance.
     */
    public function __construct(
        TemplateManagerFactory $templateManagerFactory
    ) {
        $this->templateManagerFactory = $templateManagerFactory;
    }

    /**
     * Get available directories based on API method and additional options.
     */
    public function getAvailableDirectories(string $apiMethod, ?string $additionalOption): array
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
     * Scaffold a new feature.
     */
    public function scaffold(
        ScaffoldFeatureDTO $dto,
        Command $command
    ): void {
        $basePath = app_path(($dto->parentDir ? "/{$dto->parentDir}" : '') . "/{$dto->featureName}");

        // Get the template manager for the selected API method
        $templateManager = $this->templateManagerFactory->create($dto->apiMethod, $dto->additionalOption);

        // Generate files for the selected directories
        foreach ($dto->directories as $directory) {
            $this->generateFilesForDirectory(
                $templateManager,
                $directory,
                $basePath,
                $dto->parentDir,
                $dto->featureName,
                $command
            );
        }
    }

    /**
     * Generate files for a specific directory.
     */
    protected function generateFilesForDirectory(
        $templateManager,
        string $directory,
        string $basePath,
        string $parentDir,
        string $featureName,
        Command $command
    ): void {
        // Get templates for the current directory
        $templates = $templateManager->getTemplatesForDirectory($directory);

        if (empty($templates)) {
            return;
        }

        $dirPath = "{$basePath}/{$directory}";
        File::makeDirectory($dirPath, 0755, true);

        foreach ($templates as $fileName => $templateFile) {
            // First, replace placeholders in the fileName itself
            $processedFileName = $this->replacePlaceholdersInString($fileName, [
                'FEATURE_NAME' => $featureName,
                'FEATURE_NAME_LCFIRST' => Str::lcfirst($featureName),
                'FEATURE_NAME_LOWERCASE_KEBAB' => Str::kebab($featureName),
            ]);

            $this->createFileFromTemplate(
                $dirPath,
                $processedFileName,
                $templateFile,
                $parentDir,
                $featureName,
                $directory,
                $command
            );
        }
    }

    /**
     * Replace placeholders in a string with actual values.
     */
    protected function replacePlaceholdersInString(
        string $input,
        array $replacements
    ): string {
        $placeholders = array_map(function ($key) {
            return '{{' . $key . '}}';
        }, array_keys($replacements));

        return str_replace($placeholders, array_values($replacements), $input);
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
        string $directory,
        Command $command
    ): void {
        // Look for published templates first
        $templatePath = resource_path('templates/vendor/laravel-scaffold-feature/' . $templateFile);

        if (! File::exists($templatePath)) {
            // Fallback to default template path in the package
            $templatePath = __DIR__ . '/../resources/templates/scaffold-feature/' . $templateFile;
        }

        if (! File::exists($templatePath)) {
            $command->error("Template file {$templateFile} not found!");
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
        $filePath = "{$dirPath}/{$fileName}";
        File::put($filePath, $content);

        $command->info("Created: {$filePath}");
    }

    /**
    * Get the class name for the file.
    */
    protected function getClassName(
        string $fileName,
        string $featureName
    ): string {
        return str_replace('FileName', $featureName, pathinfo($fileName, PATHINFO_FILENAME));
    }

    /**
     * Get the final file name.
     */
    protected function getFileName(
        string $fileName,
        string $featureName
    ): string {
        return str_replace('FileName', $featureName, $fileName);
    }
}
