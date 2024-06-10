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

namespace Webkul\Marketplace\Controller\Product\Gallery;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList as FilesystemDirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\File\Uploader;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\HTTP\Adapter\Curl;
use Magento\MediaStorage\Model\ResourceModel\File\Storage\File;

/**
 * Marketplace Product RetrieveImage controller.
 */
class RetrieveImage extends Action
{
    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var Config
     */
    protected $_mediaConfig;

    /**
     * @var Filesystem
     */
    protected $_fileSystem;

    /**
     * @var Filesystem\Io\File
     */
    protected $_fileSystemIoFile;

    /**
     * @var Filesystem\Driver\File
     */
    protected $file;

    /**
     * @var AbstractAdapter
     */
    protected $_imageAdapter;

    /**
     * @var Curl
     */
    protected $_curl;

    /**
     * @var File
     */
    protected $_fileUtility;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $mpHelper;

    /**
     * Construct
     *
     * @param Context $context
     * @param RawFactory $resultRawFactory
     * @param Config $mediaConfig
     * @param Filesystem $fileSystem
     * @param Filesystem\Io\File $fileSystemIoFile
     * @param Filesystem\Driver\File $file
     * @param AdapterFactory $imageAdapterFactory
     * @param Curl $curl
     * @param File $fileUtility
     * @param \Webkul\Marketplace\Helper\Data $mpHelper
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        Config $mediaConfig,
        Filesystem $fileSystem,
        Filesystem\Io\File $fileSystemIoFile,
        Filesystem\Driver\File $file,
        AdapterFactory $imageAdapterFactory,
        Curl $curl,
        File $fileUtility,
        \Webkul\Marketplace\Helper\Data $mpHelper
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->_mediaConfig = $mediaConfig;
        $this->_fileSystem = $fileSystem;
        $this->_fileSystemIoFile = $fileSystemIoFile;
        $this->file = $file;
        $this->_imageAdapter = $imageAdapterFactory->create();
        $this->_curl = $curl;
        $this->_fileUtility = $fileUtility;
        $this->mpHelper = $mpHelper;
    }

    /**
     * Save remote image data
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $baseTmpMediaPath = $this->_mediaConfig->getBaseTmpMediaPath();
        try {
            $remoteImageUrl = $this->getRequest()->getParam('remote_image');
            $fileInfo = $this->_fileSystemIoFile->getPathInfo($remoteImageUrl);
            $baseFileName = $fileInfo['basename'];
            $localFileName = Uploader::getCorrectFileName($baseFileName);
            $localTmpFileName = Uploader::getDispretionPath($localFileName).DIRECTORY_SEPARATOR.$localFileName;
            $localFileMediaPath = $baseTmpMediaPath.($localTmpFileName);
            $localUniqueFileMediaPath = $this->getNewFileName($localFileMediaPath);
            $this->saveRemoteImage($remoteImageUrl, $localUniqueFileMediaPath);
            $localFileFullPath = $this->getDestinationFileAbsolutePath($localUniqueFileMediaPath);
            $this->_imageAdapter->validateUploadFile($localFileFullPath);
            $result = $this->appendResultSaveRemoteImage($localUniqueFileMediaPath);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $encodedParms = $this->mpHelper->arrayToJson($result);
        $response->setContents($encodedParms);

        return $response;
    }

    /**
     * Get image dataa
     *
     * @param string $fileName
     *
     * @return mixed
     */
    protected function appendResultSaveRemoteImage($fileName)
    {
        $fileInfo = $this->_fileSystemIoFile->getPathInfo($fileName);
        $tmpFileName = Uploader::getDispretionPath($fileInfo['basename']).DIRECTORY_SEPARATOR.$fileInfo['basename'];
        $result['name'] = $fileInfo['basename'];
        $result['type'] = $this->_imageAdapter->getMimeType();
        $result['error'] = 0;
        try {
            $result['size'] = strlen($this->file->fileGetContents($fileName));
        } catch (\Exception $e) {
            $result['size'] = '';
        }
        $result['url'] = $this->_mediaConfig->getTmpMediaUrl($tmpFileName);
        $result['file'] = $tmpFileName;

        return $result;
    }

    /**
     * Save image
     *
     * @param string $fileUrl
     * @param string $localFilePath
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function saveRemoteImage($fileUrl, $localFilePath)
    {
        $this->_curl->setConfig(['header' => false]);
        $this->_curl->write('GET', $fileUrl);
        $image = $this->_curl->read();
        if (empty($image)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Could not get preview image information. Please check your connection and try again.')
            );
        }
        $this->_fileUtility->saveFile($localFilePath, $image);
    }

    /**
     * Get new file name
     *
     * @param string $localFilePath
     *
     * @return string
     */
    protected function getNewFileName($localFilePath)
    {
        $destinationFile = $this->getDestinationFileAbsolutePath($localFilePath);
        $fileName = Uploader::getNewFileName($destinationFile);
        $fileInfo = $this->_fileSystemIoFile->getPathInfo($localFilePath);

        return $fileInfo['dirname'].DIRECTORY_SEPARATOR.$fileName;
    }

    /**
     * Get abosulte path of destination fileName
     *
     * @param string $localTmpFile
     *
     * @return string
     */
    protected function getDestinationFileAbsolutePath($localTmpFile)
    {
        /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
        $mediaDirectory = $this->_fileSystem->getDirectoryRead(FilesystemDirectoryList::MEDIA);
        $pathToSave = $mediaDirectory->getAbsolutePath();

        return $pathToSave.$localTmpFile;
    }
}
