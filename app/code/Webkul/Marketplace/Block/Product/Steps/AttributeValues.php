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
/**
 * Marketplace block for fieldset of configurable product.
 */

namespace Webkul\Marketplace\Block\Product\Steps;

class AttributeValues extends \Magento\Ui\Block\Component\StepsWizard\StepAbstract
{
    /**
     * Get caption
     *
     * @return string
     */
    public function getCaption()
    {
        return __('Attribute Values');
    }
}
