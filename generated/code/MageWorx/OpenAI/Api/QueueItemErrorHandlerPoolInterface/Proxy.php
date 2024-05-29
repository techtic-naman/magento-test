<?php
namespace MageWorx\OpenAI\Api\QueueItemErrorHandlerPoolInterface;

/**
 * Proxy class for @see \MageWorx\OpenAI\Api\QueueItemErrorHandlerPoolInterface
 */
class Proxy implements \MageWorx\OpenAI\Api\QueueItemErrorHandlerPoolInterface, \Magento\Framework\ObjectManager\NoninterceptableInterface
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Proxied instance name
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Proxied instance
     *
     * @var \MageWorx\OpenAI\Api\QueueItemErrorHandlerPoolInterface
     */
    protected $_subject = null;

    /**
     * Instance shareability flag
     *
     * @var bool
     */
    protected $_isShared = null;

    /**
     * Proxy constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     * @param bool $shared
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\MageWorx\\OpenAI\\Api\\QueueItemErrorHandlerPoolInterface', $shared = true)
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
        $this->_isShared = $shared;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return ['_subject', '_isShared', '_instanceName'];
    }

    /**
     * Retrieve ObjectManager from global scope
     */
    public function __wakeup()
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Clone proxied instance
     */
    public function __clone()
    {
        $this->_subject = clone $this->_getSubject();
    }

    /**
     * Get proxied instance
     *
     * @return \MageWorx\OpenAI\Api\QueueItemErrorHandlerPoolInterface
     */
    protected function _getSubject()
    {
        if (!$this->_subject) {
            $this->_subject = true === $this->_isShared
                ? $this->_objectManager->get($this->_instanceName)
                : $this->_objectManager->create($this->_instanceName);
        }
        return $this->_subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getByType(string $type) : array
    {
        return $this->_getSubject()->getByType($type);
    }

    /**
     * {@inheritdoc}
     */
    public function process(string $type, \MageWorx\OpenAI\Api\Data\QueueItemInterface $queueItem) : bool
    {
        return $this->_getSubject()->process($type, $queueItem);
    }
}
