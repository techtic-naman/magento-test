<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PersonalPromotion\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;

class Data extends AbstractHelper
{
    /**
     * Config paths to settings
     */
    const ENABLE_PERSONAL_PROMOTION = 'mageworx_personalpromotion/main/enable_personalpromotion';

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * Calculate constructor.
     *
     * @param Context $context
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        Context $context,
        ProductMetadataInterface $productMetadata
    ) {
        parent::__construct($context);
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param null|int $storeId
     *
     * @return bool
     */
    public function isEnablePersonalPromotion($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::ENABLE_PERSONAL_PROMOTION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

}
