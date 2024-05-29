<?php
namespace MageWorx\XReviewBase\Controller\Adminhtml\Review\Gallery\Upload;

/**
 * Interceptor class for @see \MageWorx\XReviewBase\Controller\Adminhtml\Review\Gallery\Upload
 */
class Interceptor extends \MageWorx\XReviewBase\Controller\Adminhtml\Review\Gallery\Upload implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Controller\Result\RawFactory $resultRawFactory, \Magento\Framework\Serialize\Serializer\Json $serializer, \MageWorx\XReviewBase\Model\ImageUploader $imageUploader)
    {
        $this->___init();
        parent::__construct($context, $resultRawFactory, $serializer, $imageUploader);
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
