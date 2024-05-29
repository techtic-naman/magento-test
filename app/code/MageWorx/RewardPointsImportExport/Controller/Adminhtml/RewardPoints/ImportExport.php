<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\RewardPointsImportExport\Controller\Adminhtml\RewardPoints;

use Magento\Framework\Controller\ResultFactory;

class ImportExport extends \MageWorx\RewardPointsImportExport\Controller\Adminhtml\RewardPoints
{
    /**
     * Import and export Page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->messageManager->addNoticeMessage(
            $this->_objectManager->get(\Magento\ImportExport\Helper\Data::class)->getMaxUploadSizeMessage()
        );

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->setActiveMenu('MageWorx_RewardPointsImportExport::system_convert_points');
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock(
                \MageWorx\RewardPointsImportExport\Block\Adminhtml\Balance\ImportExportHeader::class
            )
        );
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock(
                \MageWorx\RewardPointsImportExport\Block\Adminhtml\Balance\ImportExport::class
            )
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Reward Points Customer Balance'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import and Export Reward Points Balance'));
        return $resultPage;
    }
}
