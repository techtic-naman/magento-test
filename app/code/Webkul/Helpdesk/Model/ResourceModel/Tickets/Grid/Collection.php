<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Helpdesk
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Model\ResourceModel\Tickets\Grid;

use Magento\Framework\Api\Search\SearchResultInterface as ApiSearchResultInterface;
use Webkul\Helpdesk\Model\ResourceModel\Tickets\Collection as TicketsCollection;
use Magento\Framework\Search\AggregationInterface as SearchAggregationInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb as ResourceModelAbstractDb;

/**
 * Webkul\Helpdesk\Model\ResourceModel\Tickets\Grid\Collection Class
 * Collection for displaying grid of Helpdesk tickets.
 */
class Collection extends TicketsCollection implements ApiSearchResultInterface
{
    /**
     * @var SearchAggregationInterface
     */
    protected $_aggregations;

    /**
     * @param EntityFactoryInterface                               $entityFactoryInterface
     * @param LoggerInterface                                      $loggerInterface
     * @param FetchStrategyInterface                               $fetchStrategyInterface
     * @param EventManagerInterface                                $eventManagerInterface
     * @param StoreManagerInterface                                $storeManagerInterface
     * @param \Magento\Backend\Model\Auth\Session                  $session
     * @param \Webkul\Helpdesk\Helper\Data                         $data
     * @param mixed|null                                           $mainTable
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $eventPrefix
     * @param mixed                                                $eventObject
     * @param mixed                                                $resourceModel
     * @param string                                               $model
     * @param null                                                 $connection
     * @param ResourceModelAbstractDb|null                         $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactoryInterface,
        LoggerInterface $loggerInterface,
        FetchStrategyInterface $fetchStrategyInterface,
        EventManagerInterface $eventManagerInterface,
        StoreManagerInterface $storeManagerInterface,
        \Magento\Backend\Model\Auth\Session $session,
        \Webkul\Helpdesk\Helper\Data $data,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
        $connection = null,
        ResourceModelAbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactoryInterface,
            $loggerInterface,
            $fetchStrategyInterface,
            $eventManagerInterface,
            $storeManagerInterface,
            $connection,
            $resource
        );
        $this->data = $data;
        $this->session = $session;
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }

    /**
     * Get aggregation
     *
     * @return SearchAggregationInterface
     */
    public function getAggregations()
    {
        return $this->_aggregations;
    }

    /**
     * Set aggregation
     *
     * @param SearchAggregationInterface $aggregationsData
     * @return $this
     */
    public function setAggregations($aggregationsData)
    {
        $this->_aggregations = $aggregationsData;
    }

    /**
     * Retrieve all ids for collection
     *
     * Backward compatibility with EAV collection.
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol(
            $this->_getAllIdsSelect($limit, $offset),
            $this->_bindParams
        );
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return $this;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null
    ) {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * RenderFiltersBefore
     */
    protected function _renderFiltersBefore()
    {
        $authSession = $this->session;
        $userId = $authSession->getUser()->getUserId();
        $role = $authSession->getUser()->getRole();
        if ($role->getRoleName()=='Agent' && !$this->data->canAgentSeeTickets()) {
            $this->getSelect()->where('to_agent ='.$userId);
        }
        parent::_renderFiltersBefore();
    }
}
