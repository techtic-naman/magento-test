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
namespace Webkul\Marketplace\Controller\Adminhtml\Support;

use Magento\Backend\App\Action;

class Storereview extends Action
{
    /**
     * Support Userguide Link.
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl("https://store.webkul.com/magento2-multi-vendor-marketplace.html#reviews");
        return $resultRedirect;
    }
}
