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
namespace Webkul\Marketplace\Model\Order\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status is used tp get the order available status
 */
class Status implements OptionSourceInterface
{
    /**
     * @var \Webkul\Marketplace\Model\Orders
     */
    protected $marketplaceOrder;

   /**
    * Construct
    *
    * @param \Webkul\Marketplace\Model\Orders $marketplaceOrder
    */
    public function __construct(\Webkul\Marketplace\Model\Orders $marketplaceOrder)
    {
        $this->marketplaceOrder = $marketplaceOrder;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->marketplaceOrder->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
