<?php
namespace Magento\Quote\Api\Data;

/**
 * ExtensionInterface class for @see \Magento\Quote\Api\Data\TotalsInterface
 */
interface TotalsExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
    /**
     * @return string|null
     */
    public function getCouponLabel();

    /**
     * @param string $couponLabel
     * @return $this
     */
    public function setCouponLabel($couponLabel);

    /**
     * @return string|null
     */
    public function getMwEarnPointsData();

    /**
     * @param string $mwEarnPointsData
     * @return $this
     */
    public function setMwEarnPointsData($mwEarnPointsData);

    /**
     * @return float|null
     */
    public function getMwRwrdpointsAmnt();

    /**
     * @param float $mwRwrdpointsAmnt
     * @return $this
     */
    public function setMwRwrdpointsAmnt($mwRwrdpointsAmnt);

    /**
     * @return float|null
     */
    public function getMwRwrdpointsCurAmnt();

    /**
     * @param float $mwRwrdpointsCurAmnt
     * @return $this
     */
    public function setMwRwrdpointsCurAmnt($mwRwrdpointsCurAmnt);

    /**
     * @return float|null
     */
    public function getBaseRwrdpointsCurAmnt();

    /**
     * @param float $baseRwrdpointsCurAmnt
     * @return $this
     */
    public function setBaseRwrdpointsCurAmnt($baseRwrdpointsCurAmnt);
}
