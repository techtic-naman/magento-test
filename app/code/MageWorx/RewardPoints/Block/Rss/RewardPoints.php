<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Rss;

use Magento\Customer\Model\Context;
use Magento\Framework\App\Rss\DataProviderInterface;

class RewardPoints extends \Magento\Framework\View\Element\AbstractBlock implements DataProviderInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\RewardPoints\Model\Rss\RewardPoints
     */
    protected $rssModel;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Framework\App\Rss\UrlBuilderInterface
     */
    protected $rssUrlBuilder;

    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * RewardPoints constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \MageWorx\RewardPoints\Model\Rss\RewardPoints $rssModel
     * @param \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \MageWorx\RewardPoints\Model\Rss\RewardPoints $rssModel,
        \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        array $data = []
    ) {
        $this->storeManager  = $context->getStoreManager();
        $this->rssModel      = $rssModel;
        $this->httpContext   = $httpContext;
        $this->rssUrlBuilder = $rssUrlBuilder;
        $this->helperData    = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->setCacheKey('rss_mageworx_rewardpoints_rule_' . $this->getStoreId() . '_' . $this->getCustomerGroupId());
        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getRssData()
    {
        $storeId = $this->getStoreId();
        $store   = $this->storeManager->getStore($storeId);

        $customerGroupId = $this->getCustomerGroupId();

        $newUrl = $this->rssUrlBuilder->getUrl(
            [
                'type'     => 'mageworx_rewardpoints',
                'store_id' => $storeId,
                'cid'      => $customerGroupId,
            ]
        );

        $title = __('%1 - Reward Points Actions', $store->getFrontendName());
        $lang  = $this->_scopeConfig->getValue(
            \Magento\Directory\Helper\Data::XML_PATH_DEFAULT_LOCALE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        $data = [
            'title'       => $title,
            'description' => $title,
            'link'        => $newUrl,
            'charset'     => 'UTF-8',
            'language'    => $lang,
        ];

        /** @var $rule \MageWorx\RewardPoints\Model\Rule */
        foreach ($this->rssModel->getDiscountCollection($store->getWebsiteId(), $customerGroupId) as $rule) {

            $toDate = '';

            if ($rule->getToDate()) {
                $formattedDate = $this->formatDate($rule->getToDate(), \IntlDateFormatter::MEDIUM);
                $toDate        = '<br/>Reward Points Action End Date: ' . $formattedDate;
            }

            $description = sprintf(
                '<table><tr><td style="text-decoration:none;">%s<br/>Reward Points Action Start from: %s %s</td></tr></table>',
                $this->escapeHtml($rule->getDescription()),
                $this->formatDate($rule->getFromDate(), \IntlDateFormatter::MEDIUM),
                $toDate
            );

            $data['entries'][] = [
                'title'       => $this->escapeHtml($rule->getName()),
                'description' => $description,
                'link'        => $this->_urlBuilder->getUrl('')
            ];
        }

        return $data;
    }

    /**
     * @return int
     */
    protected function getCustomerGroupId()
    {
        $customerGroupId = (int)$this->getRequest()->getParam('cid');
        if ($customerGroupId == null) {
            $customerGroupId = $this->httpContext->getValue(Context::CONTEXT_GROUP);
        }

        return $customerGroupId;
    }

    /**
     * @return int
     */
    protected function getStoreId()
    {
        $storeId = (int)$this->getRequest()->getParam('store_id');
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return $storeId;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheLifetime()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed()
    {
        return $this->helperData->isRssEnable();
    }

    /**
     * {@inheritdoc}
     */
    public function getFeeds()
    {
        $data = [];
        if ($this->helperData->isRssEnable()) {
            $url  = $this->rssUrlBuilder->getUrl(
                [
                    'type'     => 'mageworx_rewardpoints',
                    'store_id' => $this->getStoreId(),
                    'cid'      => $this->getCustomerGroupId(),
                ]
            );
            $data = ['label' => __('Reward Points Actions'), 'link' => $url];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthRequired()
    {
        return false;
    }
}
