<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Api;

interface VariableStorageInterface
{
    /**
     * Retrieves the value of a general variable based on the provided variable name and store ID.
     *
     * @param string $variable The name of the variable to retrieve the value for.
     * @param int $storeId The ID of the store to fetch the variable value for.
     *
     * @return string|null The value of the general variable, or null if the variable name is invalid.
     */
    public function getGeneralVariableValue(string $variable, int $storeId): ?string;
}
