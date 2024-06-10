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

namespace Webkul\Walletsystem\Block\Adminhtml\Wallet\Renderer;

/**
 * Webkul Walletsystem Addamountbutton Block
 */
class Addamountbutton extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Array to store all options data.
     *
     * @var array
     */
    protected $actions = [];

    /**
     * Return Actions.
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $this->actions = [];
        $actions[0] = [
            '@' => [
                'type' => 'button',
                'class' => 'button wk_addamount',
                'customer-id' => $row->getEntityId(),
                'customer-name' => preg_replace('#<script(.*?)>(.*?)</script>#is', '', $row->getName()),
                'title' => __('Adjust amount'),
            ],
            '#' => __('Adjust amount'),
        ];
        $this->addToActions($actions);

        return $this->_actionsToHtml();
    }

    /**
     * Get escaped value.
     *
     * @param string $value
     *
     * @return string
     */
    protected function _getEscapedValue($value)
    {
        return $this->escapeHtml($value);
    }

    /**
     * Render options array as a HTML string.
     *
     * @param array $actions
     *
     * @return string
     */
    protected function _actionsToHtml(array $actions = [])
    {
        $html = [];
        $attributesObject = new \Magento\Framework\DataObject();
        if (empty($actions)) {
            $actions = $this->actions;
        }
        foreach ($actions[0] as $action) {
            if (!empty($action['@'])) {
                $attributesObject->setData($action['@']);
                $html[] = '<button '.$attributesObject->serialize().'>'.$action['#'].'</button>';
            } else {
                $html[] = '<span>'.$action['#'].'</span>';
            }
        }

        return implode('', $html);
    }

    /**
     * Add one action array to all options data storage.
     *
     * @param array $actionArray
     */
    public function addToActions($actionArray)
    {
        $this->actions[] = $actionArray;
    }
}
