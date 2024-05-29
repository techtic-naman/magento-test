<?php
namespace Webkul\Walletsystem\Model\Plugin\AllWishlist;

/**
 * Interceptor class for @see \Webkul\Walletsystem\Model\Plugin\AllWishlist
 */
class Interceptor extends \Webkul\Walletsystem\Model\Plugin\AllWishlist implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Webkul\Walletsystem\Helper\Data $walletHelper, \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Framework\App\Request\Http $request, \Magento\Framework\Controller\ResultFactory $resultFactory, \Magento\Framework\UrlInterface $urlInterface, \Magento\Framework\Registry $registry, \Magento\Framework\App\Action\Context $context, \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Wishlist\Model\ItemCarrier $itemCarrier)
    {
        $this->___init();
        parent::__construct($walletHelper, $messageManager, $checkoutSession, $request, $resultFactory, $urlInterface, $registry, $context, $wishlistProvider, $formKeyValidator, $itemCarrier);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        return $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute();
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        return $pluginInfo ? $this->___callPlugins('dispatch', func_get_args(), $pluginInfo) : parent::dispatch($request);
    }
}
