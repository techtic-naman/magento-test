<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Block\Adminhtml\CountdownTimer\Widget;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Widget\Block\Adminhtml\Widget\Chooser as WidgetChooser;
use Magento\Backend\Block\Widget\Grid\Extended as WidgetGridExtended;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\CollectionFactory;
use MageWorx\CountdownTimersBase\Model\CountdownTimerFactory;
use MageWorx\CountdownTimersBase\Model\Source\CountdownTimer\Status as StatusOptions;
use MageWorx\CountdownTimersBase\Model\Source\CountdownTimer\DisplayMode as DisplayModeOptions;

class Chooser extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var CountdownTimerFactory
     */
    protected $countdownTimerFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var StatusOptions
     */
    protected $statusOptions;

    /**
     * Chooser constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param CountdownTimerFactory $countdownTimerFactory
     * @param StatusOptions $statusOptions
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        CollectionFactory $collectionFactory,
        CountdownTimerFactory $countdownTimerFactory,
        StatusOptions $statusOptions,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);

        $this->countdownTimerFactory = $countdownTimerFactory;
        $this->collectionFactory     = $collectionFactory;
        $this->statusOptions         = $statusOptions;
    }

    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->setDefaultSort(CountdownTimerInterface::COUNTDOWN_TIMER_ID);
        $this->setDefaultDir(SortOrder::SORT_ASC);
        $this->setUseAjax(true);
        $this->setDefaultFilter(['chooser_status' => StatusOptions::ENABLE]);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param AbstractElement $element
     * @return AbstractElement
     * @throws LocalizedException
     */
    public function prepareElementHtml(AbstractElement $element): AbstractElement
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());

        $sourceUrl = $this->getUrl(
            'mageworx_countdowntimersbase/countdownTimer_widget/chooser',
            ['uniq_id' => $uniqId]
        );

        $chooser = $this->getLayout()->createBlock(
            WidgetChooser::class
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqId
        );

        if ($element->getValue()) {
            $countdownTimer = $this->countdownTimerFactory->create()->load($element->getValue());

            if ($countdownTimer->getId()) {
                $chooser->setLabel($this->escapeHtml($countdownTimer->getName()));
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());

        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback(): string
    {
        $chooserJsObj = $this->getId();
        $js           = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var blockId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                var blockTitle = trElement.down("td").next().innerHTML;
                ' .
            $chooserJsObj .
            '.setElementValue(blockId);
                ' .
            $chooserJsObj .
            '.setElementLabel(blockTitle);
                ' .
            $chooserJsObj .
            '.close();
            }
        ';

        return $js;
    }

    /**
     * @return WidgetGridExtended
     */
    protected function _prepareCollection(): WidgetGridExtended
    {
        /** @var \MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection
            ->addFieldToFilter(CountdownTimerInterface::DISPLAY_MODE, DisplayModeOptions::CUSTOM);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return WidgetGridExtended
     * @throws \Exception
     */
    protected function _prepareColumns(): WidgetGridExtended
    {
        $this->addColumn(
            'chooser_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'index'  => CountdownTimerInterface::COUNTDOWN_TIMER_ID,
                'width'  => 50
            ]
        );

        $this->addColumn(
            'chooser_name',
            ['header' => __('Name'), 'align' => 'left', 'index' => CountdownTimerInterface::NAME]
        );

        $this->addColumn(
            'chooser_status',
            [
                'header'  => __('Status'),
                'index'   => CountdownTimerInterface::STATUS,
                'type'    => 'options',
                'options' => $this->statusOptions->toArray()
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl(): string
    {
        return $this->getUrl('mageworx_countdowntimersbase/countdownTimer_widget/chooser', ['_current' => true]);
    }
}
