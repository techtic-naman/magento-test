<?php
namespace MageWorx\RewardPoints\Model\Rule\Condition\Product;

/**
 * Factory class for @see \MageWorx\RewardPoints\Model\Rule\Condition\Product\Combine
 */
class CombineFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\MageWorx\\RewardPoints\\Model\\Rule\\Condition\\Product\\Combine')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \MageWorx\RewardPoints\Model\Rule\Condition\Product\Combine
     */
    public function create(array $data = [])
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}