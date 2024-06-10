<?php
namespace Webkul\Helpdesk\Model\Mail\TransportBuilder;

/**
 * Interceptor class for @see \Webkul\Helpdesk\Model\Mail\TransportBuilder
 */
class Interceptor extends \Webkul\Helpdesk\Model\Mail\TransportBuilder implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Mail\Template\FactoryInterface $templateFactory, \Magento\Framework\Mail\MessageInterface $message, \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Framework\Mail\TransportInterfaceFactory $mailTransportFactory, ?\Magento\Framework\Mail\MessageInterfaceFactory $messageFactory = null, ?\Magento\Framework\Mail\EmailMessageInterfaceFactory $emailMessageInterfaceFactory = null, ?\Magento\Framework\Mail\MimeMessageInterfaceFactory $mimeMessageInterfaceFactory = null, ?\Magento\Framework\Mail\MimePartInterfaceFactory $mimePartInterfaceFactory = null, ?\Magento\Framework\Mail\AddressConverter $addressConverter = null)
    {
        $this->___init();
        parent::__construct($templateFactory, $message, $senderResolver, $objectManager, $mailTransportFactory, $messageFactory, $emailMessageInterfaceFactory, $mimeMessageInterfaceFactory, $mimePartInterfaceFactory, $addressConverter);
    }

    /**
     * {@inheritdoc}
     */
    public function setBodyHtml($html, $charset = null, $encoding = 'quoted-printable')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setBodyHtml');
        return $pluginInfo ? $this->___callPlugins('setBodyHtml', func_get_args(), $pluginInfo) : parent::setBodyHtml($html, $charset, $encoding);
    }

    /**
     * {@inheritdoc}
     */
    public function addAttachment($content, $attachment)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addAttachment');
        return $pluginInfo ? $this->___callPlugins('addAttachment', func_get_args(), $pluginInfo) : parent::addAttachment($content, $attachment);
    }

    /**
     * {@inheritdoc}
     */
    public function addHeader($name, $value, $append = false)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addHeader');
        return $pluginInfo ? $this->___callPlugins('addHeader', func_get_args(), $pluginInfo) : parent::addHeader($name, $value, $append);
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeContentType($filename)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMimeContentType');
        return $pluginInfo ? $this->___callPlugins('getMimeContentType', func_get_args(), $pluginInfo) : parent::getMimeContentType($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderEncoding()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getHeaderEncoding');
        return $pluginInfo ? $this->___callPlugins('getHeaderEncoding', func_get_args(), $pluginInfo) : parent::getHeaderEncoding();
    }

    /**
     * {@inheritdoc}
     */
    public function getCharset()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCharset');
        return $pluginInfo ? $this->___callPlugins('getCharset', func_get_args(), $pluginInfo) : parent::getCharset();
    }

    /**
     * {@inheritdoc}
     */
    public function addCc($address, $name = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addCc');
        return $pluginInfo ? $this->___callPlugins('addCc', func_get_args(), $pluginInfo) : parent::addCc($address, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function addTo($address, $name = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addTo');
        return $pluginInfo ? $this->___callPlugins('addTo', func_get_args(), $pluginInfo) : parent::addTo($address, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function addBcc($address)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addBcc');
        return $pluginInfo ? $this->___callPlugins('addBcc', func_get_args(), $pluginInfo) : parent::addBcc($address);
    }

    /**
     * {@inheritdoc}
     */
    public function setReplyTo($email, $name = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setReplyTo');
        return $pluginInfo ? $this->___callPlugins('setReplyTo', func_get_args(), $pluginInfo) : parent::setReplyTo($email, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function setFrom($from)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setFrom');
        return $pluginInfo ? $this->___callPlugins('setFrom', func_get_args(), $pluginInfo) : parent::setFrom($from);
    }

    /**
     * {@inheritdoc}
     */
    public function setFromByScope($from, $scopeId = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setFromByScope');
        return $pluginInfo ? $this->___callPlugins('setFromByScope', func_get_args(), $pluginInfo) : parent::setFromByScope($from, $scopeId);
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateIdentifier($templateIdentifier)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setTemplateIdentifier');
        return $pluginInfo ? $this->___callPlugins('setTemplateIdentifier', func_get_args(), $pluginInfo) : parent::setTemplateIdentifier($templateIdentifier);
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateModel($templateModel)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setTemplateModel');
        return $pluginInfo ? $this->___callPlugins('setTemplateModel', func_get_args(), $pluginInfo) : parent::setTemplateModel($templateModel);
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateVars($templateVars)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setTemplateVars');
        return $pluginInfo ? $this->___callPlugins('setTemplateVars', func_get_args(), $pluginInfo) : parent::setTemplateVars($templateVars);
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateOptions($templateOptions)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setTemplateOptions');
        return $pluginInfo ? $this->___callPlugins('setTemplateOptions', func_get_args(), $pluginInfo) : parent::setTemplateOptions($templateOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransport()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTransport');
        return $pluginInfo ? $this->___callPlugins('getTransport', func_get_args(), $pluginInfo) : parent::getTransport();
    }
}
