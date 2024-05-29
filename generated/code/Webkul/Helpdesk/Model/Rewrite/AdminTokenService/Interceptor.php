<?php
namespace Webkul\Helpdesk\Model\Rewrite\AdminTokenService;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Model\Rewrite\AdminTokenService
 */
class Interceptor extends \Webkul\Helpdesk\Model\Rewrite\AdminTokenService implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Integration\Model\Oauth\TokenFactory $tokenModelFactory, \Magento\User\Model\User $userModel, \Magento\Integration\Model\ResourceModel\Oauth\Token\CollectionFactory $tokenModelCollectionFactory, \Magento\Integration\Model\CredentialsValidator $validatorHelper)
    {
        $this->___init();
        parent::__construct($tokenModelFactory, $userModel, $tokenModelCollectionFactory, $validatorHelper);
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAdminAccessToken($adminId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'revokeAdminAccessToken');
        return $pluginInfo ? $this->___callPlugins('revokeAdminAccessToken', func_get_args(), $pluginInfo) : parent::revokeAdminAccessToken($adminId);
    }

    /**
     * {@inheritdoc}
     */
    public function createAdminAccessToken($username, $password)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'createAdminAccessToken');
        return $pluginInfo ? $this->___callPlugins('createAdminAccessToken', func_get_args(), $pluginInfo) : parent::createAdminAccessToken($username, $password);
    }
}
