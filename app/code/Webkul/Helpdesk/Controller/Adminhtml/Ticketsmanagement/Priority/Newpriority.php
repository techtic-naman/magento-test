<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Helpdesk
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Priority;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Newpriority extends Action
{
    /**
     * Edit action
     *
     * @return void
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_FORWARD)->forward('edit');
    }

    /**
     * Check newpriority action Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::priority');
    }
}
