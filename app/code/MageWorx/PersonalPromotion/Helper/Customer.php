<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PersonalPromotion\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\Session;

class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * Customer constructor.
     *
     * @param Context $context
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        Session $customerSession
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * @return null
     */
    public function getCurrentCustomerId()
    {
        $customerId = null;

        if ($this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomer()->getId();
        }

        if ($customerId === null) {
            $objectManager   = \Magento\Framework\App\ObjectManager::getInstance();
            $customerSession = $objectManager->create('Magento\Customer\Model\Session');
            $customerId      = $customerSession->getCustomer()->getId();
        }

        if ($customerId === null) {
            $customerId = $objectManager->get('Magento\Backend\Model\Session\Quote')->getQuote()->getData('customer_id');
        }

        return $customerId;
    }

}
