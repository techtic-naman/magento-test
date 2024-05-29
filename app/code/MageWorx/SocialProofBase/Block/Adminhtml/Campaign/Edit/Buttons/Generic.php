<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Block\Adminhtml\Campaign\Edit\Buttons;

use MageWorx\SocialProofBase\Api\CampaignRepositoryInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\SocialProofBase\Api\Data\CampaignInterface;

class Generic
{
    /**
     * Widget Context
     *
     * @var Context
     */
    protected $context;

    /**
     * Campaign Repository
     *
     * @var CampaignRepositoryInterface
     */
    protected $campaignRepository;

    /**
     * constructor
     *
     * @param Context $context
     * @param CampaignRepositoryInterface $campaignRepository
     */
    public function __construct(
        Context $context,
        CampaignRepositoryInterface $campaignRepository
    ) {
        $this->context            = $context;
        $this->campaignRepository = $campaignRepository;
    }

    /**
     * Return Campaign ID
     *
     * @return string|null
     * @throws LocalizedException
     */
    public function getCampaignId(): ?string
    {
        try {
            $id = $this->context->getRequest()->getParam(CampaignInterface::CAMPAIGN_ID);

            return $this->campaignRepository->getById((int)$id)->getId();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return  string
     */
    public function getUrl($route = '', $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
