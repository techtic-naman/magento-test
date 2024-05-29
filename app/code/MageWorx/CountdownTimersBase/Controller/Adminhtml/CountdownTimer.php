<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use MageWorx\CountdownTimersBase\Api\CountdownTimerRepositoryInterface;
use Psr\Log\LoggerInterface;

abstract class CountdownTimer extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MageWorx_CountdownTimersBase::countdown_timer';

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
     * @var CountdownTimerRepositoryInterface
     */
    protected $countdownTimerRepository;

    /**
     * CountdownTimer constructor.
     *
     * @param Context $context
     * @param ResultFactory $resultFactory
     * @param LoggerInterface $logger
     * @param CountdownTimerRepositoryInterface $countdownTimerRepository
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        LoggerInterface $logger,
        CountdownTimerRepositoryInterface $countdownTimerRepository
    ) {
        $this->resultFactory            = $resultFactory;
        $this->logger                   = $logger;
        $this->countdownTimerRepository = $countdownTimerRepository;

        parent::__construct($context);
    }
}
