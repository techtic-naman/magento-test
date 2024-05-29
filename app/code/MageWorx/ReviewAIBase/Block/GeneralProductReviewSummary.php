<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Block;

class GeneralProductReviewSummary extends ProductReviewSummary
{
    /**
     * Check is need to display the review summary block on product page
     *
     * @return bool
     */
    public function isNeedToDisplayReviewSummary(): bool
    {
        if ($this->config->isXReviewEnabled()) {
            // Do not display additional block, as we have one in our XReviewBase template
            // In MageWorx_XReviewBase @see view/frontend/templates/review/product/list.phtml:21
            return false;
        }

        return parent::isNeedToDisplayReviewSummary();
    }
}

