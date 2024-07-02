<?php
namespace Tryathome\Core\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class TrialProductUser extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('trial_product_user', 'trial_id'); // Assuming 'trial_id' is the primary key
    }

    public function getRelatedTrialProductUsers($tryId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable())
            ->where('try_id = ?', $tryId);

        return $connection->fetchAll($select);
    }
}