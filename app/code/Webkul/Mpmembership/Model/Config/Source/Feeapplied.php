<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Mpmembership
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Mpmembership\Model\Config\Source;

/**
 * Used in creating options for getting fees applied for vendor or products
 */
class Feeapplied
{
    public const PER_VENDOR = 0;

    public const PER_PRODUCT = 1;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data =  [
            ['value' => '0', 'label' => __('Vendor')],
            ['value' => '1', 'label' => __('Product')]
        ];
        return $data;
    }
}
