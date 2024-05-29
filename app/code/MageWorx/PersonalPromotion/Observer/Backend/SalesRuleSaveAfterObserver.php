<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PersonalPromotion\Observer\Backend;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use MageWorx\PersonalPromotion\Helper\Data as HelperData;
use MageWorx\PersonalPromotion\Helper\LinkFieldResolver as HelperLinkFieldResolver;
use MageWorx\PersonalPromotion\Model\ResourceModel\PersonalPromotion as PersonalPromotionResourceModel;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\Framework\App\Request\Http as HttpRequest;

class SalesRuleSaveAfterObserver implements ObserverInterface
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperLinkFieldResolver
     */
    protected $helperLinkFieldResolver;

    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * @var PersonalPromotionResourceModel
     */
    protected $personalPromotionResourceModel;

    /**
     * SalesRuleSaveAfterObserver constructor.
     *
     * @param HelperData $helperData
     * @param HelperLinkFieldResolver $helperLinkFieldResolver
     * @param HttpRequest $request
     * @param PersonalPromotionResourceModel $personalPromotionResourceModel
     */
    public function __construct(
        HelperData $helperData,
        HelperLinkFieldResolver $helperLinkFieldResolver,
        HttpRequest $request,
        PersonalPromotionResourceModel $personalPromotionResourceModel
    ) {
        $this->helperData                     = $helperData;
        $this->helperLinkFieldResolver        = $helperLinkFieldResolver;
        $this->request                        = $request;
        $this->personalPromotionResourceModel = $personalPromotionResourceModel;
    }

    /**
     * @param EventObserver $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        $dataPromoCustomers = "{}";
        $ruleFileldId       = $this->helperLinkFieldResolver->getLinkField(RuleInterface::class);
        $ruleId             = $this->getRuleId($observer, $ruleFileldId);
        if (!$ruleId) {
            return $this;
        }

        $ruleData = $this->getRuleData($observer);
        if (!empty($ruleData['promo_customers'])) {
            $dataPromoCustomers = $ruleData['promo_customers'];
        }

        $promoCustomersStaging = $this->request->getPost('promo_customers_staging');
        $staging               = $this->request->getPost('staging');

        if (!empty($promoCustomersStaging) && !empty($staging)) {
            $dataPromoCustomers = $promoCustomersStaging;
        }

        $this->personalPromotionResourceModel->deleteCustomersByRuleId($ruleId);
        $dataCustomers = json_decode($dataPromoCustomers, true);
        $dataCustomers = $this->filterCustomers($dataCustomers);

        if (!empty($dataCustomers)) {
            $selectedCustomers = $this->prepareDataToSaveCustomers($dataCustomers, $ruleId);
            $this->personalPromotionResourceModel->saveRuleCustomerRelations($selectedCustomers);
        }

        return $this;
    }

    /**
     * @param array $dataSelectedCustomers
     * @param int $salesRuleId
     *
     * @return array
     */
    protected function prepareDataToSaveCustomers($dataSelectedCustomers, $salesRuleId)
    {
        $result = [];
        foreach ($dataSelectedCustomers as $key => $value) {
            $result[] = [
                'personal_id'   => '',
                'sales_rule_id' => $salesRuleId,
                'customer_id'   => $key
            ];
        }

        return $result;
    }


    /**
     * @param $observer
     * @return null|array
     */
    protected function getRuleData($observer)
    {
        $event = $observer->getEvent();
        $rule  = $event->getData('rule');
        if (!$rule) {
            return null;
        }

        return $rule->getData();
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @param String $ruleFileldId
     * @return int|null
     */
    protected function getRuleId($observer, $ruleFileldId)
    {
        $event = $observer->getEvent();
        $rule  = $event->getData('rule');
        if (!$rule) {
            return null;
        }

        $ruleId = $rule->getData($ruleFileldId);
        if (!$ruleId) {
            return null;
        }

        return (int)$ruleId;
    }

    /**
     * Delete not correct data
     *
     * @param $dataCustomers
     * @return mixed
     */
    protected function filterCustomers($dataCustomers)
    {
        foreach ($dataCustomers as $key => $value) {
            if (!is_int($key)) {
                unset($dataCustomers[$key]);
            }
        }

        return $dataCustomers;
    }
}