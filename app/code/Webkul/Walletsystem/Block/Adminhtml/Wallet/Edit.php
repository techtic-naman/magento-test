<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_Walletsystem
 * @author Webkul
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\Walletsystem\Block\Adminhtml\Wallet;

/**
 * Webkul Walletsystem Class
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize walletsystem edit block
     */
    protected function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'Webkul_Walletsystem';
        $this->_controller = 'adminhtml_wallet';

        parent::_construct();
        $this->buttonList->remove('save');
        $this->buttonList->add(
            'my_back',
            [
                'label' =>  'Back',
                'onclick'   => 'setLocation(\'' . $this->getUrl('walletsystem/wallet/index') . '\')',
                'class'     =>  'back'
            ],
            100
        );
        $this->buttonList->remove('back');
    }

    /**
     * Retrieve text for header element depending on loaded post
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Adjust Amount');
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
