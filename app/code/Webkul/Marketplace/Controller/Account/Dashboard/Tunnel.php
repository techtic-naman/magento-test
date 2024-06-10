<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Marketplace\Controller\Account\Dashboard;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result;
use Magento\Framework\Encryption\Helper\Security;
use Webkul\Marketplace\Helper\Dashboard\Data as MpDashboardHelper;
use Webkul\Marketplace\Helper\Data as MpHelper;
use Laminas\Http\Request;

/**
 * Webkul Marketplace Account Dashboard Tunnel Controller.
 */
class Tunnel extends Action
{
    /**
     * @var Result\RawFactory
     */
    protected $_resultRawFactory;

    /**
     * @var MpDashboardHelper
     */
    protected $mpDashboardHelper;

    /**
     * @var \Magento\Framework\HTTP\LaminasClient
     */
    protected $httpZendClient;

    /**
     * @var MpHelper
     */
    protected $mpHelper;
    
    /**
     * @var \Magento\Framework\Url\DecoderInterface
     */
    protected $decoderInterface;

    /**
     * Construct
     *
     * @param Context $context
     * @param Result\RawFactory $resultRawFactory
     * @param MpDashboardHelper $mpDashboardHelper
     * @param \Magento\Framework\HTTP\LaminasClient $httpZendClient
     * @param MpHelper $mpHelper
     * @param \Magento\Framework\Url\DecoderInterface $decoderInterface
     */
    public function __construct(
        Context $context,
        Result\RawFactory $resultRawFactory,
        MpDashboardHelper $mpDashboardHelper,
        \Magento\Framework\HTTP\LaminasClient $httpZendClient,
        MpHelper $mpHelper,
        \Magento\Framework\Url\DecoderInterface $decoderInterface
    ) {
        parent::__construct($context);
        $this->_resultRawFactory = $resultRawFactory;
        $this->mpDashboardHelper = $mpDashboardHelper;
        $this->httpZendClient = $httpZendClient;
        $this->mpHelper = $mpHelper;
        $this->decoderInterface = $decoderInterface;
    }

    /**
     * Request to get seller statistics graph image to the web-service.
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        
        $errorMessage = __('invalid request');
        $httpCode = 400;
        $getEncodedParamData = $this->_request->getParam('param_data');
        $getEncryptedHashData = $this->_request->getParam('encrypted_data');
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->_resultRawFactory->create();
        if ($getEncodedParamData && $getEncryptedHashData) {
            /** @var $helper \Webkul\Marketplace\Helper\Dashboard\Data */
            $helper = $this->mpDashboardHelper;
            $newEncryptedHashData = $helper->getChartEncryptedHashData($getEncodedParamData);
            if (Security::compareStrings($newEncryptedHashData, $getEncryptedHashData)) {
                $params = null;
                $paramsJson = $this->decoderInterface->decode(urldecode($getEncodedParamData));
      
                if ($paramsJson) {
                    $params = $this->mpHelper->jsonToArray($paramsJson);
                }
                if ($params) {
                    try {
                        /** @var $httpZendClient \Magento\Framework\HTTP\ZendClient */
                        $httpZendClient = $this->httpZendClient;
                        
                        $httpZendClient->setUri(
                            \Webkul\Marketplace\Block\Account\Dashboard\Diagrams::GOOGLE_API_URL
                        )->setParameterGet(
                            $params
                        )
                        ->setMethod(
                            Request::METHOD_GET
                        );
                        
                        $response = $httpZendClient->send();
                        $responseHeaders = $response->getHeaders()->toArray();
                        
                        $resultRaw->setHeader('Content-type', $responseHeaders['Content-Type'])
                            ->setContents($response->getBody());

                        return $resultRaw;
                    } catch (\Exception $e) {
                        $this->mpHelper->logDataInLogger(
                            "controller_account_dashboard_tunnel execute : ".$e->getMessage()
                        );
                        $errorMessage = __('see error log for details');
                        $httpCode = 503;
                    }
                }
            }
        }
        $resultRaw->setHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->setHttpResponseCode($httpCode)
            ->setContents(__('Service unavailable: %1', $errorMessage));

        return $resultRaw;
    }
}
