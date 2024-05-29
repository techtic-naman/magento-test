<?php
namespace Magento\Quote\Api\Data;

/**
 * Extension class for @see \Magento\Quote\Api\Data\TotalsInterface
 */
class TotalsExtension extends \Magento\Framework\Api\AbstractSimpleObject implements TotalsExtensionInterface
{
    /**
     * @return string|null
     */
    public function getCouponLabel()
    {
        return $this->_get('coupon_label');
    }

    /**
     * @param string $couponLabel
     * @return $this
     */
    public function setCouponLabel($couponLabel)
    {
        $this->setData('coupon_label', $couponLabel);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMwEarnPointsData()
    {
        return $this->_get('mw_earn_points_data');
    }

    /**
     * @param string $mwEarnPointsData
     * @return $this
     */
    public function setMwEarnPointsData($mwEarnPointsData)
    {
        $this->setData('mw_earn_points_data', $mwEarnPointsData);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMwRwrdpointsAmnt()
    {
        return $this->_get('mw_rwrdpoints_amnt');
    }

    /**
     * @param float $mwRwrdpointsAmnt
     * @return $this
     */
    public function setMwRwrdpointsAmnt($mwRwrdpointsAmnt)
    {
        $this->setData('mw_rwrdpoints_amnt', $mwRwrdpointsAmnt);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMwRwrdpointsCurAmnt()
    {
        return $this->_get('mw_rwrdpoints_cur_amnt');
    }

    /**
     * @param float $mwRwrdpointsCurAmnt
     * @return $this
     */
    public function setMwRwrdpointsCurAmnt($mwRwrdpointsCurAmnt)
    {
        $this->setData('mw_rwrdpoints_cur_amnt', $mwRwrdpointsCurAmnt);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBaseRwrdpointsCurAmnt()
    {
        return $this->_get('base_rwrdpoints_cur_amnt');
    }

    /**
     * @param float $baseRwrdpointsCurAmnt
     * @return $this
     */
    public function setBaseRwrdpointsCurAmnt($baseRwrdpointsCurAmnt)
    {
        $this->setData('base_rwrdpoints_cur_amnt', $baseRwrdpointsCurAmnt);
        return $this;
    }
}
