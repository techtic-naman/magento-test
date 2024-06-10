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

namespace Webkul\Marketplace\Controller\Order\Shipment\Tracking;

class Delete extends \Webkul\Marketplace\Controller\Order
{
    /**
     * Add new tracking number action
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        try {
            $trackId = $this->getRequest()->getParam('id');
            if ($this->_initShipment()) {
                $track = $this->shipmentTrack->load($trackId);
                if ($track->getId()) {
                    $track->delete();
                    $response = [
                        'error' => false
                    ];
                } else {
                    $response = [
                        'error' => true,
                        'message' => __(
                            'We can\'t load track with retrieving identifier right now.%1'
                        )
                    ];
                }
            } else {
                $response = [
                    'error' => true,
                    'message' => __(
                        'We can\'t initialize shipment for adding tracking number.'
                    ),
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'error' => true,
                'message' => __('We can\'t delete tracking number.')
            ];
            $this->helper->logDataInLogger(
                "Controller_Order_Shipment_Tracking_Delete execute : ".$e->getMessage()
            );
        }
        if (is_array($response)) {
            $response = $this->helper->arrayToJson($response);
            $this->getResponse()->representJson($response);
        } else {
            $this->getResponse()->setBody($response);
        }
    }
}
