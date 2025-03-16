<?php

declare(strict_types = 1);

namespace Stackrats\LaravelScaffoldFeature\Templates;

interface TemplateManagerInterface
{
    /**
     * Get templates for a specific directory.
     *
     * @param string $directory
     * @return array<string, string> Key-value pairs of fileName => templateFile
     */
    public function getTemplatesForDirectory(string $directory): array;
}