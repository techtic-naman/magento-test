<?php
namespace Productvideo\Core\Plugin;

use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface;
use Psr\Log\LoggerInterface;

class ImportProductPlugin
{
    protected $mediaGalleryManagement;
    protected $logger;

    public function __construct(
        ProductAttributeMediaGalleryManagementInterface $mediaGalleryManagement, 
        LoggerInterface $logger
    ) {
        $this->mediaGalleryManagement = $mediaGalleryManagement;
        $this->logger = $logger;
    }

    public function beforeSaveProductEntity(
        ImportProduct $subject,
        $entityRowsIn,
        $entityTypeCode
    ) {
        $this->logger->info('subject', ['subject' => json_encode($subject)]);
        $this->logger->info('entityTypeCode', ['entityTypeCode' => json_encode($entityTypeCode)]);
        $this->logger->info('Entering beforeSaveProductEntity method');
        if (empty($entityRowsIn)) {
            $this->logger->info('No entity rows found');
        } else {
            $this->logger->info('Entity rows received: ' . count($entityRowsIn));
        }

        foreach ($entityRowsIn as $row) {
            $this->logger->info('Processing row: ' . json_encode($row));

            if (isset($row['video_url'])) {
                try {
                    $this->logger->info('Video URL found for product ' . $row['sku'] . ': ' . $row['video_url']);
                    $this->mediaGalleryManagement->create($row['sku'], [
                        'video_url' => $row['video_url'],
                        'media_type' => 'external-video',
                        'video_title' => 'test',
                        'video_description' => 'test',
                        'video_metadata' => 'test1',
                        'video_provider' => 'vimeo'
                    ]);
                } catch (\Exception $e) {
                    $this->logger->error('Error adding video for product ' . $row['sku'] . ': ' . $e->getMessage());
                    throw $e; // Rethrow the exception to ensure it is not silently ignored
                }
            }
        }

        $this->logger->info('Exiting beforeSaveProductEntity method');

        return [$entityRowsIn, $entityTypeCode];
    }
}
