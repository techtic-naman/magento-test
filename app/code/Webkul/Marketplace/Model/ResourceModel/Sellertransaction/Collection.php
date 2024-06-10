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
namespace Webkul\Marketplace\Model\ResourceModel\Sellertransaction;

use \Webkul\Marketplace\Model\ResourceModel\AbstractCollection;

/**
 * Webkul Marketplace ResourceModel Sellertransaction collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\Marketplace\Model\Sellertransaction::class,
            \Webkul\Marketplace\Model\ResourceModel\Sellertransaction::class
        );
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}
