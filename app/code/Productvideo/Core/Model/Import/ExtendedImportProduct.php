<?php
namespace Productvideo\Core\Model\Import;

use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Psr\Log\LoggerInterface;

class ExtendedImportProduct extends ImportProduct
{
    protected $logger;

    public function __construct(
        LoggerInterface $logger,
        
    ) {
        $this->logger = $logger;
        parent::__construct(
           
        );
    }

    public function saveProductEntity(array $entityRowsIn, $entityTypeCode)
    {
        // Call the parent method to save the product data
        parent::saveProductEntity($entityRowsIn, $entityTypeCode);
        $this->logger->info('entity row: ' . json_encode($entityRowsIn));

        // Process additional data
        foreach ($entityRowsIn as $row) {
            if (isset($row['additional_data'])) {
                // Process the additional data
                $this->processAdditionalData($row['additional_data']);
            }
        }
    }

    protected function processAdditionalData($additionalData)
    {
        // Add your code here to process the additional data
    }
}