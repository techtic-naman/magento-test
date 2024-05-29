<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\Converter;

use Magento\Framework\DataObject;
use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Views extends \MageWorx\SocialProofBase\Model\ConverterAbstract
{
    /**
     * @var array
     */
    protected $allowedVars = [
        'product.name',
        'product.image',
        'product.url',
        'product.rating_summary',
        'product.rating_stars',
        'period',
        'count_customers',
        'count_times'
    ];

    /**
     * @param DataObject $campaign
     * @return string
     * @throws NoSuchEntityException
     */
    public function convert($campaign): string
    {
        $htmlTemplate = $campaign->getData(CampaignInterface::CONTENT);
        $vars         = $this->getVarsFromTemplate($htmlTemplate);

        if (empty($vars)) {
            return $htmlTemplate;
        }

        if (empty($campaign['viewedItem'])) {
            return '';
        }

        return $this->process($vars, $htmlTemplate, $campaign['viewedItem']);
    }

    /**
     * @param array $vars
     * @param string $template
     * @param DataObject $viewedItem
     * @return string
     * @throws NoSuchEntityException
     */
    protected function process($vars, $template, $viewedItem): string
    {
        foreach ($vars as $var) {
            switch ($var) {
                case 'product.name':
                    $value = $this->getProductName((int)$viewedItem->getData('product_id'));
                    break;
                case 'product.image':
                    $value = $this->getProductImageUrl((int)$viewedItem->getData('product_id'));
                    break;
                case 'product.url':
                    $value = $this->getProductUrl((int)$viewedItem->getData('product_id'));
                    break;
                case 'product.rating_summary':
                    $value = $this->getProductRatingSummary((int)$viewedItem->getData('product_id'));
                    break;
                case 'product.rating_stars':
                    $value = $this->getProductRatingStars((int)$viewedItem->getData('product_id'));
                    break;
                case 'period':
                    $value = $this->getPeriodStr((int)$viewedItem->getData('period'));
                    break;
                case 'count_customers':
                    $value = $this->getCountCustomersStr((int)$viewedItem->getData('count_visitors'));
                    break;
                case 'count_times':
                    $value = $this->getCountTimesStr((int)$viewedItem->getData('count_visitors'));
                    break;
                default:
                    $value = null;
            }

            $templateVarContainer = $this->getTemplateVarContainer($var, $value);

            $eventArgs = [
                'viewedItem'  => $viewedItem,
                'templateVarContainer' => $templateVarContainer
            ];

            $this->eventManager->dispatch(
                'mageworx_socialproofbase_campaign_views_convert_template_var',
                $eventArgs
            );

            $value = $templateVarContainer->getValue();

            if (!is_null($value)) {
                $template = str_replace("[$var]", $value, $template);
            }
        }

        return trim($template);
    }
}
