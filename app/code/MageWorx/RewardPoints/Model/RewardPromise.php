<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\RewardPoints\Model;

use MageWorx\RewardPoints\Api\Data\RewardPromiseInterface;

class RewardPromise extends \Magento\Framework\Model\AbstractExtensibleModel implements RewardPromiseInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAmount(): float
    {
        return (float)$this->getData(self::AMOUNT_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId(): int
    {
        return (int)$this->getData(self::PRODUCT_ID_KEY);
    }

    /**
     * @inheritDoc
     *
     * @return \MageWorx\RewardPoints\Api\Data\RewardPromiseExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritDoc
     *
     * @param \MageWorx\RewardPoints\Api\Data\RewardPromiseExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \MageWorx\RewardPoints\Api\Data\RewardPromiseExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
