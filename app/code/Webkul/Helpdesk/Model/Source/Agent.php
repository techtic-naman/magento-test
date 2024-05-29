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
namespace Webkul\Helpdesk\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 */
class Agent implements OptionSourceInterface
{
    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $adminUserFactory;

    /**
     * Constructor
     *
     * @param \Magento\User\Model\UserFactory $adminUserFactory
     */
    public function __construct(\Magento\User\Model\UserFactory $adminUserFactory)
    {
        $this->adminUserFactory = $adminUserFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $userColl = $this->adminUserFactory->create()->getCollection()
            ->addFieldToFilter("is_active", ["neq"=>0]);

        $options = [];
        foreach ($userColl as $key => $user) {
            $options[] = [
                'label' => $user['firstname']." ".$user['lastname'],
                'value' => $user['user_id'],
            ];
        }
        return $options;
    }
}
