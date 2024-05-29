<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\ResourceModel\AbstractResource;
use MageWorx\RewardPoints\Api\Data\RuleInterface;
use Magento\Framework\Stdlib\DateTime;

class Rule extends AbstractResource
{
    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = [];

    /**
     * @var array
     */
    protected $customerGroupIds = [];

    /**
     * @var array
     */
    protected $websiteIds = [];

    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @var \Magento\Framework\EntityManager\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    private $metadataPool;

    /**
     * Rule constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\EntityManager\EntityManager $entityManager
     * @param Rule\AssociatedEntityMap $associatedEntityMapInstance
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\EntityManager\EntityManager $entityManager,
        \MageWorx\RewardPoints\Model\ResourceModel\Rule\AssociatedEntityMap $associatedEntityMapInstance,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        $connectionName = null
    ) {
        $this->string                 = $string;
        $this->entityManager          = $entityManager;
        $this->_associatedEntitiesMap = $associatedEntityMapInstance->getData();
        $this->serializer             = $serializer;
        $this->metadataPool           = $metadataPool;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_rewardpoints_rule', 'rule_id');
    }

    /**
     * Load an object
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $this->entityManager->load($object, $value);

        return $this;
    }

    /**
     * @param \MageWorx\RewardPoints\Api\Data\PointTransactionInterface $pointTransaction
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateRuleUsage(\MageWorx\RewardPoints\Api\Data\PointTransactionInterface $pointTransaction)
    {
        if ($pointTransaction->getRuleId() && $pointTransaction->getPointsDelta() > 0) {
            $pointsDelta = $pointTransaction->getPointsDelta();
            $operator = $pointsDelta >= 0 ? '+' : '-';

            $bind = [
                'points_generated' => new \Zend_Db_Expr('`points_generated`' . $operator . abs($pointsDelta)),
                'times_used'       => new \Zend_Db_Expr('`times_used` + 1')
            ];

            $connection = $this->getConnection();
            $connection->update(
                $this->getMainTable(),
                $bind,
                ['rule_id=?' => (int)$pointTransaction->getRuleId()]
            );
        }

        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function _beforeSave(AbstractModel $object)
    {
        $this->getActiveAttributes();

        if ($object->getFromdDate() instanceof \DateTimeInterface) {
            $object->setFromdDate($object->getFromdDate()->format(DateTime::DATETIME_PHP_FORMAT));
        }

        return parent::_beforeSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->entityManager->save($object);

        return $this;
    }

    /**
     * Bind reward rule to customer group(s) and website(s).
     * Save rule's associated store labels.
     * Save product attributes used in rule.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        if ($object->hasStoreLabels()) {
            $this->saveStoreLabels($object->getId(), $object->getStoreLabels());
        }

        // Save product attributes used in rule
        $ruleProductAttributes = array_merge(
            $this->getProductAttributes($this->serializer->serialize($object->getConditions()->asArray())),
            $this->getProductAttributes($this->serializer->serialize($object->getActions()->asArray()))
        );

        if (count($ruleProductAttributes)) {
            $this->setActualProductAttributes($object, $ruleProductAttributes);
        }

        return parent::_afterSave($object);
    }

    /**
     * Delete the object
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);

        return $this;
    }

    /**
     * Save rule labels for different store views
     *
     * @param int $ruleId
     * @param array $labels
     * @throws \Exception
     * @return $this
     */
    public function saveStoreLabels($ruleId, $labels)
    {
        $deleteByStoreIds = [];
        $connection       = $this->getConnection();

        $data = [];
        foreach ($labels as $storeId => $label) {
            if ($this->string->strlen($label)) {
                $data[] = ['rule_id' => $ruleId, 'store_id' => $storeId, 'label' => $label];
            } else {
                $deleteByStoreIds[] = $storeId;
            }
        }

        $connection->beginTransaction();
        try {
            if (!empty($data)) {
                $connection->insertOnDuplicate(
                    $this->getTable('mageworx_rewardpoints_rule_label'),
                    $data,
                    ['label']
                );
            }

            if (!empty($deleteByStoreIds)) {
                $connection->delete(
                    $this->getTable('mageworx_rewardpoints_rule_label'),
                    ['rule_id=?' => $ruleId, 'store_id IN (?)' => $deleteByStoreIds]
                );
            }
        } catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
        $connection->commit();

        return $this;
    }

