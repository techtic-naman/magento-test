<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\RewardPointsImportExport\Controller\Adminhtml\RewardPoints;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Component\ComponentRegistrar;

class ExportExamplePost extends \MageWorx\RewardPointsImportExport\Controller\Adminhtml\RewardPoints
{
    /**
     * @var ComponentRegistrar
     */
    protected $componentRegistrar;

    /**
     * ExportExamplePost constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param ComponentRegistrar $componentRegistrar
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        ComponentRegistrar $componentRegistrar
    ) {
        parent::__construct($context, $fileFactory);
        $this->componentRegistrar = $componentRegistrar;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $relativeFilePath = implode(
            DIRECTORY_SEPARATOR,
            [
                'examples',
                'example_export.csv'
            ]
        );
        $path = $this->componentRegistrar->getPath(
            ComponentRegistrar::MODULE, 'MageWorx_RewardPointsImportExport'
        );
        $file = $path .
            DIRECTORY_SEPARATOR .
            $relativeFilePath;

        $content = file_get_contents($file);

        return $this->fileFactory->create(
            'rewardpoints_example.csv',
            $content,
            DirectoryList::VAR_DIR
        );
    }
}
