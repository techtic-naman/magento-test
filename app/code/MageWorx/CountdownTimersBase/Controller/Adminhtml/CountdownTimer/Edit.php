<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\CountdownTimersBase\Api\CountdownTimerRepositoryInterface;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use Psr\Log\LoggerInterface;

class Edit extends \MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param ResultFactory $resultFactory
     * @param LoggerInterface $logger
     * @param CountdownTimerRepositoryInterface $countdownTimerRepository
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        LoggerInterface $logger,
        CountdownTimerRepositoryInterface $countdownTimerRepository,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context, $resultFactory, $logger, $countdownTimerRepository);

        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute(): ResultInterface
    {
        $countdownTimerId = $this->getRequest()->getParam(CountdownTimerInterface::COUNTDOWN_TIMER_ID);

        if ($countdownTimerId) {
            try {
                $countdownTimer = $this->countdownTimerRepository->getById($countdownTimerId);

                $this->dataPersistor->set('mageworx_countdowntimersbase_countdown_timer', $countdownTimer->getData());
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The Countdown Timer no longer exists.'));

                return $this->resultRedirectFactory->create()->setPath('mageworx_countdowntimersbase/*/');
            }
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find a Countdown Timer to edit.'));

            return $this->resultRedirectFactory->create()->setPath('mageworx_countdowntimersbase/*/');
        }

        /** @var ResultPage $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend(__('Countdown Timers'));
        $resultPage->getConfig()->getTitle()->prepend($countdownTimer->getName());

        return $resultPage;
    }
}
