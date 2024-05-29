<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\Converter;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order as ModelOrder;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory as OrderAddressCollectionFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Review\Model\ReviewFactory;

class RecentSales extends \MageWorx\SocialProofBase\Model\ConverterAbstract
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
        'customer.name',
        'customer.location',
        'customer.city',
        'customer.country',
        'customer.region',
        'last_purchase'
    ];

    /**
     * @var null|array
     */
    protected $customerAddressData;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var OrderAddressCollectionFactory
     */
    protected $addressCollectionFactory;

    /**
     * @var CountryFactory
     */
    protected $countryFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * Review model factory
     *
     * @var ReviewFactory
     */
    protected $reviewFactory;

    /**
     * RecentSales constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param ImageHelper $imageHelper
     * @param ReviewFactory $reviewFactory
     * @param OrderAddressCollectionFactory $addressCollectionFactory
     * @param CountryFactory $countryFactory
     * @param EventManagerInterface $eventManager
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        ImageHelper $imageHelper,
        ReviewFactory $reviewFactory,
        OrderAddressCollectionFactory $addressCollectionFactory,
        CountryFactory $countryFactory,
        EventManagerInterface $eventManager,
        Escaper $escaper,
        $data = []
    ) {
        parent::__construct(
            $storeManager,
            $productRepository,
            $imageHelper,
            $reviewFactory,
            $eventManager,
            $escaper,
            $data
        );

        $this->addressCollectionFactory = $addressCollectionFactory;
        $this->countryFactory           = $countryFactory;
    }

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

        if (empty($campaign['orderItem'])) {
            return '';
        }

        return $this->process($vars, $htmlTemplate, $campaign['orderItem']);
    }

    /**
     * @param array $vars
     * @param string $template
     * @param DataObject $orderItem
     * @return string
     * @throws NoSuchEntityException
     */
    protected function process($vars, $template, $orderItem): string
    {
        foreach ($vars as $var) {
            switch ($var) {
                case 'product.name':
                    $value = $this->escaper->escapeHtml($orderItem->getData(OrderItemInterface::NAME));
                    break;
                case 'product.image':
                    $value = $this->getProductImageUrl((int)$orderItem->getData(OrderItemInterface::PRODUCT_ID));
                    break;
                case 'product.url':
                    $value = $this->getProductUrl((int)$orderItem->getData(OrderItemInterface::PRODUCT_ID));
                    break;
                case 'product.rating_summary':
                    $value = $this->getProductRatingSummary((int)$orderItem->getData(OrderItemInterface::PRODUCT_ID));
                    break;
                case 'product.rating_stars':
                    $value = $this->getProductRatingStars((int)$orderItem->getData(OrderItemInterface::PRODUCT_ID));
                    break;
                case 'customer.name':
                    $value = $this->getCustomerName((int)$orderItem->getData(ModelOrder::BILLING_ADDRESS_ID));
                    break;
                case 'customer.location':
                    $value = $this->getCustomerLocation((int)$orderItem->getData(ModelOrder::BILLING_ADDRESS_ID));
                    break;
                case 'customer.city':
                    $value = $this->getCustomerCity((int)$orderItem->getData(ModelOrder::BILLING_ADDRESS_ID));
                    break;
                case 'customer.country':
                    $value = $this->getCustomerCountry((int)$orderItem->getData(ModelOrder::BILLING_ADDRESS_ID));
                    break;
                case 'customer.region':
                    $value = $this->getCustomerRegion((int)$orderItem->getData(ModelOrder::BILLING_ADDRESS_ID));
                    break;
                case 'last_purchase':
                    $value = $this->getLastPurchase($orderItem->getData(OrderItemInterface::CREATED_AT));
                    break;
                default:
                    $value = null;
            }

            $templateVarContainer = $this->getTemplateVarContainer($var, $value);

            $eventArgs = [
                'orderItem'            => $orderItem,
                'templateVarContainer' => $templateVarContainer
            ];

            $this->eventManager->dispatch(
                'mageworx_socialproofbase_campaign_recent_sales_convert_template_var',
                $eventArgs
            );

            $value = $templateVarContainer->getValue();

            if (!is_null($value)) {
                $template = str_replace("[$var]", (string)$value, $template);
            }
        }

        return trim($template);
    }

    /**
     * @param int $billingAddressId
     * @return string
     */
    protected function getCustomerName($billingAddressId): string
    {
        $customerAddressData = $this->getCustomerAddressData($billingAddressId);

        if ($customerAddressData) {
            return $this->escaper->escapeHtml($customerAddressData['firstname']);
        }

        return '';
    }


    /**
     * @param int $billingAddressId
     * @return string
     */
    protected function getCustomerLocation($billingAddressId): string
    {
        $customerAddressData = $this->getCustomerAddressData($billingAddressId);

        if ($customerAddressData) {
            $city    = $customerAddressData['city'];
            $country = $customerAddressData['county'];
            $result  = $city ? $city . ', ' . $country : $country;

            return $this->escaper->escapeHtml($result);
        }

        return '';
    }

    /**
     * @param int $billingAddressId
     * @return string
     */
    protected function getCustomerCity($billingAddressId): string
    {
        $customerAddressData = $this->getCustomerAddressData($billingAddressId);

        if ($customerAddressData) {

            return $this->escaper->escapeHtml($customerAddressData['city']);
        }

        return '';
    }

    /**
     * @param int $billingAddressId
     * @return string
     */
    protected function getCustomerCountry($billingAddressId): string
    {
        $customerAddressData = $this->getCustomerAddressData($billingAddressId);

        if ($customerAddressData) {

            return $this->escaper->escapeHtml($customerAddressData['country']);
        }

        return '';
    }

    /**
     * @param int $billingAddressId
     * @return string
     */
    protected function getCustomerRegion($billingAddressId): string
    {
        $customerAddressData = $this->getCustomerAddressData($billingAddressId);

        if ($customerAddressData) {

            return $this->escaper->escapeHtml($customerAddressData['region']);
        }

        return '';
    }

    /**
     * @param int $billingAddressId
     * @return null|array
     */
    protected function getCustomerAddressData($billingAddressId): ?array
    {
        if (!$this->customerAddressData) {

            $collection = $this->addressCollectionFactory->create();
            $collection
                ->addFieldToSelect(
                    [
                        OrderAddressInterface::FIRSTNAME,
                        OrderAddressInterface::CITY,
                        OrderAddressInterface::COUNTRY_ID,
                        OrderAddressInterface::REGION
                    ]
                )
                ->addFieldToFilter(OrderAddressInterface::ENTITY_ID, $billingAddressId);

            $addressData = current($collection->getData());

            if (!empty($addressData)) {

                $this->customerAddressData = [
                    'firstname' => $addressData[OrderAddressInterface::FIRSTNAME],
                    'city'      => $addressData[OrderAddressInterface::CITY],
                    'county'    => $this->getCountryName($addressData[OrderAddressInterface::COUNTRY_ID]),
                    'region'    => $addressData[OrderAddressInterface::REGION]
                ];
            }
        }

        return $this->customerAddressData;
    }

    /**
     * @param string $countryCode
     * @return string
     */
    protected function getCountryName($countryCode): string
    {
        $country = $this->countryFactory->create()->loadByCode($countryCode);

        return $country->getName();
    }

    /**
     * @param string $timeStr
     * @return string
     */
    protected function getLastPurchase($timeStr): string
    {
        $diff = time() - strtotime($timeStr);
        $days = floor($diff / (60 * 60 * 24));

        if ($days) {
            if ($days > 1) {
                return $days . ' ' . __('days ago');
            }

            return $days . ' ' . __('day ago');
        }

        $hours = floor($diff / (60 * 60));

        if ($hours) {
            if ($hours > 1) {
                return $hours . ' ' . __('hours ago');
            }

            return $hours . ' ' . __('hour ago');
        }

        $minutes = round($diff / 60);

        if ($minutes) {
            if ($minutes > 1) {
                return $minutes . ' ' . __('minutes ago');
            }

            return $minutes . ' ' . __('minute ago');
        }

        if ($diff > 1) {
            return $diff . ' ' . __('seconds ago');
        }

        return __('1 second ago')->__toString();
    }
}
