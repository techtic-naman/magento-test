<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Controller\Adminhtml\ReviewSummary;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\ReviewAIBase\Api\ReviewSummaryGeneratorInterface;
use Psr\Log\LoggerInterface;

class AddProductToQueue extends Action
{
    protected ReviewSummaryGeneratorInterface $reviewSummaryGenerator;
    protected LoggerInterface                 $logger;
    protected Json                            $jsonSerializer;
    protected AppEmulation                    $appEmulation;
    protected StoreManagerInterface           $storeManager;

    /**
     * @param Context $context
     * @param ReviewSummaryGeneratorInterface $reviewSummaryGenerator
     * @param Json $jsonSerializer
     * @param AppEmulation $appEmulation
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context                         $context,
        ReviewSummaryGeneratorInterface $reviewSummaryGenerator,
        Json                            $jsonSerializer,
        AppEmulation                    $appEmulation,
        StoreManagerInterface           $storeManager,
        LoggerInterface                 $logger
    ) {
        $this->reviewSummaryGenerator = $reviewSummaryGenerator;

        $this->jsonSerializer = $jsonSerializer;
        $this->appEmulation   = $appEmulation;
        $this->storeManager   = $storeManager;
        $this->logger         = $logger;
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultObject */
        $resultObject = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $productId = (int)$this->getRequest()->getParam('product_id');
        $storeId   = (int)$this->getRequest()->getParam('store_id') ?? 0;

        try {
            $result = $this->reviewSummaryGenerator->addToQueue(
                $productId,
                $storeId
            );

            $queueItemIds = [];
            foreach ($result as $queueItem) {
                $queueItemIds[] = $queueItem->getId();
            }
            $resultJson = $this->jsonSerializer->serialize(['added_to_queue' => true, 'queue_items' => $queueItemIds]);
        } catch (LocalizedException $exception) {
            $resultObject->setJsonData(
                $this->jsonSerializer->serialize(
                    ['error' => true, 'message' => $exception->getLogMessage()]
                )
            );

            return $resultObject;
        }

        $resultObject->setJsonData($resultJson);

        return $resultObject;
    }
}
