<?php

declare(strict_types = 1);

namespace Stackrats\LaravelScaffoldFeature\Templates;

class GetTemplateManager extends AbstractTemplateManager
{
    /**
     * The API method.
     *
     * @var string
     */
    public const API_METHOD = 'get';
    
    /**
     * The additional option.
     *
     * @var string|null
     */
    protected $additionalOption;
    
    /**
     * Create a new template manager instance.
     */
    public function __construct(?string $additionalOption = null)
    {
        $this->additionalOption = $additionalOption;
    }
    
    /**
     * Get the path template for templates.
     */
    protected function getPathTemplate(): string
    {
        return $this->additionalOption
            ? self::API_METHOD . '/' . $this->additionalOption . '/%s'
            : self::API_METHOD . '/%s';
    }
    
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