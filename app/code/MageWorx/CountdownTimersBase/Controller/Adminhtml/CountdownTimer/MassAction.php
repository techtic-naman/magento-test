<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\ResultFactory;
use MageWorx\CountdownTimersBase\Api\CountdownTimerRepositoryInterface;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Psr\Log\LoggerInterface;

abstract class MassAction extends \MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer
{
    /**
     * Mass Action filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * Countdown Timer collection factory
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Action success message
     *
     * @var string
     */
    protected $successMessage;

    /**
     * Action error message
     *
     * @var string
     */
    protected $errorMessage;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * MassAction constructor.
     *
     * @param Context $context
     * @param ResultFactory $resultFactory
     * @param LoggerInterface $logger
     * @param CountdownTimerRepositoryInterface $countdownTimerRepository
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ResourceConnection $resourceConnection
     * @param string $successMessage
     * @param string $errorMessage
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        LoggerInterface $logger,
        CountdownTimerRepositoryInterface $countdownTimerRepository,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ResourceConnection $resourceConnection,
        $successMessage,
        $errorMessage
    ) {
        parent::__construct($context, $resultFactory, $logger, $countdownTimerRepository);

        $this->filter             = $filter;
        $this->collectionFactory  = $collectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->successMessage     = $successMessage;
        $this->errorMessage       = $errorMessage;
    }

    /**
     * @param CountdownTimerInterface $countdownTimer
     * @return void
     */
    abstract protected function massAction(CountdownTimerInterface $countdownTimer): void;

    /**
     * Execute action
     *
     * @return ResultRedirect
     */
    public function execute(): ResultRedirect
    {
        try {
            $collection     = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();

            foreach ($collection as $countdownTimer) {
                $this->massAction($countdownTimer);
            }
            $this->messageManager->addSuccessMessage(__($this->successMessage, $collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->critical($e);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, $this->errorMessage);
            $this->logger->critical($e);
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('mageworx_countdowntimersbase/*/index');

        return $redirectResult;
    }
}
