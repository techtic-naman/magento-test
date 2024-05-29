<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use MageWorx\CountdownTimersBase\Api\CountdownTimerRepositoryInterface;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\Template\Collection;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\Template\CollectionFactory;
use MageWorx\CountdownTimersBase\Model\CountdownTimer\Template as CountdownTimerTemplate;
use Psr\Log\LoggerInterface;

class GetTemplateData extends \MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * GetTemplateData constructor.
     *
     * @param Context $context
     * @param ResultFactory $resultFactory
     * @param LoggerInterface $logger
     * @param CountdownTimerRepositoryInterface $countdownTimerRepository
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        LoggerInterface $logger,
        CountdownTimerRepositoryInterface $countdownTimerRepository,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $resultFactory, $logger, $countdownTimerRepository);

        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $identifier = $this->getRequest()->getParam(CountdownTimerTemplate::IDENTIFIER);
        $data       = [];

        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $collection
            ->addFieldToSelect(CountdownTimerTemplate::THEME)
            ->addFieldToSelect(CountdownTimerTemplate::ACCENT)
            ->addFieldToFilter(CountdownTimerTemplate::IDENTIFIER, [$identifier]);

        $collectionData = $collection->getData();

        if (empty($collectionData)) {
            $data['error'] = ['This template is missing!'];
        } else {
            $data['theme']  = $collectionData[0][CountdownTimerTemplate::THEME];
            $data['accent'] = $collectionData[0][CountdownTimerTemplate::ACCENT];
        }

        $resultJson->setData($data);

        return $resultJson;
    }
}
