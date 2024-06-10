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
namespace Webkul\Marketplace\Ui\Component;

class ExportButton extends \Magento\Ui\Component\AbstractComponent
{
    /**
     * Component name
     */
    public const NAME = 'exportButton';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @param ContextInterface $context
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\App\Request\Http $request
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\Request\Http $request,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->_urlBuilder = $urlBuilder;
        $this->_request = $request;
    }

    /**
     * Prepare seller id
     */
    public function prepare()
    {
        $sellerId = $this->_request->getParam('seller_id');
        $configData = $this->getData('config');
        if (isset($configData['options'])) {
            $configOptions = [];
            foreach ($configData['options'] as $configOption) {
                $configOption['url'] = $this->_urlBuilder->getUrl(
                    $configOption['url'],
                    ["seller_id"=>$sellerId]
                );
                $configOptions[] = $configOption;
            }
            $configData['options'] = $configOptions;
            $this->setData('config', $configData);
        }
        parent::prepare();
    }

    /**
     * Get component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return static::NAME;
    }
}
