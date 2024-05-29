<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewAIBase\Model\Config\Source;

class ReviewAIModels extends \MageWorx\OpenAI\Model\Source\OpenAIModels
{
    protected array $allowedModels = [];

    /**
     * @param array $allowedModels
     */
    public function __construct(
        array $allowedModels = []
    ) {
        $this->allowedModels = $allowedModels;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        $origOptions = parent::toOptionArray();
        foreach ($origOptions as $origOption) {
            if (in_array($origOption['value'], $this->allowedModels)) {
                $options[] = $origOption;
            }
        }

        return $options;
    }
}
