<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\RewardPointsImportExport\Controller\Adminhtml\RewardPoints;

use Magento\Framework\Controller\ResultFactory;

class ImportPost extends \MageWorx\RewardPointsImportExport\Controller\Adminhtml\RewardPoints
{
    /**
     * @var \MageWorx\RewardPointsImportExport\Model\Balance\CsvImportHandler
     */
    protected $csvImportHandler;

    /**
     * ImportPost constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \MageWorx\RewardPointsImportExport\Model\Balance\CsvImportHandler $csvImportHandler
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \MageWorx\RewardPointsImportExport\Model\Balance\CsvImportHandler $csvImportHandler
    ) {
        $this->csvImportHandler = $csvImportHandler;
        parent::__construct($context, $fileFactory);
    }


    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {

            $file = $this->getRequest()->getFiles('import_rewardpoints_balance_file');

            if ($file && !empty($file['tmp_name'])) {

                try {
                    $this->csvImportHandler->importFromCsvFile($file);
                    $this->messageManager->addSuccessMessage(__('The reward points balance has been imported.'));
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->addInvalidFileMessage();
                }

            } else {
                $this->addInvalidFileMessage();
            }

        } else {
            $this->addInvalidFileMessage();
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
        return $resultRedirect;
    }

    /**
     * @return void
     */
    protected function addInvalidFileMessage()
    {
        $this->messageManager->addErrorMessage(__('Invalid file upload attempt'));
    }
}
