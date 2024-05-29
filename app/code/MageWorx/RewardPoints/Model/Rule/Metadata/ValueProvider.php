<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Rule\Metadata;

use MageWorx\RewardPoints\Model\Rule;
use Magento\Store\Model\System\Store;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject;
use MageWorx\RewardPoints\Model\RuleFactory;
use MageWorx\RewardPoints\Model\Source\Event as EventOptions;
use MageWorx\RewardPoints\Model\Source\GivePoints as GivePointOptions;
use MageWorx\RewardPoints\Model\Source\CalculationTypes as CalculationTypeOptions;
use MageWorx\RewardPoints\Model\Source\EmailTemplates as EmailTemplatesOptions;

class ValueProvider
{
    /**
     * @var Store
     */
    protected $store;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var DataObject
     */
    protected $objectConverter;

    /**
     * @var \MageWorx\RewardPoints\Model\RuleFactory
     */
    protected $rewardRuleFactory;

    /**
     * @var EventOptions
     */
    protected $eventOptions;

    /**
     * @var GivePointOptions
     */
    protected $givePointOption;

    /**
     * @var CalculationTypeOptions
     */
    protected $calculationTypeOptions;

    /**
     * @var EmailTemplatesOptions
     */
    protected $emailTemplateOptions;


    /**
     * ValueProvider constructor.
     *
     * @param Store $store
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObject $objectConverter
     * @param RuleFactory $rewardRuleFactory
     * @param EventOptions $eventOptions
     * @param GivePointOptions $givePoints
     * @param CalculationTypeOptions $calculationTypeOptions
     * @param EmailTemplatesOptions $emailTemplatesOptions
     */
    public function __construct(
        Store $store,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObject $objectConverter,
        RuleFactory $rewardRuleFactory,
        EventOptions $eventOptions,
        GivePointOptions $givePoints,
        CalculationTypeOptions $calculationTypeOptions,
        EmailTemplatesOptions $emailTemplatesOptions
    ) {
        $this->store                  = $store;
        $this->groupRepository        = $groupRepository;
        $this->searchCriteriaBuilder  = $searchCriteriaBuilder;
        $this->objectConverter        = $objectConverter;
        $this->rewardRuleFactory      = $rewardRuleFactory;
        $this->eventOptions           = $eventOptions;
        $this->givePointOption        = $givePoints;
        $this->calculationTypeOptions = $calculationTypeOptions;
        $this->emailTemplateOptions   = $emailTemplatesOptions;
    }

    /**
     * Get metadata for reward rule form. It will be merged with form UI component declaration.
     *
     * @param Rule $rule
     * @return array
     */
    public function getMetadataValues(\MageWorx\RewardPoints\Model\Rule $rule)
    {
        $customerGroups = $this->groupRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        $labels = $rule->getStoreLabels();

        return [
            'rule_information' => [
                'children' => [
                    'website_ids'           => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => $this->store->getWebsiteValuesForForm(),
                                ],
                            ],
                        ],
                    ],
                    'is_active'             => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => [
                                        ['label' => __('Active'), 'value' => '1'],
                                        ['label' => __('Inactive'), 'value' => '0']
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'customer_group_ids'    => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => $this->objectConverter->toOptionArray($customerGroups, 'id', 'code'),
                                ],
                            ],
                        ],
                    ],
                    'is_rss'                => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => [
                                        ['label' => __('Yes'), 'value' => '1'],
                                        ['label' => __('No'), 'value' => '0']
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'is_allow_notification' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => [
                                        ['label' => __('Yes'), 'value' => '1'],
                                        ['label' => __('No'), 'value' => '0']
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'email_template_id'     => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => $this->emailTemplateOptions->toOptionArray(),
                                ],
                            ],
                        ],
                    ],
                ]
            ],
            'actions'          => [
                'children' => [
                    //Event
                    'event'                 => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => $this->eventOptions->toOptionArray()
                                ],
                            ]
                        ]
                    ],
                    //Give Points
                    'simple_action'         => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => $this->givePointOption->toOptionArray()
                                ],
                            ]
                        ]
                    ],
                    'calculation_type'      => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => $this->calculationTypeOptions->toOptionArray()
                                ],
                            ]
                        ]
                    ],
                    'points_amount'         => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'value' => '0',
                                ],
                            ],
                        ],
                    ],
                    'stop_rules_processing' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => [
                                        ['label' => __('Yes'), 'value' => '1'],
                                        ['label' => __('No'), 'value' => '0'],
                                    ],
                                ],
                            ]
                        ]
                    ],
                ]
            ],
            'labels'           => [
                'children' => [
                    'store_labels[0]' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'value' => isset($labels[0]) ? $labels[0] : '',
                                ],
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }
}
