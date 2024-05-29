<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Helpdesk
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Helpdesk\Ui\Component\Listing\Columns\Report;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use \Webkul\Helpdesk\Model\TicketsFactory;
use \Webkul\Helpdesk\Helper\Data as HelperData;
use \Webkul\Helpdesk\Model\ThreadFactory;

/**
 * Class Helpdesk Actions.
 */
class CustomerAvgResponse extends Column
{
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var TicketsFactory
     */
    protected $_ticketsFactory;

    /**
     * @var ThreadFactory
     */
    protected $_threadFactory;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param TicketsFactory     $ticketsFactory
     * @param HelperData         $helperData
     * @param ThreadFactory      $threadFactory
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        TicketsFactory $ticketsFactory,
        HelperData $helperData,
        ThreadFactory $threadFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_ticketsFactory = $ticketsFactory;
        $this->_threadFactory = $threadFactory;
        $this->_helperData = $helperData;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $indexName = $this->getData('name');
                switch ($indexName) {
                
                    case 'avg_response_time':
                        $resolveStatus = $this->_helperData->getConfigResolveStatus();
                        $closeStatus = $this->_helperData->getConfigCloseStatus();
                        $collection = $this->_ticketsFactory->create()->getCollection()
                        ->addFieldToFilter("customer_id", ["eq"=>$item['entity_id']])
                        ->addFieldToFilter(
                            ['status', 'status'],
                            [
                                                            ['eq'=>$resolveStatus],
                                                            ['eq'=>$closeStatus]
                                                        ]
                        );
                        $resolveTime = 0;
                        $count = 0;
                        $count = $this->avgResponseTime($collection, $resolveTime, $count);
                        $avgResponse = json_decode($count);
                        if (!$avgResponse[1]) {
                            $avgResponse[1] = 1;
                        }
                        $item[$this->getData('name')] = round($avgResponse[0] / $avgResponse[1], 2)." ".__("Seconds");
                        break;

                }
            }
        }
        return $dataSource;
    }

    /**
     * Return Average response time
     *
     * @param object $collection
     * @param string $resolveTime
     * @param int $count
     * @return array
     */
    public function avgResponseTime($collection, $resolveTime, $count)
    {
        foreach ($collection as $key => $ticket) {
            $firstThread = $this->_threadFactory->create()->getCollection()
                ->addFieldToFilter("ticket_id", ["eq"=>$ticket->getId()])
                ->addFieldToFilter(
                    ['thread_type', 'thread_type'],
                    [
                                                        ['eq'=>'create'],
                                                        ['eq'=>'reply']
                                                    ]
                )
                ->getFirstItem();

            $customerTime = 0;
            $fisrtTime = 0;
            $lastTime = 0;
            if ($firstThread->getId()) {
                $fisrtTime = strtotime($firstThread->getCreatedAt());
            }

            $lastThread = $this->_threadFactory->create()->getCollection()
                ->addFieldToFilter("ticket_id", ["eq"=>$ticket->getId()])
                ->addFieldToFilter(
                    ['thread_type', 'thread_type'],
                    [
                                            ['eq'=>'create'],
                                            ['eq'=>'reply']
                                        ]
                )
                ->getLastItem();
            if ($lastThread->getId()) {
                $lastTime = strtotime($lastThread->getCreatedAt());
            }
            $remainingSeconds = ($lastTime - $fisrtTime);
            if ($remainingSeconds > 0) {
                $resolveTime = $resolveTime + $remainingSeconds;
                $count++;
            }
        }
        return json_encode([$resolveTime,$count]);
    }
}