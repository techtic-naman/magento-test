<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PersonalPromotion\Block\Adminhtml\Promo\Tab\Customers\Renderer;

use Magento\Directory\Model\CountryInformationAcquirer;
use Magento\Framework\Exception\NoSuchEntityException;

class Country extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var CountryInformationAcquirer
     */
    protected $countryInformationAcquirer;

    /**
     * Country constructor.
     *
     * @param \Magento\Backend\Block\Context $context
     * @param CountryInformationAcquirer $countryInformationAcquirer
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        CountryInformationAcquirer $countryInformationAcquirer,
        array $data = []
    ) {
        $this->countryInformationAcquirer = $countryInformationAcquirer;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $countryCode = $row->getCountryCode();
        if (isset($countryCode)) {
            return $this->getCountryNameById($countryCode);
        }

        return '';
    }

    /**
     * Get country name by country id
     *
     * @param int $id
     * @return mixed|null|string
     */
    protected function getCountryNameById($id)
    {
        try {
            $countryInfo = $this->countryInformationAcquirer->getCountryInfo($id);

            return $countryInfo->getFullNameLocale();
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }

}
