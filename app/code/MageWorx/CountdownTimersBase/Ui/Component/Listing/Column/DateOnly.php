<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Ui\Component\Listing\Column;

use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class DateOnly extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var DateTimeFactory
     */
    protected $dateTimeFactory;

    /**
     * DateOnly constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param DateTimeFactory $dateTimeFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        DateTimeFactory $dateTimeFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->dateTimeFactory = $dateTimeFactory;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {

            foreach ($dataSource['data']['items'] as &$item) {

                if (isset($item[$this->getData('name')])
                    && $item[$this->getData('name')] !== "0000-00-00 00:00:00"
                ) {
                    $date = $this->dateTimeFactory->create()->date(
                        DateTime::DATE_PHP_FORMAT,
                        $item[$this->getData('name')]
                    );

                    $item[$this->getData('name')] = $date;
                }
            }
        }

        return $dataSource;
    }
}
