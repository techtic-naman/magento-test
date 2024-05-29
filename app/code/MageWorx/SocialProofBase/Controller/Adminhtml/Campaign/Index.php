<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Controller\Adminhtml\Campaign;

use Magento\Backend\Model\View\Result\Page as ResultPage;
use Magento\Framework\Controller\ResultFactory;

class Index extends \MageWorx\SocialProofBase\Controller\Adminhtml\Campaign
{
    /**
     * @return ResultPage
     */
    public function execute(): ResultPage
    {
        /** @var ResultPage $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend(__('Social proof'));

        return $resultPage;
    }
}
