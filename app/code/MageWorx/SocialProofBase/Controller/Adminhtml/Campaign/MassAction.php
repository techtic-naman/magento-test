<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Controller\Adminhtml\Campaign;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\ResultFactory;
use MageWorx\SocialProofBase\Api\CampaignRepositoryInterface;
use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use MageWorx\SocialProofBase\Model\ResourceModel\Campaign\CollectionFactory as CampaignCollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Psr\Log\LoggerInterface;

abstract class MassAction extends \MageWorx\SocialProofBase\Controller\Adminhtml\Campaign
{
    /**
     * Mass Action filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * Campaign collection factory
     *
     * @var CampaignCollectionFactory
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
     * @param CampaignRepositoryInterface $campaignRepository
     * @param Filter $filter
     * @param CampaignCollectionFactory $collectionFactory
     * @param ResourceConnection $resourceConnection
     * @param string $successMessage
     * @param string $errorMessage
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        LoggerInterface $logger,
        CampaignRepositoryInterface $campaignRepository,
        Filter $filter,
        CampaignCollectionFactory $collectionFactory,
        ResourceConnection $resourceConnection,
        $successMessage,
        $errorMessage
    ) {
        parent::__construct($context, $resultFactory, $logger, $campaignRepository);

        $this->filter             = $filter;
        $this->collectionFactory  = $collectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->successMessage     = $successMessage;
        $this->errorMessage       = $errorMessage;
    }

    /**
     * @param CampaignInterface $campaign
     * @return void
     */
    abstract protected function massAction(CampaignInterface $campaign): void;

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

            foreach ($collection as $campaign) {
                $this->massAction($campaign);
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
        $redirectResult->setPath('mageworx_socialproofbase/*/index');

        return $redirectResult;
    }
}
