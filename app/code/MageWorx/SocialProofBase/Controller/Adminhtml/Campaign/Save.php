<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Controller\Adminhtml\Campaign;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\SocialProofBase\Api\CampaignRepositoryInterface;
use MageWorx\SocialProofBase\Api\Data\CampaignInterfaceFactory;
use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use MageWorx\SocialProofBase\Helper\Campaign\Rules as RulesHelper;
use MageWorx\SocialProofBase\Model\Source\Campaign\DisplayMode as DisplayModeOptions;
use MageWorx\SocialProofBase\Model\Source\Campaign\EventType as EventTypeOptions;
use MageWorx\SocialProofBase\Model\Source\Campaign\DisplayOn as DisplayOnOptions;
use MageWorx\SocialProofBase\Model\Source\Campaign\Products\AssignType as ProductsAssignTypeOptions;
use MageWorx\SocialProofBase\Model\Source\Campaign\CmsPages\AssignType as CmsPagesAssignTypeOptions;
use MageWorx\SocialProofBase\Model\Source\Campaign\Categories\AssignType as CategoriesAssignTypeOptions;
use MageWorx\SocialProofBase\Model\Campaign\RestrictionRule;
use Magento\Framework\Serialize\Serializer\Json as SerializerJson;
use Psr\Log\LoggerInterface;

