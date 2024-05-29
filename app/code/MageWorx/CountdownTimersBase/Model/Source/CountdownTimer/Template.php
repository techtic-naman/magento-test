<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Model\Source\CountdownTimer;

use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\Template\Collection;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\Template\CollectionFactory;
use MageWorx\CountdownTimersBase\Model\CountdownTimer\Template as CountdownTimerTemplate;

class Template extends \MageWorx\CountdownTimersBase\Model\Source
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Template constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if (is_null($this->options)) {
            /** @var Collection $collection */
            $collection = $this->collectionFactory->create();

            $collection->addFieldToSelect([CountdownTimerTemplate::IDENTIFIER, CountdownTimerTemplate::TITLE]);

            $templates = $collection->getData();
            $options   = [];

            foreach ($templates as $template) {
                $options[] = [
                    'value' => $template[CountdownTimerTemplate::IDENTIFIER],
                    'label' => $template[CountdownTimerTemplate::TITLE]
                ];
            }

            $this->options = $options;
        }

        return $this->options;
    }
}
