<?php

declare(strict_types=1);

namespace Stackrats\LaravelScaffoldFeature\Dtos;

class ScaffoldFeatureDto
{
    /**
     * Parent directory path.
     *
     * @var string
     */
    public $parentDir;

    /**
     * Feature name.
     *
     * @var string
     */
    public $featureName;

    /**
     * API method.
     *
     * @var string
     */
    public $apiMethod;

    /**
     * Additional option.
     *
     * @var string|null
     */
    public $additionalOption;

    /**
     * Selected directories.
     *
     * @var array<string>
     */
    public $directories;

    /**
     * Create a new DTO instance.
     */
    public function __construct(
        string $parentDir,
        string $featureName,
        string $apiMethod,
        ?string $additionalOption,
        array $directories
    ) {
        $this->parentDir = $parentDir;
        $this->featureName = $featureName;
        $this->apiMethod = $apiMethod;
        $this->additionalOption = $additionalOption;
        $this->directories = $directories;
    }
}
