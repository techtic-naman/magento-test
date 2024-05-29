<?php

namespace Meetanshi\Bulksms\Model\Config\Backend;

use Magento\Framework\File\Csv;
use Magento\Config\Model\Config\Backend\File;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\App\Filesystem\DirectoryList;
use Meetanshi\Bulksms\Model\BulksmsFactory;

class Import extends File
{
    protected $csv;
    protected $bulksmsFactory;


    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        UploaderFactory $uploaderFactory,
        RequestDataInterface $requestData,
        Filesystem $filesystem,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        Csv $csv,
        BulksmsFactory $bulksmsFactory,
        array $data = []
    ) {
        $this->_uploaderFactory = $uploaderFactory;
        $this->_requestData = $requestData;
        $this->_filesystem = $filesystem;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->csv = $csv;
        $this->bulksmsFactory = $bulksmsFactory;
        parent::__construct($context, $registry, $config, $cacheTypeList, $uploaderFactory, $requestData, $filesystem, $resource, $resourceCollection, $data);
    }

    public function getAllowedExtensions()
    {
        return ['csv'];
    }

    public function beforeSave()
    {
        $file = $this->getFileData();
        if (!empty($file)) {
            try {
                if (!isset($file['name'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
                } else {
                    if (!(strpos(strtolower($file['name']), '.csv') !== false)) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file type. only import csv file'));
                    }
                }
                $csvData = $this->csv->getData($file['tmp_name']);
                foreach ($csvData as $row => $data) {
                    if ($row > 0) {

                        if (sizeof($data) == 2) {
                            if ($data[0] != '' && $data[1] != '') {
                                $bulksms = $this->bulksmsFactory->create();
                                $bulksms->setMobilenumber($data[1])
                                    ->setName($data[0])
                                    ->save();
                            }
                        }
                    }
                }

            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__('%1', $e->getMessage()));
            }
        }
        $this->setValue('');
        return $this;
    }
}
