<?php
namespace Tryathome\Core\Model;

use Magento\Framework\Model\AbstractModel;

class TryItem extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Tryathome\Core\Model\ResourceModel\TryItem::class);
    }

    public function getRelatedTrialProductUsers()
    {
        $resourceModel = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Tryathome\Core\Model\ResourceModel\TrialProductUser::class);
        return $resourceModel->getRelatedTrialProductUsers($this->getId());
    }
}