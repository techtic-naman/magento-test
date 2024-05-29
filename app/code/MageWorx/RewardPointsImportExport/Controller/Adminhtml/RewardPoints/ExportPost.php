<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\RewardPointsImportExport\Controller\Adminhtml\RewardPoints;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;

class ExportPost extends \MageWorx\RewardPointsImportExport\Controller\Adminhtml\RewardPoints
{
    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\CollectionFactory
     */
    protected $customerBalanceCollectionFactory;

    /**
     * ExportPost constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\CollectionFactory $balanceCollectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\CollectionFactory $balanceCollectionFactory
    ) {
        $this->customerBalanceCollectionFactory = $balanceCollectionFactory;

        parent::__construct($context, $fileFactory);
    }

    /**
     * Export action from export reward points
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        /** start csv content and set template */
        $headers = new \Magento\Framework\DataObject(
            [
                'website_code'      => __('Website Code'),
                'customer_email'    => __('Customer Email'),
                'points'            => __('Points'),
                'expiration_date'   => __('Expiration Date')
            ]
        );
        $template = '"{{website_code}}","{{customer_email}}","{{points}}","{{expiration_date}}"';
        $content = $headers->toString($template);

        $content .= "\n";

        $collection = $this->getCustomerBalanceCollection();

        while ($customerBalance = $collection->fetchItem()) {
            $content .= $customerBalance->toString($template) . "\n";
        }
        return $this->fileFactory->create('export_rewardpoints_balance_file.csv', $content, DirectoryList::VAR_DIR);
    }

    /**
     * @return \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\Collection
     */
    protected function getCustomerBalanceCollection()
    {
        /** @var \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\Collection $customerBalanceCollection */
        $customerBalanceCollection = $this->customerBalanceCollectionFactory->create();

        $customerBalanceCollection->joinCustomerTable();
        $customerBalanceCollection->joinWebsiteTable();
        $customerBalanceCollection->getSelect()->order(['website_id asc', 'email asc']);

        return $customerBalanceCollection;
    }
}
