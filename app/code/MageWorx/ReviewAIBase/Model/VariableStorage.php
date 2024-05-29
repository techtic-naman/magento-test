<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model;

use MageWorx\ReviewAIBase\Helper\Config;
use MageWorx\ReviewAIBase\Api\VariableStorageInterface;

class VariableStorage implements VariableStorageInterface
{
    protected Config $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Retrieves the value of a general variable based on the provided variable name and store ID.
     *
     * @param string $variable The name of the variable to retrieve the value for.
     * @param int $storeId The ID of the store to fetch the variable value for.
     *
     * @return string|null The value of the general variable, or null if the variable name is invalid.
     */
    public function getGeneralVariableValue(string $variable, int $storeId): ?string
    {
        if ($variable === 'max_length') {
            return (string)$this->config->getDesiredSummaryLength($storeId);
        }

        return null;
    }
}
