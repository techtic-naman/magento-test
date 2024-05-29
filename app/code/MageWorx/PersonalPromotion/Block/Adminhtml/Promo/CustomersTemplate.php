<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PersonalPromotion\Block\Adminhtml\Promo;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollection;
use MageWorx\PersonalPromotion\Model\ResourceModel\PersonalPromotion as PersonalPromotionResourceModel;
use MageWorx\PersonalPromotion\Helper\Rule as HelperRule;

class CustomersTemplate extends \Magento\Backend\Block\Template
{
    /**
     * Block Grid
     */
    protected $blockGrid;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var HelperRule
     */
    protected $helperRule;

    /**
     * AssignCustomers constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param CustomerCollection $customerFactory
     * @param PersonalPromotionResourceModel $personalPromotionResourceModel
     * @param HelperRule $helperRule
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        CustomerCollection $customerFactory,
        PersonalPromotionResourceModel $personalPromotionResourceModel,
        HelperRule $helperRule,
        array $data = []
    ) {
        $this->registry                       = $registry;
        $this->jsonEncoder                    = $jsonEncoder;
        $this->customerFactory                = $customerFactory;
        $this->personalPromotionResourceModel = $personalPromotionResourceModel;
        $this->helperRule                     = $helperRule;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                \MageWorx\PersonalPromotion\Block\Adminhtml\Promo\Tab\Customers::class,
                'promo.customers.grid'
            );
        }

        return $this->blockGrid;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getCustomersJson()
    {
        $params = $this->getRequest()->getParams();
        $ruleId = $this->helperRule->getRuleId($params);

        if (empty($ruleId)) {
            return '{}';
        }

        $result      = [];
        $customerIds = $this->personalPromotionResourceModel->getCustomerIdsByRuleId($ruleId);
        foreach ($customerIds as $id) {
            $result[$id] = "0";
        }

        if (!empty($result)) {
            return $this->jsonEncoder->encode($result);
        }

        return '{}';
    }
}