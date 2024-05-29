<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use MageWorx\SocialProofBase\Api\CampaignRepositoryInterface;
use Psr\Log\LoggerInterface;

abstract class Campaign extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MageWorx_SocialProofBase::campaign';

    /**
     * Result factory
     *
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CampaignRepositoryInterface
     */
    protected $campaignRepository;

    /**
     * Campaign constructor.
     *
     * @param Context $context
     * @param ResultFactory $resultFactory
     * @param LoggerInterface $logger
     * @param CampaignRepositoryInterface $campaignRepository
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        LoggerInterface $logger,
        CampaignRepositoryInterface $campaignRepository
    ) {
        $this->resultFactory      = $resultFactory;
        $this->logger             = $logger;
        $this->campaignRepository = $campaignRepository;

        parent::__construct($context);
    }
}
