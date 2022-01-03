<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Export\Template\Type\Twig\Sandbox;

class Policy
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var array
     */
    private $filters;

    /**
     * @var array
     */
    private $methods;

    /**
     * @var array
     */
    private $properties;

    /**
     * @var array
     */
    private $functions;

    public function __construct(
        bool $enabled = true,
        array $tags = [],
        array $filters = [],
        array $methods = [],
        array $properties = [],
        array $functions = []
    ) {
        $this->enabled = $enabled;
        $this->tags = $tags;
        $this->filters = $filters;
        $this->methods = $methods;
        $this->properties = $properties;
        $this->functions = $functions;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return $this->functions;
    }
}
