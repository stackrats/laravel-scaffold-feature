<?php

declare(strict_types = 1);

namespace Stackrats\LaravelScaffoldFeature\Templates;

class PutTemplateManager extends AbstractTemplateManager
{
    /**
     * The API method.
     *
     * @var string
     */
    public const API_METHOD = 'put';
    
    /**
     * Get the template configuration.
     */
    protected function getTemplateConfig(): array
    {
        return [
            'Actions' => [
                "{{FEATURE_NAME}}Action.php" => 'action.stub',
                "Build{{FEATURE_NAME}}RspAction.php" => 'build_rsp_action.stub',
            ],
            'Controllers' => [
                "{{FEATURE_NAME}}Controller.php" => 'controller.stub',
            ],
            'Handlers' => [
                "{{FEATURE_NAME}}Handler.php" => 'handler.stub',
            ],
            'Data' => [
                "{{FEATURE_NAME}}ActionDto.php" => 'dto.stub',
            ],
            'Data/Requests' => [
                "{{FEATURE_NAME}}Req.php" => 'req.stub',
            ],
            'Data/Responses' => [
                "{{FEATURE_NAME}}Rsp.php" => 'rsp.stub',
                "{{FEATURE_NAME}}Data.php" => 'rsp_data.stub',
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