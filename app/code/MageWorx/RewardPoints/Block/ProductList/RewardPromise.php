<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\RewardPoints\Block\ProductList;

use Magento\Framework\View\Element\Template\Context;
use MageWorx\RewardPoints\Helper\Data;

class RewardPromise extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * RewardPromise constructor.
     *
     * @param Context $context
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->helperData  = $helperData;
        $this->httpContext = $httpContext;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function canBeDisplayed(): bool
    {
        return $this->helperData->isDisplayPromiseMessageOnCategory() && $this->getMessage();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMessage()
    {
        return $this->escapeHtml(
            $this->helperData->getPromiseMessageForCategory(
                $this->_storeManager->getStore()->getId()
            )
        );
    }

    /**
     * @return string
     */
    public function getServiceUrl()
    {
        if ($this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)) {
            return $this->getUrl('rest/V1/mw-rewardpoints/mine/getRewardPromise/');
        }

        return $this->getUrl('rest/V1/mw-rewardpoints/guest/getRewardPromise/');
    }
}
