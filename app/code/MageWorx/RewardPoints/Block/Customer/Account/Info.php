<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Customer\Account;

class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \MageWorx\RewardPoints\Model\CustomerBalance
     */
    protected $customerBalance;

    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface
     */
    protected $customerBalanceRepository;

    /**
     * Info constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        array $data = []
    ) {
        $this->helperData                = $helperData;
        $this->customerSession           = $customerSession;
        $this->customerBalanceRepository = $customerBalanceRepository;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (!$this->getCustomerBalance()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * @return false|null|\MageWorx\RewardPoints\Model\CustomerBalance
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerBalance()
    {
        if ($this->customerBalance === null) {

            $customer = $this->customerSession->getCustomer();

            if ($customer && $customer->getId()) {

                $customerBalance       = $this->customerBalanceRepository->getByCustomer(
                    $customer->getId(),
                    $this->_storeManager->getWebsite()->getId()
                );
                $this->customerBalance = $customerBalance->getId() ? $customerBalance : false;
            } else {
                $this->customerBalance = false;
            }
        }

        return $this->customerBalance;
    }

    /**
     * @return bool
     */
    public function getIsMageWorxAction()
    {
        return ($this->getRequest()->getModuleName() == 'rewardpoints');
    }

    /**
     * @return \Magento\Framework\Phrase|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getExpirationDateMessage()
    {
        $expirationDate = $this->getCustomerBalance()->getExpirationDate();

        if ($expirationDate && $this->helperData->isEnableExpirationDate($this->getCustomerBalance()->getWebsiteId())) {
            $date = new \DateTime($expirationDate);
            return __('Expiration date is %1.', $this->formatDate($date));
        }

        return '';
    }
}
