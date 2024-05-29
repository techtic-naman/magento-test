<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Walletsystem
 * @author    Webkul
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Walletsystem\Model\Attribute\Backend;

class WalletCashBack extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * Constructor
     *
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->productFactory = $productFactory;
    }

    /**
     * Before Save Product Page
     *
     * @param \Magento\Framework\DataObject $object
     *
     * @return $this
     */
    public function beforeSave($object)
    {
        $this->validateCashback($object);
        return parent::beforeSave($object);
    }

    /**
     * Validate Cashback
     *
     * @param object $object
     */
    public function validateCashback($object)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $walletCashBack = (int)$object->getData($attributeCode);
        $productPrice = (int)$object->getPrice();

        if ($object->getTypeId()=="bundle") {

            $product = clone $this->productFactory->create();
            foreach ($object['bundle_selections_data'] as $productData) {
                foreach ($productData as $selected) {

                    $productPriceById = $product->load($selected['product_id'])->getPrice();

                    if ($walletCashBack > (float)$productPriceById) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('The cashback amount must be less than or equal to associate product amount.')
                        );
                    }
                }
            }
            
            return true;
        }

        if ($object->getTypeId()=="configurable") {
            return true;
        }
 
        if ($walletCashBack > $productPrice) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The cashback amount must be less than or equal to product amount.')
            );
        }

        return true;
    }
}
