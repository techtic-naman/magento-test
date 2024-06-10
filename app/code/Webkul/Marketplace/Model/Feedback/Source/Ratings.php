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
 * Class Ratings is used tp get the Feedback rating options
 */
class Ratings implements OptionSourceInterface
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
        $availableOptions = $this->marketplaceFeedback->getAllRatingOptions();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'row_label' =>  '<span class="mpfeedback"><span class="ratingslider-box">
                <span class="rating" style="width:'.$key.'%;"></span>
            </span></span>',
                'value' => $key,
            ];
        }
        return $options;
    }
}
