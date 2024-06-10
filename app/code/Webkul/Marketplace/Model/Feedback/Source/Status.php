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
namespace Webkul\Marketplace\Model\Feedback\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status is used tp get the Feedback available status
 */
class Status implements OptionSourceInterface
{
    /**
     * @var \Webkul\Marketplace\Model\Feedback
     */
    protected $marketplaceFeedback;

    /**
     * Construct
     *
     * @param \Webkul\Marketplace\Model\Feedback $marketplaceFeedback
     */
    public function __construct(\Webkul\Marketplace\Model\Feedback $marketplaceFeedback)
    {
        $this->marketplaceFeedback = $marketplaceFeedback;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->marketplaceFeedback->getAvailableStatuses();
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
