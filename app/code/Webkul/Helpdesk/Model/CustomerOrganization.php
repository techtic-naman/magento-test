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
namespace Webkul\Helpdesk\Model;

use Webkul\Helpdesk\Api\Data\CustomerOrganizationInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Helpdesk CustomerOrganization Model
 *
 * @method \Webkul\Helpdesk\Model\ResourceModel\CustomerOrganization _getResource()
 * @method \Webkul\Helpdesk\Model\ResourceModel\CustomerOrganization getResource()
 */
class CustomerOrganization extends \Magento\Framework\Model\AbstractModel implements
    CustomerOrganizationInterface,
    IdentityInterface
{
    /**
     * No route page id
     */
    public const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Helpdesk CustomerOrganization cache tag
     */
    public const CACHE_TAG = 'helpdesk_customer_organization';

    /**
     * @var string
     */
    protected $_cacheTag = 'helpdesk_customer_organization';

    /**
     * @var string
     */
    protected $_eventPrefix = 'helpdesk_customer_organization';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Webkul\Helpdesk\Model\ResourceModel\CustomerOrganization::class);
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
            return $this->noRouteCustomerOrganization();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route CustomerOrganization
     *
     * @return \Webkul\Helpdesk\Model\CustomerOrganization
     */
    public function noRouteCustomerOrganization()
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
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID
     *
     * @param  int $id
     * @return \Webkul\Helpdesk\Api\Data\CustomerOrganizationInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}
