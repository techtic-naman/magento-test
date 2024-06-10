<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Model;

use Webkul\Marketplace\Model\ResourceModel\Controllers\CollectionFactory;

class ControllersRepository implements \Webkul\Marketplace\Api\ControllersRepositoryInterface
{
    /**
     * @var ControllersFactory
     */
    protected $_controllersFactory;

    /**
     * @var Controllers[]
     */
    protected $_instancesById = [];

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param ControllersFactory    $controllersFactory
     * @param CollectionFactory     $collectionFactory
     */
    public function __construct(
        ControllersFactory $controllersFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->_controllersFactory = $controllersFactory;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * Get by id
     *
     * @param int $controllersId
     * @return array
     */
    public function getById($controllersId)
    {
        $controllersData = $this->_controllersFactory->create();
        /* @var \Webkul\Marketplace\Model\ResourceModel\Controllers\Collection $controllersData */
        $controllersData->load($controllersId);
        if (!$controllersData->getId()) {
            $this->_instancesById[$controllersId] = $controllersData;
        }
        $this->_instancesById[$controllersId] = $controllersData;

        return $this->_instancesById[$controllersId];
    }

    /**
     * Get controllers by module nme
     *
     * @param string $moduleName
     * @return collection
     */
    public function getByModuleName($moduleName = null)
    {
        $controllersCollection = $this->_collectionFactory->create()
                ->addFieldToFilter('module_name', $moduleName);
        $controllersCollection->load();

        return $controllersCollection;
    }

    /**
     * Get by path
     *
     * @param string $controllerPath
     * @return \Webkul\Marketplace\Model\ResourceModel\Controllers\Collection
     */
    public function getByPath($controllerPath = null)
    {
        $controllersCollection = $this->_collectionFactory->create()
                ->addFieldToFilter('controller_path', $controllerPath);
        $controllersCollection->load();

        return $controllersCollection;
    }

   /**
    * Get list
    *
    * @return \Webkul\Marketplace\Model\ResourceModel\Controllers\Collection
    */
    public function getList()
    {
        /** @var \Webkul\Marketplace\Model\ResourceModel\Controllers\Collection $collection */
        $collection = $this->_collectionFactory->create();
        $collection->load();

        return $collection;
    }
}
