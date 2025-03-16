<?php

declare(strict_types = 1);

namespace Stackrats\LaravelScaffoldFeature\Templates;

class TemplateManagerFactory
{
    /**
     * Create a template manager for the given API method and additional option.
     */
    public function create(string $apiMethod, ?string $additionalOption = null): TemplateManagerInterface
    {
        return match ($apiMethod) {
            'get' => new GetTemplateManager($additionalOption),
            'post' => new PostTemplateManager(),
            'put' => new PutTemplateManager(),
            'delete' => new DeleteTemplateManager(),
            default => throw new \InvalidArgumentException("Unsupported API method: {$apiMethod}"),
        };
    }
}