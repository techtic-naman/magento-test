<?php

namespace Meetanshi\Bulksms\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Twilio\Rest\ClientFactory as TwilioClientFactory;
use Meetanshi\Bulksms\Model\SmslogFactory;

class Data extends AbstractHelper
{

    const BULKSMS_ENABLED = 'bulksms/general/enabled';
    const APIPROVIDER = 'bulksms/apisetting/apiprovider';
    const SENDER = 'bulksms/apisetting/senderid';
    const MESSAGETYPE = 'bulksms/apisetting/messagetype';
    const APIURL = 'bulksms/apisetting/apiurl';
    const APIKEY = 'bulksms/apisetting/apikey';
    const SID = 'bulksms/apisetting/sid';
    const TOKEN = 'bulksms/apisetting/token';
    const FROMMOBILENUMBER = 'bulksms/apisetting/frommobilenumber';
    const DEVELOPER_NUMBER = 'bulksms/developer/adminmobile';

    protected $customerFactory;
    private $storeManagerInterface;
    private $url;
    private $directory;
    private $twilioClientFactory;
    private $smslogFactory;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        UrlInterface $url,
        DirectoryList $directoryList,
        TwilioClientFactory $twilioClientFactory,
        SmslogFactory $smslogFactory
    ) {

        $this->storeManagerInterface = $storeManager;
        $this->url = $url;
        $this->directory = $directoryList;
        $this->twilioClientFactory = $twilioClientFactory;
        $this->smslogFactory = $smslogFactory;
        parent::__construct($context);
    }


    public function getConfig($value)
    {
        return $this->scopeConfig->getValue($value, ScopeInterface::SCOPE_STORE);
    }

    public function isEnable()
    {
        return $this->getConfig(self::BULKSMS_ENABLED);
    }

    public function getApiprovider()
    {
        return $this->getConfig(self::APIPROVIDER);
    }

    public function getSid()
    {
        return $this->getConfig(self::SID);
    }

    public function getToken()
    {
        return $this->getConfig(self::TOKEN);
    }

    public function getAdminmobile()
    {
        return $this->getConfig(self::FROMMOBILENUMBER);
    }

    public function getStoreName()
    {
        return $this->getConfig('general/store_information/name');
    }

    public function getStoreUrl()
    {
        return $this->storeManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
    }

    public function sendDeveloperSms()
    {
        $adminMobile = $this->getConfig(self::DEVELOPER_NUMBER);
        if ($this->curlApi($adminMobile, 'Testing Api check')) {
            return 'SMS send';
        } else {
            return 'Error in SMS send';
        }
    }

    public function curlApi($mobilenumber, $message)
    {

        try {
            if ($this->isEnable()) {

                $mainMsg = $message;

                if ($this->getApiprovider() == "msg91") {
                    $msg = urlencode($message);
                    $apikey = $this->getConfig(self::APIKEY);
                    $senderid = $this->getConfig(self::SENDER);
                    $url = $this->getConfig(self::APIURL);
                    $msgtype = $this->getConfig(self::MESSAGETYPE);

                    $postUrl = $url . "?sender=" . $senderid . "&route=" . $msgtype . "&mobiles=" . $mobilenumber . "&authkey=" . $apikey . "&message=" . $msg . "";
                    $curl = curl_init();
                    curl_setopt_array(
                        $curl,
                        [
                            CURLOPT_URL => $postUrl,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "GET",
                            CURLOPT_SSL_VERIFYHOST => 0,
                            CURLOPT_SSL_VERIFYPEER => 0,
                        ]
                    );
                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    curl_close($curl);
                    if ($err) {
                        return "cURL Error #:" . $err;
                    } else {
                        if ($response) {

                            $smslog = $this->smslogFactory->create();
                            $smslog->setMsgSid($response)
                                ->setApiProvider('Msg 91')
                                ->setMobilenumber($mobilenumber)
                                ->setMsgStatus('Unknown')
                                ->setSmsText($mainMsg)
                                ->save();

                            return true;
                        }
                    }
                } elseif ($this->getApiprovider() == "textlocal") {
                    $url = $this->getConfig(self::APIURL);
                    $apiKey = urlencode($this->getConfig(self::APIKEY));
                    $numbers = [$mobilenumber];
                    $sender = urlencode($this->getConfig(self::SENDER));
                    $message = rawurlencode($message);
                    $numbers = implode(',', $numbers);
                    $data = ['apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message];

                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    curl_close($ch);

                    $responseArray = json_decode($response, true);

                    if ($responseArray['status'] == "success") {

                        $smslog = $this->smslogFactory->create();
                        $smslog->setMsgSid('--')
                            ->setApiProvider('Text Local')
                            ->setMobilenumber($mobilenumber)
                            ->setMsgStatus($responseArray['status'])
                            ->setSmsText($mainMsg)
                            ->save();

                        return true;
                    } else {

                        $smslog = $this->smslogFactory->create();
                        $smslog->setMsgSid('--')
                            ->setApiProvider('Text Local')
                            ->setMobilenumber($mobilenumber)
                            ->setMsgStatus($responseArray['status'])
                            ->setSmsText($mainMsg)
                            ->save();

                        return false;
                    }
                } elseif ($this->getApiprovider() == "twilio") {
                    $sid = $this->getConfig(self::SID);
                    $token = $this->getConfig(self::TOKEN);
                    $fromMobile = $this->getConfig(self::FROMMOBILENUMBER);
                    $twilio = $this->twilioClientFactory->create([
                        'username' => $sid,
                        'password' => $token
                    ]);

                    try {
                        $message = $twilio->messages
                            ->create(
                                '+' . $mobilenumber,
                                [
                                    "body" => $message,
                                    "from" => $fromMobile
                                ]
                            );

                        if ($message->sid) {

                            $smslog = $this->smslogFactory->create();
                            $smslog->setMsgSid($message->sid)
                                ->setApiProvider('Twilio')
                                ->setMobilenumber($mobilenumber)
                                ->setMsgStatus($message->status)
                                ->setSmsText($mainMsg)
                                ->save();
                            return true;

                        } else {

                            $smslog = $this->smslogFactory->create();
                            $smslog->setMsgSid('--')
                                ->setApiProvider('Twilio')
                                ->setMobilenumber($mobilenumber)
                                ->setMsgStatus('Failed')
                                ->setSmsText($mainMsg)
                                ->save();
                            return false;
                        }
                    } catch (\Exception $e) {

                        $smslog = $this->smslogFactory->create();
                        $smslog->setMsgSid('--')
                            ->setApiProvider('Twilio')
                            ->setMobilenumber($mobilenumber)
                            ->setMsgStatus('Failed')
                            ->setSmsText($mainMsg)
                            ->save();
                    }
                }
            }
        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
        }
    }
}
