<?php
namespace Webkul\Walletsystem\Model\Resolver\AddWalletAmounttoCart;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Model\Resolver\AddWalletAmounttoCart
 */
class Interceptor extends \Webkul\Walletsystem\Model\Resolver\AddWalletAmounttoCart implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Checkout\Model\Cart $cart, \Magento\Catalog\Model\Product $product, \Magento\Catalog\Model\ProductRepository $productRepository, \Magento\Framework\App\Config\Storage\WriterInterface $configWriter, \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool)
    {
        $this->___init();
        parent::__construct($scopeConfig, $cart, $product, $productRepository, $configWriter, $cacheTypeList, $cacheFrontendPool);
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
