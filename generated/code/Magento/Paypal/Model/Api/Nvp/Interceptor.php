<?php
namespace Magento\Paypal\Model\Api\Nvp;

/**
 * Interceptor class for @see \Magento\Paypal\Model\Api\Nvp
 */
class Interceptor extends \Magento\Paypal\Model\Api\Nvp implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Customer\Helper\Address $customerAddress, \Psr\Log\LoggerInterface $logger, \Magento\Payment\Model\Method\Logger $customLogger, \Magento\Framework\Locale\ResolverInterface $localeResolver, \Magento\Directory\Model\RegionFactory $regionFactory, \Magento\Directory\Model\CountryFactory $countryFactory, \Magento\Paypal\Model\Api\ProcessableExceptionFactory $processableExceptionFactory, \Magento\Framework\Exception\LocalizedExceptionFactory $frameworkExceptionFactory, \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($customerAddress, $logger, $customLogger, $localeResolver, $regionFactory, $countryFactory, $processableExceptionFactory, $frameworkExceptionFactory, $curlFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function call($methodName, array $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'call');
        return $pluginInfo ? $this->___callPlugins('call', func_get_args(), $pluginInfo) : parent::call($methodName, $request);
    }
}
