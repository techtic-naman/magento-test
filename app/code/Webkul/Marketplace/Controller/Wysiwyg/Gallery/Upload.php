<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Marketplace\Controller\Wysiwyg\Gallery;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;
use Webkul\Marketplace\Api\Data\WysiwygImageInterfaceFactory;
use Webkul\Marketplace\Helper\Data as MpHelper;

/**
 * Marketplace Wysiwyg Image Upload controller.
 */
class Upload extends Action
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var Filesystem\Driver\File $file
     */
    private $file;

    /**
     * Media StorageFile Uploader factory.
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var WysiwygImageInterfaceFactory
     */
    protected $wysiwygImage;
    /**
     * @var MpHelper
     */
    protected $mpHelper;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * Initialization
     *
     * @param Context $context
     * @param Filesystem $filesystem
     * @param Filesystem\Driver\File $file
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param StoreManagerInterface $storeManager
     * @param WysiwygImageInterfaceFactory $wysiwygImage
     * @param MpHelper $mpHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        Filesystem\Driver\File $file,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        StoreManagerInterface $storeManager,
        WysiwygImageInterfaceFactory $wysiwygImage,
        MpHelper $mpHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(
            DirectoryList::MEDIA
        );
        $this->file = $file;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->storeManager = $storeManager;
        $this->wysiwygImage = $wysiwygImage;
        $this->mpHelper = $mpHelper;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * Upload image function
     *
     * @return void
     */
    public function execute()
    {
        $helper = $this->mpHelper;
        try {
            $target = $this->mediaDirectory->getAbsolutePath(
                'tmp/desc'
            );
            $fileUploader = $this->fileUploaderFactory->create(
                ['fileId' => 'image']
            );
            $fileUploader->setAllowedExtensions(
                ['gif', 'jpg', 'png', 'jpeg']
            );
            $fileUploader->setFilesDispersion(true);
            $fileUploader->setAllowRenameFiles(true);
            $resultData = $fileUploader->save($target);
            unset($resultData['tmp_name']);
            unset($resultData['path']);
            $resultData['url'] = $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . 'tmp/desc' . '/' . ltrim(str_replace('\\', '/', $resultData['file']), '/');
            $resultData['file'] = $resultData['file'] . '.tmp';

            $this->getResponse()->representJson(
                $this->jsonHelper->jsonEncode($resultData)
            );
        } catch (\Exception $e) {
            $helper->logDataInLogger(
                "Controller_Wysiwyg_Gallery_Upload execute : ".$e->getMessage()
            );
            $this->getResponse()->representJson(
                $this->jsonHelper->jsonEncode(
                    [
                        'error' => $e->getMessage(),
                        'errorcode' => $e->getCode(),
                    ]
                )
            );
        }
    }
}
