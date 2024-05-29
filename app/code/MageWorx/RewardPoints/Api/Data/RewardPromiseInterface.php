<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\RewardPoints\Api\Data;

interface RewardPromiseInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const AMOUNT_KEY = 'amount';

    const PRODUCT_ID_KEY = 'product_id';

    /**
     * @return float
     */
    public function getAmount(): float;

    /**
     * @return int
     */
    public function getProductId(): int;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \MageWorx\RewardPoints\Api\Data\RewardPromiseExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \MageWorx\RewardPoints\Api\Data\RewardPromiseExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \MageWorx\RewardPoints\Api\Data\RewardPromiseExtensionInterface $extensionAttributes
    );
}
