<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Controller\Adminhtml\Campaign;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use MageWorx\SocialProofBase\Api\CampaignRepositoryInterface;
use MageWorx\SocialProofBase\Model\ResourceModel\Campaign\Template\Collection;
use MageWorx\SocialProofBase\Model\ResourceModel\Campaign\Template\CollectionFactory;
use MageWorx\SocialProofBase\Model\Campaign\Template as CampaignTemplate;
use Psr\Log\LoggerInterface;

class GetTemplateHtml extends \MageWorx\SocialProofBase\Controller\Adminhtml\Campaign
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * GetTemplateHtml constructor.
     *
     * @param Context $context
     * @param ResultFactory $resultFactory
     * @param LoggerInterface $logger
     * @param CampaignRepositoryInterface $campaignRepository
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        LoggerInterface $logger,
        CampaignRepositoryInterface $campaignRepository,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $resultFactory, $logger, $campaignRepository);

        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $identifier = $this->getRequest()->getParam(CampaignTemplate::IDENTIFIER);
        $data       = [];

        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $collection
            ->addFieldToSelect(CampaignTemplate::CONTENT)
            ->addFieldToFilter(CampaignTemplate::IDENTIFIER, [$identifier]);

        $collectionData = $collection->getData();

        if (empty($collectionData)) {
            $data['error'] = ['This template is missing!'];
        } else {
            $data['content'] = $collectionData[0][CampaignTemplate::CONTENT];
        }

        $resultJson->setData($data);

        return $resultJson;
    }
}
