<?php
namespace MageWorx\RewardPoints\Model\Config\Backend\UpdateExpirationDates;

/**
 * Interceptor class for @see \MageWorx\RewardPoints\Model\Config\Backend\UpdateExpirationDates
 */
class Interceptor extends \MageWorx\RewardPoints\Model\Config\Backend\UpdateExpirationDates implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\MageWorx\RewardPoints\Helper\Data $helperData, \MageWorx\RewardPoints\Helper\ExpirationDate $helperExpirationDate, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configCollectionFactory, \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\CollectionFactory $customerBalanceCollectionFactory, \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository, \Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\App\Config\ScopeConfigInterface $config, \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, \Magento\Framework\Model\ResourceModel\AbstractResource $resource, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection, array $data = [])
    {
        $this->___init();
        parent::__construct($helperData, $helperExpirationDate, $storeManager, $configCollectionFactory, $customerBalanceCollectionFactory, $customerBalanceRepository, $context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'afterSave');
        return $pluginInfo ? $this->___callPlugins('afterSave', func_get_args(), $pluginInfo) : parent::afterSave();
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'save');
        return $pluginInfo ? $this->___callPlugins('save', func_get_args(), $pluginInfo) : parent::save();
    }
}
