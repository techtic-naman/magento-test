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
namespace Webkul\Marketplace\Model;

use Webkul\Marketplace\Api\Data\WysiwygImageInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class WysiwygImage extends AbstractModel implements WysiwygImageInterface, IdentityInterface
{
    public const CACHE_TAG = 'marketplace_wysiwygimage';
    /**
     * @var string
     */
    protected $_cacheTag = 'marketplace_wysiwygimage';
    /**
     * @var string
     */
    protected $_eventPrefix = 'marketplace_wysiwygimage';
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\Marketplace\Model\ResourceModel\WysiwygImage::class
        );
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getEntityId()];
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Set Id
     *
     * @param int $id
     * @return WysiwygImageInterface
     */
    public function setEntityId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Get Seller Id
     *
     * @return int|null
     */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * Set Seller Id
     *
     * @param int $sellerId
     * @return WysiwygImageInterface
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * Get Url
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->getData(self::URL);
    }

    /**
     * Set Url
     *
     * @param string $url
     * @return WysiwygImageInterface
     */
    public function setUrl($url)
    {
        return $this->setData(self::URL, $url);
    }

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set Name
     *
     * @param string $name
     * @return WysiwygImageInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get Type
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * Set Type
     *
     * @param string $type
     * @return WysiwygImageInterface
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get File
     *
     * @return string|null
     */
    public function getFile()
    {
        return $this->getData(self::FILE);
    }

    /**
     * Set File
     *
     * @param string $file
     * @return WysiwygImageInterface
     */
    public function setFile($file)
    {
        return $this->setData(self::FILE, $file);
    }
}
