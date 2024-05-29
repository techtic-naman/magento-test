<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use MageWorx\CountdownTimersBase\Helper\VersionResolver;

class CountdownTimerActions extends Column
{
    const URL_PATH_EDIT   = 'mageworx_countdowntimersbase/countdownTimer/edit';
    const URL_PATH_DELETE = 'mageworx_countdowntimersbase/countdownTimer/delete';

    /**
     * Url builder
     *
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var VersionResolver
     */
    protected $versionResolver;

    /**
     * CountdownTimerActions constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param VersionResolver $versionResolver
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        VersionResolver $versionResolver,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder      = $urlBuilder;
        $this->versionResolver = $versionResolver;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {

            foreach ($dataSource['data']['items'] as & $item) {

                if (isset($item[CountdownTimerInterface::COUNTDOWN_TIMER_ID])) {
                    $timerId = $item[CountdownTimerInterface::COUNTDOWN_TIMER_ID];
                    $confirm = [
                        'title'   => __('Delete "%1"', ['${ $.$data.name }']),
                        'message' => __(
                            'Are you sure you want to delete the Countdown Timer "%1"?',
                            ['${ $.$data.name }']
                        )
                    ];

                    if ($this->versionResolver->checkModuleVersion('Magento_Ui', '101.1.4')) {
                        $confirm['__disableTmpl'] = ['title' => false, 'message' => false];
                    }

                    $item[$this->getData('name')] = [
                        'edit'   => [
                            'href'  => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    CountdownTimerInterface::COUNTDOWN_TIMER_ID => $timerId
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href'    => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    CountdownTimerInterface::COUNTDOWN_TIMER_ID => $timerId
                                ]
                            ),
                            'label'   => __('Delete'),
                            'confirm' => $confirm
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
