<?php
namespace Meetanshi\Extensions\Controller\Adminhtml\Index\SendFeedback;

/**
 * Interceptor class for @see \Meetanshi\Extensions\Controller\Adminhtml\Index\SendFeedback
 */
class Interceptor extends \Meetanshi\Extensions\Controller\Adminhtml\Index\SendFeedback implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Email\Model\Template\SenderResolver $senderResolver)
    {
        $this->___init();
        parent::__construct($context, $transportBuilder, $senderResolver);
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
