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
namespace Webkul\Helpdesk\Model;

use Webkul\Helpdesk\Api\Data\AgentLevelInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Helpdesk AgentLevel Model
 *
 * @method \Webkul\Helpdesk\Model\ResourceModel\AgentLevel _getResource()
 * @method \Webkul\Helpdesk\Model\ResourceModel\AgentLevel getResource()
 */
class AgentLevel extends \Magento\Framework\Model\AbstractModel implements AgentLevelInterface, IdentityInterface
{
    /**
     * No route page id
     */
    public const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Helpdesk AgentLevel cache tag
     */
    public const CACHE_TAG = 'helpdesk_ticket_agent_level';

    /**
     * @var string
     */
    protected $_cacheTag = 'helpdesk_ticket_agent_level';

    /**
     * @var string
     */
    protected $_eventPrefix = 'helpdesk_ticket_agent_level';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Webkul\Helpdesk\Model\ResourceModel\AgentLevel::class);
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
            return $this->noRouteAgentLevel();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route AgentLevel
     *
     * @return \Webkul\Helpdesk\Model\AgentLevel
     */
    public function noRouteAgentLevel()
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
     * @return \Webkul\Helpdesk\Api\Data\AgentLevelInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}
