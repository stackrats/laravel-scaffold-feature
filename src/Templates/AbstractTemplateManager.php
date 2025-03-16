<?php

declare(strict_types = 1);

namespace Stackrats\LaravelScaffoldFeature\Templates;

abstract class AbstractTemplateManager implements TemplateManagerInterface
{
    /**
     * The API method to be used in the path template.
     */
    protected const API_METHOD = 'none';

    /**
     * Get the path template for templates.
     */
    protected function getPathTemplate(): string
    {
        return static::API_METHOD . '/%s';
    }

    /**
     * Get the template configuration.
     */
    abstract protected function getTemplateConfig(): array;

    /**
     * Get templates for a specific directory.
     */
    public function getTemplatesForDirectory(string $directory): array
    {
        $config = $this->getTemplateConfig();
        
        if (!isset($config[$directory])) {
            return [];
        }
        
        $pathTemplate = $this->getPathTemplate();
        $templates = [];
        
        foreach ($config[$directory] as $fileName => $templateFile) {
            // We need to keep template placeholders in the filenames intact
            // They will be replaced later in the createFileFromTemplate method
            $templates[$fileName] = sprintf($pathTemplate, $templateFile);
        }
        
        return $templates;
    }
}