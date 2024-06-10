<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Mpmembership
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Mpmembership\Model;

use Webkul\Mpmembership\Api\Data\TransactionInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Transaction Model.
 *
 * @method \Webkul\Mpmembership\Model\ResourceModel\Transaction _getResource()
 * @method \Webkul\Mpmembership\Model\ResourceModel\Transaction getResource()
 */
class Transaction extends \Magento\Catalog\Model\AbstractModel implements
    IdentityInterface,
    TransactionInterface
{
    /**
     * No route page id.
     */
    public const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Membership Transaction cache tag
     */
    public const CACHE_TAG = 'membership_transaction';

    /**
     * Check types
     */
    public const TIME_AND_PRODUCTS = 0;

    public const TIME = 1;

    public const PRODUCTS = 2;

    /**
     * Transaction types
     */
    public const PENDING = 0;

    public const EXPIRED = 1;

    public const VALID = 2;

    public const TIME_EXPIRED = 3;

    public const PRODUCT_LIMIT_COMPLETED = 4;

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var string
     */
    protected $_eventPrefix = 'membership_transaction';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Webkul\Mpmembership\Model\ResourceModel\Transaction::class);
    }

    /**
     * Load object data
     *
     * @param  int|null $id
     * @param  string   $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteTransaction();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route Transaction
     *
     * @return \Webkul\Mpmembership\Model\Transaction
     */
    public function noRouteTransaction()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Webkul\Mpmembership\Api\Data\TransactionInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}
