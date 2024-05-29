<?php

namespace MageWorx\RewardPoints\Controller;

use Magento\Framework\App\RequestInterface;

abstract class Customer extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $currentCustomer;

    /**
     * Customer constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \Magento\Framework\Registry $registry
    ) {
        $this->customerSession = $customerSession;
        $this->currentCustomer = $customerSession->getCustomer();
        $this->helperData      = $helperData;
        $this->registry        = $registry;
        parent::__construct($context);
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        if (!$this->helperData->isEnable()) {
            throw new \Magento\Framework\Exception\NotFoundException(__('Access Denied'));
        }

        return parent::dispatch($request);
    }
}