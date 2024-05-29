<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Source;

class EmailTemplates extends \Magento\Config\Model\Config\Source\Email\Template
{
    const BY_CONFIG_VALUE = '0';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        array_unshift($options, ['value' => self::BY_CONFIG_VALUE, 'label' => __('Use config')]);
        return $options;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return \MageWorx\RewardPoints\Helper\Data::EMAIL_TEMPLATE_FROM_RULES;
    }
}
