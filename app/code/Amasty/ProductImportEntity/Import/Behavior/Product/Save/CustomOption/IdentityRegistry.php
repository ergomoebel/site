<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Product\Save\CustomOption;

class IdentityRegistry
{
    /**
     * @var array
     */
    private $registry = [];

    /**
     * @var array
     */
    private $persistedIdentities = [];

    /**
     * Add identity to registry
     *
     * @param string $identity
     * @return void
     */
    public function add(string $identity)
    {
        $this->registry[$identity] = $identity;
    }

    /**
     * Mark identity as persisted
     *
     * @param string $identity
     * @return void
     */
    public function markPersisted(string $identity)
    {
        if (isset($this->registry[$identity])) {
            $this->persistedIdentities[$identity] = true;
        }
    }

    /**
     * Checks if identity was persisted
     *
     * @param string $identity
     * @return bool
     */
    public function isPersisted(string $identity)
    {
        return $this->persistedIdentities[$identity] ?? false;
    }

    /**
     * Clear the registry
     *
     * @return void
     */
    public function clear()
    {
        $this->registry = [];
        $this->persistedIdentities = [];
    }
}
