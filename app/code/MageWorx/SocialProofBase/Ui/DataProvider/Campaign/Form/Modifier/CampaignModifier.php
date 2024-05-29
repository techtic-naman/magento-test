<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Ui\DataProvider\Campaign\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\App\Request\Http as HttpRequest;

class CampaignModifier implements ModifierInterface
{
    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * CampaignModifier constructor.
     *
     * @param HttpRequest $request
     */
    public function __construct(HttpRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta): array
    {
        if ($this->request->getFullActionName() !== 'mageworx_socialproofbase_campaign_new') {
            $metaData = [
                'display-mode' => [
                    'children' => [
                        'display_mode' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => true
                                    ],
                                ],
                            ]
                        ]
                    ]
                ],
                'event-type'   => [
                    'children' => [
                        'event_type' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => true
                                    ],
                                ],
                            ]
                        ]
                    ]
                ]
            ];

            $meta = array_merge_recursive($meta, $metaData);
        }

        return $meta;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data): array
    {
        return $data;
    }
}