class Save extends \MageWorx\SocialProofBase\Controller\Adminhtml\Campaign
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var CampaignInterfaceFactory
     */
    protected $campaignFactory;

    /**
     * @var RulesHelper
     */
    protected $rulesHelper;

    /**
     * @var SerializerJson
     */
    protected $serializerJson;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param ResultFactory $resultFactory
     * @param LoggerInterface $logger
     * @param CampaignRepositoryInterface $campaignRepository
     * @param DataPersistorInterface $dataPersistor
     * @param CampaignInterfaceFactory $campaignFactory
     * @param RulesHelper $rulesHelper
     * @param SerializerJson $serializerJson
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        LoggerInterface $logger,
        CampaignRepositoryInterface $campaignRepository,
        DataPersistorInterface $dataPersistor,
        CampaignInterfaceFactory $campaignFactory,
        RulesHelper $rulesHelper,
        SerializerJson $serializerJson
    ) {
        parent::__construct($context, $resultFactory, $logger, $campaignRepository);

        $this->dataPersistor   = $dataPersistor;
        $this->campaignFactory = $campaignFactory;
        $this->rulesHelper     = $rulesHelper;
        $this->serializerJson  = $serializerJson;
    }

    /**
     * @return ResultRedirect
     */
    public function execute(): ResultRedirect
    {
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $data = $this->getRequest()->getPostValue();
        $id   = !empty($data[CampaignInterface::CAMPAIGN_ID]) ? $data[CampaignInterface::CAMPAIGN_ID] : null;

        try {
            $data = $this->prepareData($data);

            if ($id) {
                $campaign = $this->campaignRepository->getById((int)$id);
            } else {
                unset($data[CampaignInterface::CAMPAIGN_ID]);
                $campaign = $this->campaignFactory->create();
                $campaign->isObjectNew(true);
            }

            $campaign->setData($data);

            $this->campaignRepository->save($campaign);

            $this->messageManager->addSuccessMessage(__('You saved the Campaign.'));
            $this->dataPersistor->clear('mageworx_socialproofbase_campaign');

            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath(
                    'mageworx_socialproofbase/campaign/edit',
                    [CampaignInterface::CAMPAIGN_ID => $campaign->getId()]
                );
            } else {
                $resultRedirect->setPath('mageworx_socialproofbase/campaign');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->critical($e);

            $this->dataPersistor->set('mageworx_socialproofbase_campaign', $data);

            if ($id) {
                $resultRedirect->setPath(
                    'mageworx_socialproofbase/campaign/edit',
                    [CampaignInterface::CAMPAIGN_ID => $id]
                );
            } else {
                $resultRedirect->setPath('mageworx_socialproofbase/campaign/new');
            }

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the Campaign'));
            $this->logger->critical($e);

            $this->dataPersistor->set('mageworx_socialproofbase_campaign', $data);

            if ($id) {
                $resultRedirect->setPath(
                    'mageworx_socialproofbase/campaign/edit',
                    [CampaignInterface::CAMPAIGN_ID => $id]
                );
            } else {
                $resultRedirect->setPath('mageworx_socialproofbase/campaign/new');
            }
        }

        return $resultRedirect;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareData($data): array
    {
        $this->prepareStoreIds($data);
        $this->preparePeriod($data);
        $this->prepareMaxNumberPerPage($data);
        $this->prepareDisplayOn($data);
        $this->prepareProducts($data);
        $this->prepareCategories($data);
        $this->prepareCmsPages($data);
        $this->prepareConditions($data);
        $this->preparePosition($data);
        $this->prepareContent($data);

        return $data;
    }

    /**
     * @param array $data
     */
    protected function prepareStoreIds(& $data): void
    {
        if (in_array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $data[CampaignInterface::STORE_IDS])) {
            $data[CampaignInterface::STORE_IDS] = [0];
        }
    }

    /**
     * @param array $data
     */
    protected function prepareDisplayOn(& $data): void
    {
        if ($data[CampaignInterface::DISPLAY_MODE] === DisplayModeOptions::HTML_TEXT) {
            $data[CampaignInterface::DISPLAY_ON] = [DisplayOnOptions::PRODUCT_PAGES];
        } elseif ($data[CampaignInterface::RESTRICT_TO_CURRENT_PRODUCT] || empty($data[CampaignInterface::DISPLAY_ON])) {
            $data[CampaignInterface::DISPLAY_ON] = [];
        }
    }

    /**
     * @param array $data
     */
    protected function prepareProducts(& $data): void
    {
        $data[CampaignInterface::PRODUCT_IDS] = [];

        if ($data[CampaignInterface::RESTRICT_TO_CURRENT_PRODUCT]) {
            $data[CampaignInterface::PRODUCTS_ASSIGN_TYPE] = ProductsAssignTypeOptions::ALL_PRODUCTS;
        } elseif ($data[CampaignInterface::PRODUCTS_ASSIGN_TYPE] == ProductsAssignTypeOptions::SPECIFIC_PRODUCTS
            && !empty($data['specific-products'])
            && !empty($data['specific-products'][CampaignInterface::PRODUCT_IDS])
        ) {
            $data[CampaignInterface::PRODUCT_IDS] = array_column(
                $data['specific-products'][CampaignInterface::PRODUCT_IDS],
                'id'
            );
        }

        unset($data['specific-products']);
    }

    /**
     * @param array $data
     */
    protected function prepareCategories(& $data): void
    {
        if ($data[CampaignInterface::CATEGORIES_ASSIGN_TYPE] != CategoriesAssignTypeOptions::SPECIFIC_CATEGORIES) {
            $data[CampaignInterface::CATEGORY_IDS] = [];
        }
    }

    /**
     * @param array $data
     */
    protected function prepareCmsPages(& $data): void
    {
        $data[CampaignInterface::CMS_PAGE_IDS] = [];

        if ($data[CampaignInterface::CMS_PAGES_ASSIGN_TYPE] == CmsPagesAssignTypeOptions::SPECIFIC_PAGES
            && !empty($data['specific-cms-pages'])
            && !empty($data['specific-cms-pages'][CampaignInterface::CMS_PAGE_IDS])
        ) {
            $data[CampaignInterface::CMS_PAGE_IDS] = array_column(
                $data['specific-cms-pages'][CampaignInterface::CMS_PAGE_IDS],
                'id'
            );
        }

        unset($data['specific-cms-pages']);
    }

    /**
     * @param array $data
     */
    protected function prepareConditions(& $data): void
    {
        if ($data[CampaignInterface::RESTRICT_TO_CURRENT_PRODUCT]) {
            $data[CampaignInterface::DISPLAY_ON_PRODUCTS_CONDITIONS_SERIALIZED] = null;
        } elseif ($data[CampaignInterface::PRODUCTS_ASSIGN_TYPE] == ProductsAssignTypeOptions::BY_CONDITIONS
            && !empty($data['rule']['conditions'])
        ) {
            $ruleModel = $this->rulesHelper->getDisplayOnProductsRuleModel();
            $ruleData  = [
                'conditions' => $data['rule']['conditions']
            ];
            $ruleModel->loadPost($ruleData);

            $data[CampaignInterface::DISPLAY_ON_PRODUCTS_CONDITIONS_SERIALIZED] = $this->serializerJson->serialize(
                $ruleModel->getConditions()->asArray()
            );
        } else {
            $data[CampaignInterface::DISPLAY_ON_PRODUCTS_CONDITIONS_SERIALIZED] = null;
        }

        if ($data[CampaignInterface::RESTRICT_TO_CURRENT_PRODUCT]) {
            $data[CampaignInterface::RESTRICTION_CONDITIONS_SERIALIZED] = null;
        } elseif (!empty($data['rule'][RestrictionRule::CONDITIONS_PREFIX])) {
            $ruleModel = $this->rulesHelper->getRestrictionRuleModel();
            $ruleData  = [
                'conditions' => $data['rule'][RestrictionRule::CONDITIONS_PREFIX]
            ];
            $ruleModel->loadPost($ruleData);

            $data[CampaignInterface::RESTRICTION_CONDITIONS_SERIALIZED] = $this->serializerJson->serialize(
                $ruleModel->getConditions()->asArray()
            );
        }

        unset($data['rule']);
    }

    /**
     * @param array $data
     */
    protected function preparePosition(& $data): void
    {
        $data[CampaignInterface::POSITION] = $data['position-wrapper'][CampaignInterface::POSITION];

        unset($data['position-wrapper']);
    }

    /**
     * @param array $data
     */
    protected function prepareContent(& $data): void
    {
        $data[CampaignInterface::CONTENT] = $data['templates'][CampaignInterface::CONTENT];

        unset($data['templates']);
    }

    /**
     * @param array $data
     */
    protected function preparePeriod(& $data): void
    {
        if (!$data[CampaignInterface::PERIOD] && $data[CampaignInterface::EVENT_TYPE] == EventTypeOptions::VIEWS) {
            $data[CampaignInterface::PERIOD] = 1;
        }
    }

    /**
     * @param array $data
     */
    protected function prepareMaxNumberPerPage(& $data): void
    {
        if ($data[CampaignInterface::RESTRICT_TO_CURRENT_PRODUCT]) {
            $data[CampaignInterface::MAX_NUMBER_PER_PAGE] = 1;
        }
    }
}
