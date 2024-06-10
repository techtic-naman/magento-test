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
namespace Webkul\Marketplace\Model\Seller\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status is used tp get the seller available status
 */
class Status implements OptionSourceInterface
{
    /**
     * @var \Webkul\Marketplace\Model\Seller
     */
    protected $marketplaceSeller;

    /**
     * Construct
     *
     * @param \Webkul\Marketplace\Model\Seller $marketplaceSeller
     */
    public function __construct(\Webkul\Marketplace\Model\Seller $marketplaceSeller)
    {
        $this->marketplaceSeller = $marketplaceSeller;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->marketplaceSeller->getAvailableStatuses();
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
