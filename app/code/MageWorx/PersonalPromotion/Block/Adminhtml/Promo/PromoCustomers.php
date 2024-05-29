<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PersonalPromotion\Block\Adminhtml\Promo;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollection;
use MageWorx\PersonalPromotion\Model\ResourceModel\PersonalPromotion as PersonalPromotionResourceModel;
use MageWorx\PersonalPromotion\Helper\Data as HelperData;

class PromoCustomers extends CustomersTemplate
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'promo/customers.phtml';

}