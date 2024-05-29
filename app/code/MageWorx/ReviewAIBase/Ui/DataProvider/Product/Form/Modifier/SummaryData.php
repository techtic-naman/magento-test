<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Ui\DataProvider\Product\Form\Modifier;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Request\Http as RequestHTTP;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use MageWorx\ReviewAIBase\Api\ReviewSummaryRepositoryInterface;

class SummaryData implements ModifierInterface
{
    protected ReviewSummaryRepositoryInterface $reviewSummaryRepository;
    protected SearchCriteriaBuilder            $searchCriteriaBuilder;
    protected FilterBuilder                    $filterBuilder;
    protected RequestHTTP                      $request;
    protected StoreManagerInterface            $storeManager;
    protected UrlInterface                     $urlBuilder;

    public function __construct(
        ReviewSummaryRepositoryInterface $reviewSummaryRepository,
        SearchCriteriaBuilder            $searchCriteriaBuilder,
        FilterBuilder                    $filterBuilder,
        RequestHTTP                      $request,
        UrlInterface                     $urlBuilder,
        StoreManagerInterface            $storeManager
    ) {
        $this->reviewSummaryRepository = $reviewSummaryRepository;
        $this->searchCriteriaBuilder   = $searchCriteriaBuilder;
        $this->filterBuilder           = $filterBuilder;
        $this->request                 = $request;
        $this->urlBuilder              = $urlBuilder;
        $this->storeManager            = $storeManager;
    }

    /**
     * @param array $data
     * @return array
     * @throws LocalizedException
     */
    public function modifyData(array $data): array
    {
        foreach ($data as $productId => $productData) {
            $storeId = $this->storeManager->getStore()->getId();
            if (!$storeId) {
                return $data;
            }

            if (!$this->getProductId()) {
                return $data;
            }

            $this->searchCriteriaBuilder->addFilters(
                [
                    $this->filterBuilder
                        ->setField('product_id')
                        ->setValue($productId)
                        ->setConditionType('eq')
                        ->create(),
                    $this->filterBuilder
                        ->setField('store_id')
                        ->setValue($storeId)
                        ->setConditionType('eq')
                        ->create()
                ]
            );

            $searchCriteria = $this->searchCriteriaBuilder->create();
            $searchResults  = $this->reviewSummaryRepository->getList($searchCriteria);

            if ($searchResults->getTotalCount() > 0) {
                $items                                       = $searchResults->getItems();
                $firstItem                                   = reset($items);
                $data[$productId]['product']['summary_data'] = $firstItem->getSummaryData();
            }
        }

        return $data;
    }

    /**
     * @param array $meta
     * @return array
     * @throws NoSuchEntityException
     */
    public function modifyMeta(array $meta): array
    {
        if (!$this->getProductId()) {
            return $meta;
        }

        $storeId = $this->storeManager->getStore()->getId();
        if (!$storeId) {
            $meta['review_summary_fieldset'] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => 'fieldset',
                            'label'         => __('Product Reviews Summary'),
                            'sortOrder'     => 1000, // Adjust sortOrder to position at the bottom
                            'collapsible'   => true,
                        ]
                    ]
                ],
                'children'  => [
                    'summary_data_placeholder' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'componentType' => 'text',
                                    'component'     => 'Magento_Ui/js/form/element/abstract',
                                    'template'      => 'ui/form/field',
                                    'elementTmpl'   => 'ui/form/element/text',
                                    'value'         => __('Please switch to a corresponding store view to review, edit and generate the summary'),
                                    'sortOrder'     => 10,
                                    'displayArea'   => 'dataScope',
                                    'dataScope'     => 'staticText',
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            return $meta;
        }

        // Create a new fieldset at the bottom of the form
        $meta['review_summary_fieldset'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'fieldset',
                        'label'         => __('Product Reviews Summary'),
                        'sortOrder'     => 1000, // Adjust sortOrder to position at the bottom
                        'collapsible'   => true,
                    ]
                ]
            ],
            'children'  => [
                'summary_data'      => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement'       => 'wysiwyg',
                                'componentType'     => 'field',
                                'source'            => 'product',
                                'provider'          => 'product_form.product_form_data_source',
                                'dataScope'         => 'data.product.summary_data',
                                'label'             => __('Product Reviews Summary'),
                                'sortOrder'         => 10,
                                'wysiwyg'           => true,
                                'wysiwygConfigData' => [
                                    'add_variables'          => false,
                                    'add_widgets'            => false,
                                    'add_images'             => false,
                                    'add_directives'         => false,
                                    'use_container'          => true,
                                    'container_class'        => 'hor-scroll',
                                    'container_id'           => 'summary_data',
                                    'height'                 => '100px',
                                    'width'                  => '100%',
                                    'hidden'                 => false,
                                    'show'                   => true,
                                    'displayArea'            => 'summary_data',
                                    'is_pagebuilder_enabled' => false,
                                ],
                            ]
                        ]
                    ]
                ],
                'buttons_container' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'label'         => '',
                                'sortOrder'     => 100,
                                'collapsible'   => false,
                                'template'      => 'MageWorx_ReviewAIBase/generate_buttons_container',
                            ]
                        ]
                    ],
                    'children'  => [
                        'generate_button'     => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'component'     => 'MageWorx_ReviewAIBase/js/generate-button',
                                        'componentType' => 'container',
                                        'displayAsLink' => false,
                                        'title'         => __('Generate'),
                                        'sortOrder'     => 30,
                                        'ajaxUrl'       => $this->urlBuilder->getUrl('mageworx_reviewai/reviewsummary/GenerateForProduct'),
                                        'actions'       => [
                                            [
                                                'targetName' => 'index = summary_data',
                                                'actionName' => 'generateSummary',
                                            ],
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'add_to_queue_button' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'component'     => 'MageWorx_ReviewAIBase/js/add-to-queue-button',
                                        'componentType' => 'container',
                                        'displayAsLink' => false,
                                        'title'         => __('Add to queue'),
                                        'sortOrder'     => 40,
                                        'ajaxUrl'       => $this->urlBuilder->getUrl('mageworx_reviewai/reviewsummary/AddProductToQueue'),
                                        'actions'       => [
                                            [
                                                'targetName' => 'index = summary_data',
                                                'actionName' => 'generateSummary',
                                            ],
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ]
        ];

        return $meta;
    }

    /**
     * Get product id from request
     *
     * @return int|null
     */
    protected function getProductId(): ?int
    {
        $id = $this->request->getParam('id');

        return $id ? (int)$id : null;
    }
}
