<?php
namespace Magento\Paypal\Model\Api\PayflowNvp;

/**
 * Interceptor class for @see \Magento\Paypal\Model\Api\PayflowNvp
 */
class Interceptor extends \Magento\Paypal\Model\Api\PayflowNvp implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Customer\Helper\Address $customerAddress, \Psr\Log\LoggerInterface $logger, \Magento\Payment\Model\Method\Logger $customLogger, \Magento\Framework\Locale\ResolverInterface $localeResolver, \Magento\Directory\Model\RegionFactory $regionFactory, \Magento\Directory\Model\CountryFactory $countryFactory, \Magento\Paypal\Model\Api\ProcessableExceptionFactory $processableExceptionFactory, \Magento\Framework\Exception\LocalizedExceptionFactory $frameworkExceptionFactory, \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory, \Magento\Framework\Math\Random $mathRandom, \Magento\Paypal\Model\Api\NvpFactory $nvpFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($customerAddress, $logger, $customLogger, $localeResolver, $regionFactory, $countryFactory, $processableExceptionFactory, $frameworkExceptionFactory, $curlFactory, $mathRandom, $nvpFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getApiEndpoint()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getApiEndpoint');
        return $pluginInfo ? $this->___callPlugins('getApiEndpoint', func_get_args(), $pluginInfo) : parent::getApiEndpoint();
    }

    /**
     * {@inheritdoc}
     */
    public function getPartner()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPartner');
        return $pluginInfo ? $this->___callPlugins('getPartner', func_get_args(), $pluginInfo) : parent::getPartner();
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getUser');
        return $pluginInfo ? $this->___callPlugins('getUser', func_get_args(), $pluginInfo) : parent::getUser();
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPassword');
        return $pluginInfo ? $this->___callPlugins('getPassword', func_get_args(), $pluginInfo) : parent::getPassword();
    }

    /**
     * {@inheritdoc}
     */
    public function getVendor()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getVendor');
        return $pluginInfo ? $this->___callPlugins('getVendor', func_get_args(), $pluginInfo) : parent::getVendor();
    }

    /**
     * {@inheritdoc}
     */
    public function getTender()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTender');
        return $pluginInfo ? $this->___callPlugins('getTender', func_get_args(), $pluginInfo) : parent::getTender();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaypalTransactionId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPaypalTransactionId');
        return $pluginInfo ? $this->___callPlugins('getPaypalTransactionId', func_get_args(), $pluginInfo) : parent::getPaypalTransactionId();
    }

    /**
     * {@inheritdoc}
     */
    public function callGetTransactionDetails()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'callGetTransactionDetails');
        return $pluginInfo ? $this->___callPlugins('callGetTransactionDetails', func_get_args(), $pluginInfo) : parent::callGetTransactionDetails();
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getVersion');
        return $pluginInfo ? $this->___callPlugins('getVersion', func_get_args(), $pluginInfo) : parent::getVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAgreementType()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBillingAgreementType');
        return $pluginInfo ? $this->___callPlugins('getBillingAgreementType', func_get_args(), $pluginInfo) : parent::getBillingAgreementType();
    }

    /**
     * {@inheritdoc}
     */
    public function callSetExpressCheckout()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'callSetExpressCheckout');
        return $pluginInfo ? $this->___callPlugins('callSetExpressCheckout', func_get_args(), $pluginInfo) : parent::callSetExpressCheckout();
    }

    /**
     * {@inheritdoc}
     */
    public function callGetExpressCheckoutDetails()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'callGetExpressCheckoutDetails');
        return $pluginInfo ? $this->___callPlugins('callGetExpressCheckoutDetails', func_get_args(), $pluginInfo) : parent::callGetExpressCheckoutDetails();
    }

    /**
     * {@inheritdoc}
     */
    public function callDoExpressCheckoutPayment()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'callDoExpressCheckoutPayment');
        return $pluginInfo ? $this->___callPlugins('callDoExpressCheckoutPayment', func_get_args(), $pluginInfo) : parent::callDoExpressCheckoutPayment();
    }

    /**
     * {@inheritdoc}
     */
    public function callDoDirectPayment()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'callDoDirectPayment');
        return $pluginInfo ? $this->___callPlugins('callDoDirectPayment', func_get_args(), $pluginInfo) : parent::callDoDirectPayment();
    }

    /**
     * {@inheritdoc}
     */
    public function callDoReferenceTransaction()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'callDoReferenceTransaction');
        return $pluginInfo ? $this->___callPlugins('callDoReferenceTransaction', func_get_args(), $pluginInfo) : parent::callDoReferenceTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function getIsFraudDetected()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIsFraudDetected');
        return $pluginInfo ? $this->___callPlugins('getIsFraudDetected', func_get_args(), $pluginInfo) : parent::getIsFraudDetected();
    }

    /**
     * {@inheritdoc}
     */
    public function callDoReauthorization()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'callDoReauthorization');
        return $pluginInfo ? $this->___callPlugins('callDoReauthorization', func_get_args(), $pluginInfo) : parent::callDoReauthorization();
    }

    /**
     * {@inheritdoc}
     */
    public function callDoCapture()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'callDoCapture');
        return $pluginInfo ? $this->___callPlugins('callDoCapture', func_get_args(), $pluginInfo) : parent::callDoCapture();
    }

    /**
     * {@inheritdoc}
     */
    public function callDoAuthorization()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'callDoAuthorization');
        return $pluginInfo ? $this->___callPlugins('callDoAuthorization', func_get_args(), $pluginInfo) : parent::callDoAuthorization();
    }

    /**
     * {@inheritdoc}
     */
    public function callDoVoid()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'callDoVoid');
        return $pluginInfo ? $this->___callPlugins('callDoVoid', func_get_args(), $pluginInfo) : parent::callDoVoid();
    }

    /**
     * {@inheritdoc}
     */
    public function callRefundTransaction()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'callRefundTransaction');
        return $pluginInfo ? $this->___callPlugins('callRefundTransaction', func_get_args(), $pluginInfo) : parent::callRefundTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function callManagePendingTransactionStatus()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'callManagePendingTransactionStatus');
        return $pluginInfo ? $this->___callPlugins('callManagePendingTransactionStatus', func_get_args(), $pluginInfo) : parent::callManagePendingTransactionStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function callGetPalDetails()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'callGetPalDetails');
        return $pluginInfo ? $this->___callPlugins('callGetPalDetails', func_get_args(), $pluginInfo) : parent::callGetPalDetails();
    }

    /**
     * {@inheritdoc}
     */
    public function callSetCustomerBillingAgreement()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'callSetCustomerBillingAgreement');
        return $pluginInfo ? $this->___callPlugins('callSetCustomerBillingAgreement', func_get_args(), $pluginInfo) : parent::callSetCustomerBillingAgreement();
    }

    /**
     * {@inheritdoc}
     */
    public function callGetBillingAgreementCustomerDetails()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'callGetBillingAgreementCustomerDetails');
        return $pluginInfo ? $this->___callPlugins('callGetBillingAgreementCustomerDetails', func_get_args(), $pluginInfo) : parent::callGetBillingAgreementCustomerDetails();
    }

    /**
     * {@inheritdoc}
     */
    public function callCreateBillingAgreement()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'callCreateBillingAgreement');
        return $pluginInfo ? $this->___callPlugins('callCreateBillingAgreement', func_get_args(), $pluginInfo) : parent::callCreateBillingAgreement();
    }

    /**
     * {@inheritdoc}
     */
    public function callUpdateBillingAgreement()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'callUpdateBillingAgreement');
        return $pluginInfo ? $this->___callPlugins('callUpdateBillingAgreement', func_get_args(), $pluginInfo) : parent::callUpdateBillingAgreement();
    }

    /**
     * {@inheritdoc}
     */
    public function prepareShippingOptionsCallbackAddress(array $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'prepareShippingOptionsCallbackAddress');
        return $pluginInfo ? $this->___callPlugins('prepareShippingOptionsCallbackAddress', func_get_args(), $pluginInfo) : parent::prepareShippingOptionsCallbackAddress($request);
    }

    /**
     * {@inheritdoc}
     */
    public function formatShippingOptionsCallback()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'formatShippingOptionsCallback');
        return $pluginInfo ? $this->___callPlugins('formatShippingOptionsCallback', func_get_args(), $pluginInfo) : parent::formatShippingOptionsCallback();
    }

    /**
     * {@inheritdoc}
     */
    public function call($methodName, array $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'call');
        return $pluginInfo ? $this->___callPlugins('call', func_get_args(), $pluginInfo) : parent::call($methodName, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function setRawResponseNeeded($flag)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setRawResponseNeeded');
        return $pluginInfo ? $this->___callPlugins('setRawResponseNeeded', func_get_args(), $pluginInfo) : parent::setRawResponseNeeded($flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getApiUsername()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getApiUsername');
        return $pluginInfo ? $this->___callPlugins('getApiUsername', func_get_args(), $pluginInfo) : parent::getApiUsername();
    }

    /**
     * {@inheritdoc}
     */
    public function getApiPassword()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getApiPassword');
        return $pluginInfo ? $this->___callPlugins('getApiPassword', func_get_args(), $pluginInfo) : parent::getApiPassword();
    }

    /**
     * {@inheritdoc}
     */
    public function getApiSignature()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getApiSignature');
        return $pluginInfo ? $this->___callPlugins('getApiSignature', func_get_args(), $pluginInfo) : parent::getApiSignature();
    }

    /**
     * {@inheritdoc}
     */
    public function getApiCertificate()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getApiCertificate');
        return $pluginInfo ? $this->___callPlugins('getApiCertificate', func_get_args(), $pluginInfo) : parent::getApiCertificate();
    }

    /**
     * {@inheritdoc}
     */
    public function getBuildNotationCode()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBuildNotationCode');
        return $pluginInfo ? $this->___callPlugins('getBuildNotationCode', func_get_args(), $pluginInfo) : parent::getBuildNotationCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getUseProxy()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getUseProxy');
        return $pluginInfo ? $this->___callPlugins('getUseProxy', func_get_args(), $pluginInfo) : parent::getUseProxy();
    }

    /**
     * {@inheritdoc}
     */
    public function getProxyHost()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProxyHost');
        return $pluginInfo ? $this->___callPlugins('getProxyHost', func_get_args(), $pluginInfo) : parent::getProxyHost();
    }

    /**
     * {@inheritdoc}
     */
    public function getProxyPort()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProxyPort');
        return $pluginInfo ? $this->___callPlugins('getProxyPort', func_get_args(), $pluginInfo) : parent::getProxyPort();
    }

    /**
     * {@inheritdoc}
     */
    public function getPageStyle()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPageStyle');
        return $pluginInfo ? $this->___callPlugins('getPageStyle', func_get_args(), $pluginInfo) : parent::getPageStyle();
    }

    /**
     * {@inheritdoc}
     */
    public function getHdrimg()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getHdrimg');
        return $pluginInfo ? $this->___callPlugins('getHdrimg', func_get_args(), $pluginInfo) : parent::getHdrimg();
    }

    /**
     * {@inheritdoc}
     */
    public function getHdrbordercolor()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getHdrbordercolor');
        return $pluginInfo ? $this->___callPlugins('getHdrbordercolor', func_get_args(), $pluginInfo) : parent::getHdrbordercolor();
    }

    /**
     * {@inheritdoc}
     */
    public function getHdrbackcolor()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getHdrbackcolor');
        return $pluginInfo ? $this->___callPlugins('getHdrbackcolor', func_get_args(), $pluginInfo) : parent::getHdrbackcolor();
    }

    /**
     * {@inheritdoc}
     */
    public function getPayflowcolor()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPayflowcolor');
        return $pluginInfo ? $this->___callPlugins('getPayflowcolor', func_get_args(), $pluginInfo) : parent::getPayflowcolor();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentAction()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPaymentAction');
        return $pluginInfo ? $this->___callPlugins('getPaymentAction', func_get_args(), $pluginInfo) : parent::getPaymentAction();
    }

    /**
     * {@inheritdoc}
     */
    public function getBusinessAccount()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBusinessAccount');
        return $pluginInfo ? $this->___callPlugins('getBusinessAccount', func_get_args(), $pluginInfo) : parent::getBusinessAccount();
    }

    /**
     * {@inheritdoc}
     */
    public function import($to, array $publicMap = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'import');
        return $pluginInfo ? $this->___callPlugins('import', func_get_args(), $pluginInfo) : parent::import($to, $publicMap);
    }

    /**
     * {@inheritdoc}
     */
    public function export($from, array $publicMap = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'export');
        return $pluginInfo ? $this->___callPlugins('export', func_get_args(), $pluginInfo) : parent::export($from, $publicMap);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaypalCart(\Magento\Paypal\Model\Cart $cart)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setPaypalCart');
        return $pluginInfo ? $this->___callPlugins('setPaypalCart', func_get_args(), $pluginInfo) : parent::setPaypalCart($cart);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigObject(\Magento\Paypal\Model\Config $config)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setConfigObject');
        return $pluginInfo ? $this->___callPlugins('setConfigObject', func_get_args(), $pluginInfo) : parent::setConfigObject($config);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getLocale');
        return $pluginInfo ? $this->___callPlugins('getLocale', func_get_args(), $pluginInfo) : parent::getLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function getFraudManagementFiltersEnabled()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFraudManagementFiltersEnabled');
        return $pluginInfo ? $this->___callPlugins('getFraudManagementFiltersEnabled', func_get_args(), $pluginInfo) : parent::getFraudManagementFiltersEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function getDebugFlag()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDebugFlag');
        return $pluginInfo ? $this->___callPlugins('getDebugFlag', func_get_args(), $pluginInfo) : parent::getDebugFlag();
    }

    /**
     * {@inheritdoc}
     */
    public function getUseCertAuthentication()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getUseCertAuthentication');
        return $pluginInfo ? $this->___callPlugins('getUseCertAuthentication', func_get_args(), $pluginInfo) : parent::getUseCertAuthentication();
    }

    /**
     * {@inheritdoc}
     */
    public function getDebugReplacePrivateDataKeys()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDebugReplacePrivateDataKeys');
        return $pluginInfo ? $this->___callPlugins('getDebugReplacePrivateDataKeys', func_get_args(), $pluginInfo) : parent::getDebugReplacePrivateDataKeys();
    }

    /**
     * {@inheritdoc}
     */
    public function addData(array $arr)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addData');
        return $pluginInfo ? $this->___callPlugins('addData', func_get_args(), $pluginInfo) : parent::addData($arr);
    }

    /**
     * {@inheritdoc}
     */
    public function setData($key, $value = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setData');
        return $pluginInfo ? $this->___callPlugins('setData', func_get_args(), $pluginInfo) : parent::setData($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function unsetData($key = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'unsetData');
        return $pluginInfo ? $this->___callPlugins('unsetData', func_get_args(), $pluginInfo) : parent::unsetData($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key = '', $index = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getData');
        return $pluginInfo ? $this->___callPlugins('getData', func_get_args(), $pluginInfo) : parent::getData($key, $index);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByPath($path)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDataByPath');
        return $pluginInfo ? $this->___callPlugins('getDataByPath', func_get_args(), $pluginInfo) : parent::getDataByPath($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByKey($key)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDataByKey');
        return $pluginInfo ? $this->___callPlugins('getDataByKey', func_get_args(), $pluginInfo) : parent::getDataByKey($key);
    }

    /**
     * {@inheritdoc}
     */
    public function setDataUsingMethod($key, $args = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setDataUsingMethod');
        return $pluginInfo ? $this->___callPlugins('setDataUsingMethod', func_get_args(), $pluginInfo) : parent::setDataUsingMethod($key, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataUsingMethod($key, $args = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDataUsingMethod');
        return $pluginInfo ? $this->___callPlugins('getDataUsingMethod', func_get_args(), $pluginInfo) : parent::getDataUsingMethod($key, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function hasData($key = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'hasData');
        return $pluginInfo ? $this->___callPlugins('hasData', func_get_args(), $pluginInfo) : parent::hasData($key);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(array $keys = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toArray');
        return $pluginInfo ? $this->___callPlugins('toArray', func_get_args(), $pluginInfo) : parent::toArray($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToArray(array $keys = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertToArray');
        return $pluginInfo ? $this->___callPlugins('convertToArray', func_get_args(), $pluginInfo) : parent::convertToArray($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function toXml(array $keys = [], $rootName = 'item', $addOpenTag = false, $addCdata = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toXml');
        return $pluginInfo ? $this->___callPlugins('toXml', func_get_args(), $pluginInfo) : parent::toXml($keys, $rootName, $addOpenTag, $addCdata);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToXml(array $arrAttributes = [], $rootName = 'item', $addOpenTag = false, $addCdata = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertToXml');
        return $pluginInfo ? $this->___callPlugins('convertToXml', func_get_args(), $pluginInfo) : parent::convertToXml($arrAttributes, $rootName, $addOpenTag, $addCdata);
    }

    /**
     * {@inheritdoc}
     */
    public function toJson(array $keys = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toJson');
        return $pluginInfo ? $this->___callPlugins('toJson', func_get_args(), $pluginInfo) : parent::toJson($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToJson(array $keys = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertToJson');
        return $pluginInfo ? $this->___callPlugins('convertToJson', func_get_args(), $pluginInfo) : parent::convertToJson($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function toString($format = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toString');
        return $pluginInfo ? $this->___callPlugins('toString', func_get_args(), $pluginInfo) : parent::toString($format);
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $args)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, '__call');
        return $pluginInfo ? $this->___callPlugins('__call', func_get_args(), $pluginInfo) : parent::__call($method, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isEmpty');
        return $pluginInfo ? $this->___callPlugins('isEmpty', func_get_args(), $pluginInfo) : parent::isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($keys = [], $valueSeparator = '=', $fieldSeparator = ' ', $quote = '"')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'serialize');
        return $pluginInfo ? $this->___callPlugins('serialize', func_get_args(), $pluginInfo) : parent::serialize($keys, $valueSeparator, $fieldSeparator, $quote);
    }

    /**
     * {@inheritdoc}
     */
    public function debug($data = null, &$objects = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'debug');
        return $pluginInfo ? $this->___callPlugins('debug', func_get_args(), $pluginInfo) : parent::debug($data, $objects);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetSet');
        return $pluginInfo ? $this->___callPlugins('offsetSet', func_get_args(), $pluginInfo) : parent::offsetSet($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetExists');
        return $pluginInfo ? $this->___callPlugins('offsetExists', func_get_args(), $pluginInfo) : parent::offsetExists($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetUnset');
        return $pluginInfo ? $this->___callPlugins('offsetUnset', func_get_args(), $pluginInfo) : parent::offsetUnset($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetGet');
        return $pluginInfo ? $this->___callPlugins('offsetGet', func_get_args(), $pluginInfo) : parent::offsetGet($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function formatPrice($price)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'formatPrice');
        return $pluginInfo ? $this->___callPlugins('formatPrice', func_get_args(), $pluginInfo) : parent::formatPrice($price);
    }
}
