<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Model;

use Webkul\Helpdesk\Api\Data\TagInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Helpdesk Tag Model
 *
 * @method \Webkul\Helpdesk\Model\ResourceModel\Tag _getResource()
 * @method \Webkul\Helpdesk\Model\ResourceModel\Tag getResource()
 */
class Tag extends \Magento\Framework\Model\AbstractModel implements TagInterface, IdentityInterface
{
    /**
     * No route page id
     */
    public const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Helpdesk Tag cache tag
     */
    public const CACHE_TAG = 'helpdesk_ticket_tag';

    /**
     * @var string
     */
    protected $_cacheTag = 'helpdesk_ticket_tag';

    /**
     * @var string
     */
    protected $_eventPrefix = 'helpdesk_ticket_tag';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Webkul\Helpdesk\Model\ResourceModel\Tag::class);
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
            return $this->noRouteTag();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route Tag
     *
     * @return \Webkul\Helpdesk\Model\Tag
     */
    public function noRouteTag()
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
     * @return \Webkul\Helpdesk\Api\Data\TagInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}
