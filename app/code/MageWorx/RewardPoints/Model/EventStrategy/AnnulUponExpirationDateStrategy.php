<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\EventStrategy;

use MageWorx\RewardPoints\Model\CustomerBalanceRepository;

class AnnulUponExpirationDateStrategy extends \MageWorx\RewardPoints\Model\AbstractEventStrategy
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $date;

    /**
     * @var \MageWorx\RewardPoints\Model\CustomerBalanceRepository
     */
    protected $customerBalanceRepository;

    /**
     * AnnulUponExpirationDateStrategy constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param CustomerBalanceRepository $customerBalanceRepository
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \MageWorx\RewardPoints\Model\CustomerBalanceRepository $customerBalanceRepository,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        array $data = []
    ) {
        $this->date = $date;
        $this->customerBalanceRepository = $customerBalanceRepository;
        parent::__construct($helperData, $data);
    }

    /**
     * @param array $eventData
     * @param string|null $comment
     * @return \Magento\Framework\Phrase|string
     */
    public function getMessage(array $eventData, $comment = null)
    {
        return __('Annul upon the expiration date.');
    }

    /**
     * {@inheritdoc}
     */
    public function getPoints()
    {
        $customerId = $this->getEntity()->getId();
        $websiteId  = $this->getEntity()->getWebsiteId();

        $customerBalance = $this->customerBalanceRepository->getByCustomer($customerId, $websiteId);

        return -$customerBalance->getPoints();
    }

    /**
     * @return array
     */
    public function getEventData()
    {
        return ['annul_datetime'=> $this->date->date()->format('Y-m-d H:i:s')];
    }

    /**
     * @return bool
     */
    public function getIsPossibleSendNotification()
    {
        return false;
    }

    /**
     * @return int|false|null null - expiration period won't be changed, false - expiration period is infinity
     */
    public function getExpirationPeriod()
    {
        return false;
    }
}