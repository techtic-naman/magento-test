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
namespace Webkul\Marketplace\Ui\Component\Listing\Columns\Frontend;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Webkul\Marketplace\Helper\Data as MpHelper;
use Magento\Framework\Locale\Bundle\DataBundle;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;

class DateDetails extends Column
{
    /**
     * @var string
     */
    protected $_format = false;
    /**
     * @var MpHelper
     */
    protected $_helper;
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $_localeDate;
    /**
     * @var DateTimeFormatterInterface
     */
    protected $dateTimeFormatter;

    /**
     * Construct
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param MpHelper $helper
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Framework\Stdlib\DateTime\Timezone $localDate
     * @param DateTimeFormatterInterface $dateTimeFormatter
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        MpHelper $helper,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\Stdlib\DateTime\Timezone $localDate,
        DateTimeFormatterInterface $dateTimeFormatter,
        array $components = [],
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->localeResolver = $localeResolver;
        $this->_localeDate = $localDate;
        $this->dateTimeFormatter = $dateTimeFormatter;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $format = $this->_getFormat();
                $date = $this->_localeDate->date(new \DateTime($item['created_at']), null, false);
                $date = $this->dateTimeFormatter->formatObject($date, $format, $this->localeResolver->getLocale());
                $item['created_at'] = $date;
            }
        }

        return $dataSource;
    }

    /**
     * Retrieve date format
     *
     * @return string
     */
    protected function _getFormat()
    {
        if (!$this->_format) {
            $paramData = $this->_helper->getParams();
            $period = $paramData['period'] ?? '';
            $dataBundle = new DataBundle();
            $resourceBundle = $dataBundle->get($this->localeResolver->getLocale());
            $formats = $resourceBundle['calendar']['gregorian']['availableFormats'];
            switch ($period) {
                case 'month':
                    $format = $formats['yM'];
                    break;
                case 'year':
                    $format = $formats['y'];
                    break;
                default:
                    $format = $this->_localeDate->getDateFormat(\IntlDateFormatter::MEDIUM);
                    break;
            }
            $this->_format = $format;
        }
        return $this->_format;
    }
}
