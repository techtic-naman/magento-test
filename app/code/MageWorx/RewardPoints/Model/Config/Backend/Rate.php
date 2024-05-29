<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Config\Backend;

class Rate extends \Magento\Framework\App\Config\Value
{
    /**
     * @return $this
     * @throws \Exception
     */
    public function beforeSave()
    {
        $value = $this->getValue();

        if (!is_numeric($value)) {
            $this->initError();
        }

        // For formats such as '.5', '00.5'
        if ((string)$value !== (string)(double)$value) {
            $this->initError();
        }

        if ($value <= 0) {
            $this->initError();
        }

        return $this;
    }

    /**
     * Full message: Something went wrong while saving this configuration: the points exchange rate has incorrect format.
     *
     * @throws \Exception
     */
    protected function initError()
    {
        throw new \Magento\Framework\Exception\LocalizedException(__('the points exchange rate has incorrect format.'));
    }
}
