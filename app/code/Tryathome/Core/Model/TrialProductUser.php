<?php
namespace Tryathome\Core\Model;

use Magento\Framework\Model\AbstractModel;

class TrialProductUser extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Tryathome\Core\Model\ResourceModel\TrialProductUser::class);
    }
}