<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Customer\Account;

class Wrapper extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * Wrapper constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * @param string $alias
     * @param bool $useCache
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getChildHtml($alias = '', $useCache = true)
    {
        if ($this->helperData->isEnableForCustomer()) {

            $html = '';

            $staticBlockId = $this->helperData->getCustomerPointsBlockId();

            if ($staticBlockId && !$this->getHideDescription()) {
                $html .= $this->getLayout()
                              ->createBlock('Magento\Cms\Block\Block')
                              ->setBlockId($staticBlockId)
                              ->toHtml();
            }

            $html .= parent::getChildHtml($alias, $useCache);

            return $html;
        }

        return '';
    }
}