    /**
     * Get all existing rule labels
     *
     * @param int $ruleId
     * @return array
     */
    public function getStoreLabels($ruleId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('mageworx_rewardpoints_rule_label'),
            ['store_id', 'label']
        )->where(
            'rule_id = :rule_id'
        );

        return $this->getConnection()->fetchPairs($select, [':rule_id' => $ruleId]);
    }

    /**
     * Get rule label by specific store id
     *
     * @param int $ruleId
     * @param int $storeId
     * @return string
     */
    public function getStoreLabel($ruleId, $storeId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('mageworx_rewardpoints_rule_label'),
            'label'
        )->where(
            'rule_id = :rule_id'
        )->where(
            'store_id IN(0, :store_id)'
        )->order(
            'store_id DESC'
        );

        return $this->getConnection()->fetchOne($select, [':rule_id' => $ruleId, ':store_id' => $storeId]);
    }

    /**
     * Return codes of all product attributes currently used in reward rules
     *
     * @todo will use in plugin for adding attribute to quote item collection for recalculate
     * @return array
     */
    public function getActiveAttributes()
    {
        $connection = $this->getConnection();
        $select     = $connection->select()->from(
            ['a' => $this->getTable('mageworx_rewardpoints_rule_product_attribute')],
            'ea.attribute_code'
        )->joinInner(
            ['ea' => $this->getTable('eav_attribute')],
            'ea.attribute_id = a.attribute_id',
            []
        )->distinct();

        return $connection->fetchAll($select);
    }

    /**
     * Save product attributes currently used in conditions and actions of rule
     *
     * @todo we will use it in plugin for adding attribute to quote item collection for recalculate
     * @param \MageWorx\RewardPoints\Model\Rule $rule
     * @param mixed $attributes
     * @return $this
     */
    public function setActualProductAttributes($rule, $attributes)
    {
        $connection = $this->getConnection();
        $metadata   = $this->metadataPool->getMetadata(RuleInterface::class);

        if ($rule->getData($metadata->getLinkField())) {

            $connection->delete(
                $this->getTable('mageworx_rewardpoints_rule_product_attribute'),
                [$metadata->getLinkField() . '=?' => $rule->getData($metadata->getLinkField())]
            );
        }

        //Getting attribute IDs for attribute codes

        $select = $this->getConnection()->select()->from(
            ['a' => $this->getTable('eav_attribute')],
            ['a.attribute_id']
        )->where(
            'a.attribute_code IN (?)',
            [$attributes]
        );

        $attributeIds = $this->getConnection()->fetchCol($select, ['attribute_id']);

        if ($attributeIds) {
            $data = [];
            foreach ($rule->getCustomerGroupIds() as $customerGroupId) {
                foreach ($rule->getWebsiteIds() as $websiteId) {
                    foreach ($attributeIds as $attribute) {
                        $data[] = [
                            $metadata->getLinkField() => $rule->getData($metadata->getLinkField()),
                            'website_id'              => $websiteId,
                            'customer_group_id'       => $customerGroupId,
                            'attribute_id'            => $attribute,
                        ];
                    }
                }
            }

            $connection->insertMultiple(
                $this->getTable('mageworx_rewardpoints_rule_product_attribute'),
                $data
            );
        }

        return $this;
    }

    /**
     * Collect all product attributes used in serialized rule's action or condition
     *
     * @param string $serializedString
     * @return array
     */
    public function getProductAttributes($serializedString)
    {
        // we need 4 backslashes to match 1 in regexp, see http://www.php.net/manual/en/regexp.reference.escape.php
        preg_match_all(
            '~"Magento\\\\\\\\SalesRule\\\\\\\\Model\\\\\\\\Rule\\\\\\\\Condition\\\\\\\\Product","attribute":"(.*?)"~',
            $serializedString,
            $matches
        );

        // we always have $matches like [[],[]]
        return array_values($matches[1]);
    }
}
