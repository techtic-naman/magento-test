<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Plugin;

use Magento\Quote\Api\Data\TotalsExtensionFactory;
use Magento\Quote\Api\Data\TotalsInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Model\Quote;

class TransferPointsDataFromQuoteToTotalPlugin
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var TotalsExtensionFactory
     */
    protected $totalsExtensionFactory;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param TotalsExtensionFactory $totalsExtensionFactory
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        TotalsExtensionFactory $totalsExtensionFactory
    ) {
        $this->quoteRepository        = $quoteRepository;
        $this->totalsExtensionFactory = $totalsExtensionFactory;
    }

    /**
     * @param TotalRepository $subject
     * @param TotalsInterface $totals
     * @param int $cartId
     * @return TotalsInterface
     */
    public function afterGet(CartTotalRepositoryInterface $subject, TotalsInterface $totals, $cartId)
    {
        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        /** @var \Magento\Quote\Api\Data\TotalsExtensionInterface $extensionAttributes */
        $extensionAttributes = $totals->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->totalsExtensionFactory->create();
        }

        $extensionAttributes->setMwRwrdpointsAmnt($quote->getMwRwrdpointsAmnt());
        $extensionAttributes->setMwRwrdpointsCurAmnt($quote->getMwRwrdpointsCurAmnt());
        $extensionAttributes->setBaseRwrdpointsCurAmnt($quote->getBaseMwRwrdpointsCurAmnt());
        $extensionAttributes->setMwEarnPointsData($quote->getMwEarnPointsData());

        $totals->setExtensionAttributes($extensionAttributes);

        return $totals;
    }
}
