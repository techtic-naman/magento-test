<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Controller\Ajax;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\OrderItemInterface;
use MageWorx\SocialProofBase\Api\FrontendCampaignResolverInterface;
use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use MageWorx\SocialProofBase\Model\ConverterFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class GetCampaignData extends \Magento\Framework\App\Action\Action
{
    /**
     * @var ResultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var FrontendCampaignResolverInterface
     */
    protected $frontendCampaignResolver;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ConverterFactory
     */
    protected $converterFactory;

    /**
     * GetCampaignData constructor.
     *
     * @param Context $context
     * @param ResultJsonFactory $resultJsonFactory
     * @param FrontendCampaignResolverInterface $frontendCampaignResolver
     * @param StoreManagerInterface $storeManager
     * @param CustomerSession $customerSession
     * @param LoggerInterface $logger
     * @param ConverterFactory $converterFactory
     */
    public function __construct(
        Context $context,
        ResultJsonFactory $resultJsonFactory,
        FrontendCampaignResolverInterface $frontendCampaignResolver,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        LoggerInterface $logger,
        ConverterFactory $converterFactory
    ) {
        parent::__construct($context);

        $this->resultJsonFactory        = $resultJsonFactory;
        $this->frontendCampaignResolver = $frontendCampaignResolver;
        $this->storeManager             = $storeManager;
        $this->customerSession          = $customerSession;
        $this->logger                   = $logger;
        $this->converterFactory         = $converterFactory;
    }

    /**
     * @return ResultJson|null
     */
    public function execute(): ?ResultJson
    {
        if ($this->getRequest()->isAjax()) {
            $result      = $this->resultJsonFactory->create();
            $data        = $this->getRequest()->getParams();
            $campaignIds = !empty($data['campaignId']) ? [(int)$data['campaignId']] : null;
            $itemIds     = !empty($data['itemIds']) ? (array)$data['itemIds'] : null;

            try {
                $campaign = $this->frontendCampaignResolver->getCampaign(
                    $data['displayMode'],
                    $data['pageType'],
                    $data['associatedEntityId'],
                    $this->storeManager->getStore()->getId(),
                    $this->customerSession->getCustomerGroupId(),
                    $campaignIds,
                    false,
                    $itemIds
                );

                if (!$campaign) {
                    throw new NoSuchEntityException(__('Requested Campaign doesn\'t exist'));
                }

                $arrayResult = [
                    'success'        => true,
                    'campaignId'     => $campaign->getData(CampaignInterface::CAMPAIGN_ID),
                    'content'        => $this->getPreparedContent($campaign),
                    'position'       => $campaign->getData(CampaignInterface::POSITION),
                    'removeVerified' => $campaign->getData(CampaignInterface::REMOVE_VERIFIED),
                    'startDelay'     => $campaign->getData(CampaignInterface::START_DELAY),
                    'autoClose'      => $campaign->getData(CampaignInterface::AUTO_CLOSE_IN),
                    'maxNumber'      => $campaign->getData(CampaignInterface::MAX_NUMBER_PER_PAGE)
                ];

                $orderItem = $campaign->getData('orderItem');

                if ($orderItem) {
                    $arrayResult['itemId'] = $orderItem->getData(OrderItemInterface::PRODUCT_ID);
                }
            } catch (NoSuchEntityException $e) {
                $arrayResult = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $arrayResult = [
                    'message' => __('There was an error loading Campaign data.')
                ];
                $result->setHttpResponseCode(500);
            }

            return $result->setData($arrayResult);
        }

        return null;
    }

    /**
     * @param DataObject $campaign
     * @return string
     */
    protected function getPreparedContent($campaign): string
    {
        $converter = $this->converterFactory->create($campaign->getData(CampaignInterface::EVENT_TYPE));

        return $converter->convert($campaign);
    }
}
