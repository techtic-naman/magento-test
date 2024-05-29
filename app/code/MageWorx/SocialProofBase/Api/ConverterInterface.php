<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Api;

use Magento\Framework\DataObject;

interface ConverterInterface
{
    /**
     * Retrieve converted string by html template
     *
     * @param DataObject $campaign
     * @return string
     */
    public function convert($campaign): string;

    /**
     * @return array
     */
    public function getAllowedVars(): array;
}
