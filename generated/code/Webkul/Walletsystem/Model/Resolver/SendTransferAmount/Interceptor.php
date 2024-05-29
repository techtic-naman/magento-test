<?php
namespace Webkul\Walletsystem\Model\Resolver\SendTransferAmount;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Model\Resolver\SendTransferAmount
 */
class Interceptor extends \Webkul\Walletsystem\Model\Resolver\SendTransferAmount implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Webkul\Walletsystem\Helper\Data $walletHelper, \Webkul\Walletsystem\Helper\Mail $walletMail, \Webkul\Walletsystem\Model\WalletTransferData $walletTransfer, \Magento\Framework\Encryption\EncryptorInterface $encryptor, \Magento\Framework\Stdlib\DateTime\DateTime $date, \Webkul\Walletsystem\Model\WalletUpdateData $walletUpdate)
    {
        $this->___init();
        parent::__construct($walletHelper, $walletMail, $walletTransfer, $encryptor, $date, $walletUpdate);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(\Magento\Framework\GraphQl\Config\Element\Field $field, $context, \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'resolve');
        return $pluginInfo ? $this->___callPlugins('resolve', func_get_args(), $pluginInfo) : parent::resolve($field, $context, $info, $value, $args);
    }
}
