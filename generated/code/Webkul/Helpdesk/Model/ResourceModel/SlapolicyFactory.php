<?php
namespace Webkul\Helpdesk\Model\ResourceModel;

/**
 * Factory class for @see \Webkul\Helpdesk\Model\ResourceModel\Slapolicy
 */
class SlapolicyFactory
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
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Webkul\\Helpdesk\\Model\\ResourceModel\\Slapolicy')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Webkul\Helpdesk\Model\ResourceModel\Slapolicy
     */
    public function create(array $data = [])
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
