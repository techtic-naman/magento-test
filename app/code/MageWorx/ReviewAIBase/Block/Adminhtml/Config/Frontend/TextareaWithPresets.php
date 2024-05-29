<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Block\Adminhtml\Config\Frontend;

use Magento\Backend\Block\Template\Context;
use MageWorx\OpenAI\Api\Data\PresetGroupInterface;
use MageWorx\OpenAI\Block\Adminhtml\View\Element\Html\PresetsSelect as Select;
use MageWorx\OpenAI\Model\Presets\PresetGroupFactory as PresetGroupFactory;
use MageWorx\OpenAI\Model\ResourceModel\Presets\Preset\Collection as PresetCollection;
use MageWorx\OpenAI\Model\ResourceModel\Presets\Preset\CollectionFactory as PresetCollectionFactory;
use MageWorx\OpenAI\Model\ResourceModel\Presets\PresetGroup as PresetGroupResource;

class TextareaWithPresets extends \Magento\Config\Block\System\Config\Form\Field
{
    protected PresetGroupFactory    $presetGroupFactory;
    protected PresetGroupResource   $presetGroupResource;
    private PresetCollectionFactory $presetCollectionFactory;

    public function __construct(
        Context                 $context,
        PresetCollectionFactory $presetCollectionFactory,
        PresetGroupFactory      $presetGroupFactory,
        PresetGroupResource     $presetGroupResource,
        array                   $data = []
    ) {
        parent::__construct($context, $data);
        $this->presetCollectionFactory = $presetCollectionFactory;
        $this->presetGroupFactory      = $presetGroupFactory;
        $this->presetGroupResource     = $presetGroupResource;
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html   = parent::_getElementHtml($element);
        $select = $this->_getSelectHtml($element);
        $html   .= $select . $this->_getJsScript();

        return $html;
    }

    /**
     * Returns the select HTML with presets.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    private function _getSelectHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element): string
    {
        $groupId = $this->getReviewSummaryGroupId();
        $storeId = $this->_request->getParam('store', 0);
        /** @var PresetCollection $presets */
        $presets = $this->presetCollectionFactory->create()
                                                 ->addFieldToFilter('group_id', $groupId)
                                                 ->addFieldToFilter('store_id', [['eq' => $storeId], ['eq' => 0]]);

        $presetsOptionArray = $presets->toOptionArray();
        // Add the "please select" option
        array_unshift(
            $presetsOptionArray,
            [
                'value'        => '',
                'label'        => __('Select a preset'),
                'data-content' => '',
            ]
        );

        $isCheckboxRequired = $this->_isInheritCheckboxRequired($element);
        $disabled           = false;
        if ($element->getInherit() == 1 && $isCheckboxRequired) {
            $disabled = true;
        }

        $select = $this->getLayout()->createBlock(Select::class)
                       ->setName('preset_select')
                       ->setId('preset_select')
                       ->setClass('select')
                       ->setOptions($presetsOptionArray)
                       ->setExtraParams($disabled ? ' disabled="disabled"' : '')
                       ->getHtml();

        return $select;
    }

    private function _getJsScript(): string
    {
        $script = "<script type='text/javascript'>
        require(['jquery', 'jquery/ui'], function ($) {
            const defaultValue = $('#review_ai_main_content').val();
            $('#preset_select').on('change', function () {
                var content = $(this).find('option:selected').data('content');
                if (content) {
                    $('#review_ai_main_content').val(content);
                } else {
                    $('#review_ai_main_content').val(defaultValue);
                }
            });
        });
        </script>";

        return $script;
    }

    /**
     * Returns the review summary group ID by its code.
     *
     * @return int The review summary group ID.
     */
    protected function getReviewSummaryGroupId(): int
    {
        /** @var PresetGroupInterface $group */
        $group = $this->presetGroupFactory->create();
        $this->presetGroupResource->load($group, 'review_summary', PresetGroupInterface::CODE);

        return (int)$group->getGroupId();
    }
}
