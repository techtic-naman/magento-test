<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Block\Adminhtml\Businesshours\Edit\Tab;

class HoursTemplate extends \Magento\Config\Block\System\Config\Form\Field
{
    public const TEMPLATE = 'Webkul_Helpdesk::businesshours/edit/tab/hours.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context          $context
     * @param \Webkul\Helpdesk\Model\BusinesshoursFactory      $businesshoursFactory
     * @param \Webkul\Helpdesk\Model\Source\Days               $daysModel
     * @param \Webkul\Helpdesk\Model\Source\TimeInterval       $timeIntervalModel
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Framework\Json\Helper\Data              $jsonHelper
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Webkul\Helpdesk\Model\BusinesshoursFactory $businesshoursFactory,
        \Webkul\Helpdesk\Model\Source\Days $daysModel,
        \Webkul\Helpdesk\Model\Source\TimeInterval $timeIntervalModel,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        array $data = []
    ) {
        $this->_businesshoursFactory = $businesshoursFactory;
        $this->_daysModel = $daysModel;
        $this->_timeIntervalModel = $timeIntervalModel;
        $this->serializer = $serializer;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }

    /**
     * Set template to itself.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::TEMPLATE);
        }
        return $this;
    }

    /**
     * Get current business hour
     *
     * @param int $id
     * @return object
     */
    public function getCurrentBusinessHours($id)
    {
        return $this->_businesshoursFactory->create()->load($id);
    }

    /**
     * Get optimal time html form
     *
     * @param string $timeSelected
     * @return $this
     */
    public function getTimeOptionHtml($timeSelected)
    {
        $time = $this->getTimeInterval();
        $html = "";
        foreach ($time as $value) {
            if ($timeSelected == $value) {
                $html = $html."<option selected='selected' value='".$value."'>".$value."</option>";
            } else {
                $html = $html."<option value='".$value."'>".$value."</option>";
            }
        }
        return $html;
    }

    /**
     * Get days
     *
     * @return array
     */
    public function getDays()
    {
        return $this->_daysModel->toOptionArray();
    }

    /**
     * Get time interval
     *
     * @return array
     */
    public function getTimeInterval()
    {
        return $this->_timeIntervalModel->toOptionArray();
    }

    /**
     * Serialize data
     *
     * @return object
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Get json data
     *
     * @return object
     */
    public function getJsonData()
    {
        return $this->jsonHelper;
    }
}
