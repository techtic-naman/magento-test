<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PersonalPromotion\Helper;

use Magento\Framework\EntityManager\MetadataPool;

class LinkFieldResolver extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * LinkFieldResolver constructor.
     *
     * @param MetadataPool $metadataPool
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        MetadataPool $metadataPool,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->metadataPool = $metadataPool;
        parent::__construct($context);
    }

    /**
     * @param string $class
     * @return string
     * @throws \Exception
     */
    public function getLinkField($class)
    {
        return $this->metadataPool->getMetadata($class)->getLinkField();
    }

}