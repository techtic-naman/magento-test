<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\RewardPoints\Block\Product;

use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Framework\View\Element\Template\Context;
use MageWorx\RewardPoints\Helper\Data as HelperData;
use MageWorx\RewardPoints\Model\CustomerData;

class RewardPromise extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var HelperData
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
     * @param HelperData $helperData
     * @param Serializer $serializer
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        Serializer $serializer,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->helperData  = $helperData;
        $this->serializer  = $serializer;
        $this->httpContext = $httpContext;
    }

    /**
     * @return bool
     */
    public function canBeDisplayed(): bool
    {
        return $this->helperData->isDisplayPromiseMessageOnProduct($this->_storeManager->getStore()->getId())
            && $this->getProductId()
            && $this->getMessage();
    }

    /**
     * @return string
     */
    public function getJsonConfig(): string
    {
        return $this->serializer->serialize($this->getConfig());
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'message'    => $this->getMessage(),
            'productId'  => $this->getProductId(),
            'serviceUrl' => $this->getServiceUrl()
        ];
    }

    /**
     * @return int|null
     */
    protected function getProductId(): ?int
    {
        if ($this->getRequest()->getFullActionName() === 'catalog_product_view') {
            $productId = (int)$this->getRequest()->getParam('id');

            return $productId ?: null;
        }

        return null;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMessage()
    {
        return $this->escapeHtml(
            $this->helperData->getPromiseMessageForProduct(
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
