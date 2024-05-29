<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\Source\Campaign;

use MageWorx\SocialProofBase\Model\ResourceModel\Campaign\Template\Collection;
use MageWorx\SocialProofBase\Model\ResourceModel\Campaign\Template\CollectionFactory;
use MageWorx\SocialProofBase\Model\Campaign\Template as CampaignTemplate;

class Template extends \MageWorx\SocialProofBase\Model\Source
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

            $collection->addFieldToSelect([CampaignTemplate::IDENTIFIER, CampaignTemplate::TITLE]);

            $templates = $collection->getData();
            $options   = [];

            foreach ($templates as $template) {
                $options[] = [
                    'value' => $template[CampaignTemplate::IDENTIFIER],
                    'label' => $template[CampaignTemplate::TITLE]
                ];
            }

            $this->options = $options;
        }

        return $this->options;
    }
}
