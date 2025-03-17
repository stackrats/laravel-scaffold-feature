<?php

declare(strict_types=1);

namespace Stackrats\LaravelScaffoldFeature\Templates;

class DeleteTemplateManager extends AbstractTemplateManager
{
    /**
     * The API method.
     *
     * @var string
     */
    public const API_METHOD = 'delete';

    /**
     * Get the template configuration.
     */
    protected function getTemplateConfig(): array
    {
        return [
            'Actions' => [
                "{{FEATURE_NAME}}Action.php" => 'action.stub',
            ],
            'Controllers' => [
                "{{FEATURE_NAME}}Controller.php" => 'controller.stub',
            ],
            'Data/Requests' => [
                "{{FEATURE_NAME}}Req.php" => 'req.stub',
            ],
            'Routes' => [
                'api.php' => 'routes.stub',
            ],
            'Tests' => [
                "{{FEATURE_NAME}}ActionTest.php" => 'test.stub',
            ],
        ];
    }
}
