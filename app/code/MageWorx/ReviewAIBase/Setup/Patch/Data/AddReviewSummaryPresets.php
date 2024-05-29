<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\OpenAI\Api\Data\PresetGroupInterface;
use MageWorx\OpenAI\Api\Data\PresetGroupInterfaceFactory as PresetGroupFactory;
use MageWorx\OpenAI\Api\Data\PresetInterface;
use MageWorx\OpenAI\Api\Data\PresetInterfaceFactory as PresetFactory;
use MageWorx\OpenAI\Model\ResourceModel\Presets\Preset as PresetResource;
use MageWorx\OpenAI\Model\ResourceModel\Presets\PresetGroup as PresetGroupResource;
use Psr\Log\LoggerInterface;

class AddReviewSummaryPresets implements DataPatchInterface,
                                         PatchRevertableInterface
{
    protected ModuleDataSetupInterface $setup;
    private LoggerInterface            $logger;
    private PresetFactory              $presetFactory;
    private PresetResource             $presetResource;
    private PresetGroupFactory         $presetGroupFactory;
    private PresetGroupResource        $presetGroupResource;
    private StoreManagerInterface      $storeManager;
    private ScopeConfigInterface       $scopeConfig;

    public function __construct(
        ModuleDataSetupInterface $setup,
        LoggerInterface          $logger,
        PresetFactory            $presetFactory,
        PresetResource           $presetResource,
        PresetGroupFactory       $presetGroupFactory,
        PresetGroupResource      $presetGroupResource,
        StoreManagerInterface    $storeManager,
        ScopeConfigInterface     $scopeConfig
    ) {
        $this->logger              = $logger;
        $this->presetFactory       = $presetFactory;
        $this->presetResource      = $presetResource;
        $this->presetGroupFactory  = $presetGroupFactory;
        $this->presetGroupResource = $presetGroupResource;
        $this->storeManager        = $storeManager;
        $this->scopeConfig         = $scopeConfig;
        $this->setup               = $setup;
    }

    public function apply(): DataPatchInterface
    {
        $this->setup->startSetup();
        $group = $this->addPresetGroup();
        $this->addPresetsToGroup($group);
        $this->setup->endSetup();

        return $this;
    }

    /**
     * Add default preset group for review_summary presets.
     * The group code is 'review_summary'.
     *
     * @return PresetGroupInterface
     * @throws AlreadyExistsException
     */
    private function addPresetGroup(): PresetGroupInterface
    {
        $group = $this->presetGroupFactory->create();

        try {
            $this->presetGroupResource->load($group, 'review_summary', PresetGroupInterface::CODE);
            if (!$group->getId()) {
                // If preset group does not exist - just load it
                $group->setCode('review_summary');
                $group->setName('Review Summary');
                $this->presetGroupResource->save($group);
            }
        } catch (\Exception $e) {
            $this->logger->error('Error adding preset group: ' . $e->getMessage());
        }

        return $group;
    }

    /**
     * Add presets to a preset group.
     *
     * @param PresetGroupInterface $group The preset group to which presets will be added.
     *
     * @return void
     */
    private function addPresetsToGroup(PresetGroupInterface $group): void
    {
        $reviewSummaryGroupId = (int)$group->getGroupId();
        $presetsData          = [
            [
                'code'     => 'detailed',
                'name'     => 'Detailed',
                'group_id' => $reviewSummaryGroupId,
                'content'  => "Generate a concise summary of customer reviews. Focus on the following aspects:
Pros and Cons: Outline the main positive and negative points mentioned by customers.
Product Quality: Summarize what customers say about the durability, functionality, and overall build of the product.
Quality of Support: Describe the customer experiences with after-sales support, including response times and resolution effectiveness.
Shipping Details: Provide an overview of customer feedback on shipping efficiency, packaging quality, and delivery experience.
Location-Specific Feedback: If any reviews mention specific locations, include how customer experiences may vary based on geographic regions.
Other Characteristics: Note any additional aspects frequently highlighted by customers, such as usability, design, or value for money.
The summary should be balanced, capturing a range of customer experiences and viewpoints to provide a comprehensive overview of the product based on user reviews.
Keep it up to {{max_length}} characters. Format text as HTML.
",
                'store_id' => 0
            ],
            [
                'code'     => 'short',
                'name'     => 'Short',
                'group_id' => $reviewSummaryGroupId,
                'content'  => 'I have collected several customer reviews for our product and I need a concise and informative summary. The reviews mention various aspects like quality, performance, price, and customer satisfaction. Please generate a summary that captures the key points and overall sentiment from these reviews.',
                'store_id' => 0
            ],
            [
                'code'     => 'pros_and_cons',
                'name'     => 'Pros and Cons',
                'group_id' => $reviewSummaryGroupId,
                'content'  => 'I have collected several customer reviews for our product and I need a concise and informative summary. The reviews mention various aspects like quality, performance, price, and customer satisfaction. Please generate a summary of these reviews and present it as a bullet list of pros and cons.',
                'store_id' => 0
            ]
        ];

        $existingPresets = $this->getCurrentCustomPresets($reviewSummaryGroupId);
        $presetsData     = array_merge($presetsData, $existingPresets);

        // Insert all presets in table, on duplicate code group and store update, using connection
        $connection = $this->setup->getConnection();
        $tableName  = $this->setup->getTable('mageworx_openai_presets');
        $connection->insertOnDuplicate($tableName, $presetsData, ['content']);
    }

    /**
     * Retrieves the current custom presets for a given group ID.
     *
     * This method retrieves all the stores using the store manager. Then, it iterates
     * through each store and gets the custom content value from the scope configuration.
     * If the custom content is not empty, it creates an array with the preset data and
     * adds it to the $customPresets array. Finally, the method returns the array of
     * custom presets.
     *
     * @param int $groupId The ID of the preset group.
     * @return array The array of current custom presets.
     */
    private function getCurrentCustomPresets(int $groupId): array
    {
        $customPresets  = [];
        $stores         = $this->storeManager->getStores(true);
        $defaultContent = $this->scopeConfig->getValue(
            'review_ai/main/content',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        foreach ($stores as $store) {
            $storeId      = (int)$store->getId();
            $storeContent = $this->scopeConfig->getValue(
                'review_ai/main/content',
                ScopeInterface::SCOPE_STORES,
                $storeId
            );

            if ($storeContent && $storeContent !== $defaultContent) {
                $customPresets[] = [
                    'code'     => 'custom_' . $storeId,
                    'name'     => 'Custom (Store ID: ' . $storeId . ')',
                    'group_id' => $groupId,
                    'content'  => $storeContent,
                    'store_id' => $storeId
                ];
            }
        }

        if ($defaultContent) {
            $customPresets[] = [
                'code'     => 'custom',
                'name'     => 'Custom (Default)',
                'group_id' => $groupId,
                'content'  => $defaultContent,
                'store_id' => 0
            ];
        }

        return $customPresets;
    }

    /**
     * Reverts the changes made by the AddReviewSummaryPresets.
     *
     * This method retrieves a preset group entity with the code 'review_summary'
     * using the preset group factory and preset group resource. If the group
     * exists, it is deleted using the preset group resource. Any errors that occur
     * during the process are logged.
     *
     * @return void
     */
    public function revert(): void
    {
        try {
            $group = $this->presetGroupFactory->create();
            $this->presetGroupResource->load($group, 'review_summary', PresetGroupInterface::CODE);

            if ($group->getId()) {
                $this->presetGroupResource->delete($group);
            }
        } catch (\Exception $e) {
            $this->logger->error('Error reverting AddReviewSummaryPresets: ' . $e->getMessage());
        }
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
