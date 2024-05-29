<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Helpdesk
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Block\Adminhtml\Businesshours\Edit\Tab;

class HolidaysTemplate extends \Magento\Config\Block\System\Config\Form\Field
{
    public const TEMPLATE = 'Webkul_Helpdesk::businesshours/edit/tab/holidays.phtml';

    /**
     * @var \Webkul\Helpdesk\Model\BusinesshoursFactory
     */
    protected $_businesshoursFactory;

    /**
     * @var \Webkul\Helpdesk\Model\Source\Months
     */
    protected $_monthsModel;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @param \Magento\Backend\Block\Template\Context          $context
     * @param \Webkul\Helpdesk\Model\BusinesshoursFactory      $businesshoursFactory
     * @param \Webkul\Helpdesk\Model\Source\Months             $monthsModel
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Webkul\Helpdesk\Model\BusinesshoursFactory $businesshoursFactory,
        \Webkul\Helpdesk\Model\Source\Months $monthsModel,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        array $data = []
    ) {
        $this->_businesshoursFactory = $businesshoursFactory;
        $this->_monthsModel = $monthsModel;
        $this->serializer = $serializer;
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
     * Get Months
     *
     * @return array
     */
    public function getMonths()
    {
        return $this->_monthsModel->toOptionArray();
    }

    /**
     * Get serializer
     *
     * @return object
     */
    public function getSerializer()
    {
        return $this->serializer;
    }
}
