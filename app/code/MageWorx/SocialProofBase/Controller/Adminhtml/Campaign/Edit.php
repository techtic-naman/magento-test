<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Controller\Adminhtml\Campaign;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\SocialProofBase\Api\CampaignRepositoryInterface;
use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use Psr\Log\LoggerInterface;

class Edit extends \MageWorx\SocialProofBase\Controller\Adminhtml\Campaign
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
     * @param CampaignRepositoryInterface $campaignRepository
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        LoggerInterface $logger,
        CampaignRepositoryInterface $campaignRepository,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context, $resultFactory, $logger, $campaignRepository);

        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute(): ResultInterface
    {
        $campaignId = $this->getRequest()->getParam(CampaignInterface::CAMPAIGN_ID);

        if ($campaignId) {
            try {
                $campaign = $this->campaignRepository->getById($campaignId);

                $this->dataPersistor->set('mageworx_socialproofbase_campaign', $campaign->getData());
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The Campaign no longer exists.'));

                return $this->resultRedirectFactory->create()->setPath('mageworx_socialproofbase/*/');
            }
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find a Campaign to delete.'));

            return $this->resultRedirectFactory->create()->setPath('mageworx_socialproofbase/*/');
        }

        /** @var ResultPage $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend(__('Social proof'));
        $resultPage->getConfig()->getTitle()->prepend($campaign->getName());

        return $resultPage;
    }
}
