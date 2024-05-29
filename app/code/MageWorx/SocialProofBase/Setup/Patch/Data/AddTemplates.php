<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SocialProofBase\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use MageWorx\SocialProofBase\Model\Campaign\Template as CampaignTemplate;
use MageWorx\SocialProofBase\Model\ResourceModel\Campaign;
use MageWorx\SocialProofBase\Model\Source\Campaign\DisplayMode as DisplayModeOptions;
use MageWorx\SocialProofBase\Model\Source\Campaign\EventType as EventTypeOptions;

class AddTemplates implements DataPatchInterface, PatchVersionInterface
{
    const POPUP_VIEWS_VARIANT_ZAFFRE           = 'popup-views-zaffre';
    const POPUP_VIEWS_VARIANT_ELECTRIC_CRIMSON = 'popup-views-electric-crimson';

    const HTML_TEXT_VIEWS_VARIANT_CERULEAN_BLUE = 'html-text-views-cerulean-blue';
    const HTML_TEXT_VIEWS_VARIANT_CANDY_APPLE   = 'html-text-views-candy-apple';

    const HTML_TEXT_RECENT_SALES_VARIANT_DEFAULT         = 'html-text-recent-sales-default';
    const HTML_TEXT_RECENT_SALES_VARIANT_CURRENT_PRODUCT = 'html-text-recent-sales-current-product';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $select  = $this->moduleDataSetup->getConnection()->select()->from(
            $this->moduleDataSetup->getTable(Campaign::CAMPAIGN_TEMPLATE_TABLE)
        );
        $query   = $this->moduleDataSetup->getConnection()->query($select);
        $results = $query->fetchAll();

        $identifiers = array_map(
            function ($result) {
                return $result[CampaignTemplate::IDENTIFIER];
            },
            $results
        );

        $templates = $this->getTemplates();

        foreach ($templates as $template) {

            if (!in_array($template[CampaignTemplate::IDENTIFIER], $identifiers)) {
                $this->moduleDataSetup->getConnection()->insert(
                    $this->moduleDataSetup->getTable(Campaign::CAMPAIGN_TEMPLATE_TABLE),
                    $template
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getTemplates(): array
    {
        return [
            $this->getPopupTemplateForRecentSales(1),
            $this->getPopupTemplateForRecentSales(2, 'mw-sp__container--bg-lily-white'),
            $this->getPopupTemplateForRecentSales(3, 'mw-sp__container--bg-nero'),
            $this->getPopupTemplateForRecentSales(4, 'mw-sp__container--layout-rounded'),
            $this->getPopupTemplateForRecentSales(
                5,
                'mw-sp__container--layout-rounded mw-sp__container--bg-lily-white'
            ),
            $this->getPopupTemplateForRecentSales(6, 'mw-sp__container--layout-rounded mw-sp__container--bg-nero'),
            $this->getPopupTemplateForRecentSales(7, 'mw-sp__container--layout-circular'),
            $this->getPopupTemplateForRecentSales(
                8,
                'mw-sp__container--layout-circular mw-sp__container--bg-lily-white'
            ),
            $this->getPopupTemplateForRecentSales(9, 'mw-sp__container--layout-circular mw-sp__container--bg-nero'),
            $this->getPopupTemplateForRecentSales(10, 'mw-sp__container--bg-sazerac'),
            $this->getPopupTemplateForRecentSales(11, 'mw-sp__container--layout-rounded mw-sp__container--bg-sazerac'),
            $this->getPopupTemplateForRecentSales(12, 'mw-sp__container--layout-circular mw-sp__container--bg-sazerac'),
            $this->getPopupTemplateForRecentSales(13, 'mw-sp__container--bg-aero-blue'),
            $this->getPopupTemplateForRecentSales(
                14,
                'mw-sp__container--layout-rounded mw-sp__container--bg-aero-blue'
            ),
            $this->getPopupTemplateForRecentSales(
                15,
                'mw-sp__container--layout-circular mw-sp__container--bg-aero-blue'
            ),
            $this->getPopupTemplateForRecentSales(16, 'mw-sp__container--bg-onahau'),
            $this->getPopupTemplateForRecentSales(17, 'mw-sp__container--layout-rounded mw-sp__container--bg-onahau'),
            $this->getPopupTemplateForRecentSales(18, 'mw-sp__container--layout-circular mw-sp__container--bg-onahau'),
            $this->getPopupTemplateForRecentSales(19, 'mw-sp__container--bg-tolopea'),
            $this->getPopupTemplateForRecentSales(20, 'mw-sp__container--layout-rounded mw-sp__container--bg-tolopea'),
            $this->getPopupTemplateForRecentSales(21, 'mw-sp__container--layout-circular mw-sp__container--bg-tolopea'),
            $this->getPopupTemplateForRecentSales(22, 'mw-sp__container--bg-oab-gradient'),
            $this->getPopupTemplateForRecentSales(
                23,
                'mw-sp__container--layout-rounded mw-sp__container--bg-oab-gradient'
            ),
            $this->getPopupTemplateForRecentSales(
                24,
                'mw-sp__container--layout-circular mw-sp__container--bg-oab-gradient'
            ),
            $this->getPopupTemplateForRecentSales(25, 'mw-sp__container--no-media'),
            $this->getPopupTemplateForRecentSales(26, 'mw-sp__container--layout-rounded mw-sp__container--no-media'),
            $this->getPopupTemplateForRecentSales(27, 'mw-sp__container--layout-circular mw-sp__container--no-media'),
            $this->getPopupTemplateForViews(
                1,
                self::POPUP_VIEWS_VARIANT_ZAFFRE,
                'mw-sp__container--icon-zaffre mw-sp__container--icon-fill'
            ),
            $this->getPopupTemplateForViews(
                2,
                self::POPUP_VIEWS_VARIANT_ZAFFRE,
                'mw-sp__container--icon-zaffre mw-sp__container--layout-rounded'
            ),
            $this->getPopupTemplateForViews(
                3,
                self::POPUP_VIEWS_VARIANT_ZAFFRE,
                'mw-sp__container--bg-zaffre mw-sp__container--layout-rounded'
            ),
            $this->getPopupTemplateForViews(
                4,
                self::POPUP_VIEWS_VARIANT_ELECTRIC_CRIMSON,
                'mw-sp__container--icon-electric-crimson mw-sp__container--icon-fill mw-sp__container--layout-rounded'
            ),
            $this->getPopupTemplateForViews(
                5,
                self::POPUP_VIEWS_VARIANT_ELECTRIC_CRIMSON,
                'mw-sp__container--icon-electric-crimson mw-sp__container--layout-rounded'
            ),
            $this->getPopupTemplateForViews(
                6,
                self::POPUP_VIEWS_VARIANT_ELECTRIC_CRIMSON,
                'mw-sp__container--bg-electric-crimson mw-sp__container--layout-rounded'
            ),
            $this->getHtmlTextTemplateForViews(
                1,
                self::HTML_TEXT_VIEWS_VARIANT_CERULEAN_BLUE,
                'mw-ss__highlighted-text--subdued'
            ),
            $this->getHtmlTextTemplateForViews(
                2,
                self::HTML_TEXT_VIEWS_VARIANT_CERULEAN_BLUE
            ),
            $this->getHtmlTextTemplateForViews(
                3,
                self::HTML_TEXT_VIEWS_VARIANT_CANDY_APPLE,
                'mw-ss__highlighted-text--subdued'
            ),
            $this->getHtmlTextTemplateForViews(
                4,
                self::HTML_TEXT_VIEWS_VARIANT_CANDY_APPLE
            ),
            $this->getHtmlTextTemplateForRecentSales(
                1,
                self::HTML_TEXT_RECENT_SALES_VARIANT_DEFAULT,
                'mw-ss__highlighted-text--subdued'
            ),
            $this->getHtmlTextTemplateForRecentSales(
                2,
                self::HTML_TEXT_RECENT_SALES_VARIANT_DEFAULT
            ),
            $this->getHtmlTextTemplateForRecentSales(
                3,
                self::HTML_TEXT_RECENT_SALES_VARIANT_CURRENT_PRODUCT,
                'mw-ss__highlighted-text--subdued'
            ),
            $this->getHtmlTextTemplateForRecentSales(
                4,
                self::HTML_TEXT_RECENT_SALES_VARIANT_CURRENT_PRODUCT
            )
        ];
    }

    /**
     * @param int $id
     * @param string $additionalСlass
     * @return array
     */
    protected function getPopupTemplateForRecentSales($id, $additionalСlass = ''): array
    {
        $identifier = DisplayModeOptions::POPUP . '-' . EventTypeOptions::RECENT_SALES . '-' . $id;

        return [
            CampaignTemplate::IDENTIFIER   => $identifier,
            CampaignTemplate::TITLE        => '#' . $id,
            CampaignTemplate::CONTENT      => $this->getPopupContentForRecentSales($additionalСlass),
            CampaignTemplate::DISPLAY_MODE => DisplayModeOptions::POPUP,
            CampaignTemplate::EVENT_TYPE   => EventTypeOptions::RECENT_SALES
        ];
    }

    /**
     * @param int $id
     * @param string $variant
     * @param string $additionalСlass
     * @return array
     */
    protected function getPopupTemplateForViews($id, $variant, $additionalСlass): array
    {
        $identifier = DisplayModeOptions::POPUP . '-' . EventTypeOptions::VIEWS . '-' . $id;

        return [
            CampaignTemplate::IDENTIFIER   => $identifier,
            CampaignTemplate::TITLE        => '#' . $id,
            CampaignTemplate::CONTENT      => $this->getPopupContentForViews($variant, $additionalСlass),
            CampaignTemplate::DISPLAY_MODE => DisplayModeOptions::POPUP,
            CampaignTemplate::EVENT_TYPE   => EventTypeOptions::VIEWS
        ];
    }

    /**
     * @param int $id
     * @param string $variant
     * @param string $additionalСlass
     * @return array
     */
    protected function getHtmlTextTemplateForViews($id, $variant, $additionalСlass = ''): array
    {
        $identifier = DisplayModeOptions::HTML_TEXT . '-' . EventTypeOptions::VIEWS . '-' . $id;

        return [
            CampaignTemplate::IDENTIFIER   => $identifier,
            CampaignTemplate::TITLE        => '#' . $id,
            CampaignTemplate::CONTENT      => $this->getHtmlTextContentForViews($variant, $additionalСlass),
            CampaignTemplate::DISPLAY_MODE => DisplayModeOptions::HTML_TEXT,
            CampaignTemplate::EVENT_TYPE   => EventTypeOptions::VIEWS
        ];
    }

    /**
     * @param int $id
     * @param string $variant
     * @param string $additionalСlass
     * @return array
     */
    protected function getHtmlTextTemplateForRecentSales($id, $variant, $additionalСlass = ''): array
    {
        $identifier = DisplayModeOptions::HTML_TEXT . '-' . EventTypeOptions::RECENT_SALES . '-' . $id;

        return [
            CampaignTemplate::IDENTIFIER   => $identifier,
            CampaignTemplate::TITLE        => '#' . $id,
            CampaignTemplate::CONTENT      => $this->getHtmlTextContentForRecentSales($variant, $additionalСlass),
            CampaignTemplate::DISPLAY_MODE => DisplayModeOptions::HTML_TEXT,
            CampaignTemplate::EVENT_TYPE   => EventTypeOptions::RECENT_SALES
        ];
    }

    /**
     * @param string $additionalСlass
     * @return string
     */
    protected function getPopupContentForRecentSales($additionalСlass = ''): string
    {
        return <<<EOD
<div class="mw-sp__container $additionalСlass" role="document">
    <button class="mw-sp__close" type="button" aria-label="Close">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
            <path d="M11 1L1 11M1 1L11 11" stroke="currentColor" stroke-width="2"></path>
        </svg>
    </button>
    <div class="mw-sp__media">
        <div class="mw-sp__media__aspect-ratio">
            <img class="mw-sp__media__image" src="[product.image]" alt="[product.name]"/>
        </div>
    </div>
    <div class="mw-sp__content">
        <span class="mw-sp__message">[customer.name] in [customer.location] purchased</span>
        <span class="mw-sp__product">
            <svg width="10" height="13" viewBox="0 0 10 13" fill="none" class="mw-sp__product__icon">
                <path d="M3.99677 0C4.41117 0.816754 5.19223 2.4375 2.80135 4.875C1.85247 5.84238 1.37496 6.98156 1.23132 8.10122C0.708745 8.04764 0.244484 7.87398 0.0119817 7.3125C-0.177793 10.6498 1.93462 12.334 2.95809 12.8818C3.02473 12.9256 3.09232 12.9651 3.16069 13C3.15961 12.9937 3.15855 12.9874 3.1575 12.9811C3.172 12.9877 3.18611 12.994 3.19982 13C3.18512 12.9917 3.17058 12.9828 3.15621 12.9733C2.7782 10.6743 4.46387 9.18691 5.36452 8.7801C5.22033 9.8916 5.62066 10.4536 6.00974 10.9998C6.38307 11.5239 6.74605 12.0335 6.6077 13C8.58549 12.5236 10.9882 9.23102 9.5755 4.875C9.5755 5.6875 8.38006 6.5 7.98158 6.5C7.98158 4.875 7.34207 1.25236 3.99677 0Z"
                      fill="#FF003D"></path>
            </svg>
            <a class="mw-sp__product__link" href="[product.url]">[product.name]</a>
        </span>
        <div class="mw-sp__rating">
            <span class="mw-sp__rating__text">[product.rating_stars] stars of 5</span>
            <div class="mw-sp__stars" aria-hidden="true">
                <div class="mw-sp__rate" style="width: [product.rating_summary]%"></div>
            </div>
            <span class="mw-sp__time">[last_purchase]</span>
        </div>
    </div>
</div>
EOD;
    }

    /**
     * @param string $additionalСlass
     * @param string $variant
     * @return string
     */
    protected function getPopupContentForViews($variant, $additionalСlass): string
    {
        if ($variant === self::POPUP_VIEWS_VARIANT_ZAFFRE) {
            return $this->getZaffrePopupContentForViews($additionalСlass);
        }

        if ($variant === self::POPUP_VIEWS_VARIANT_ELECTRIC_CRIMSON) {
            return $this->getElectricCrimsonPopupContentForViews($additionalСlass);
        }

        return '';
    }

    /**
     * @param string $additionalСlass
     * @param string $variant
     * @return string
     */
    protected function getHtmlTextContentForViews($variant, $additionalСlass = ''): string
    {
        if ($variant === self::HTML_TEXT_VIEWS_VARIANT_CERULEAN_BLUE) {
            return $this->getCeruleanBlueHtmlTextContentForViews($additionalСlass);
        }

        if ($variant === self::HTML_TEXT_VIEWS_VARIANT_CANDY_APPLE) {
            return $this->getCandyAppleHtmlTextContentForViews($additionalСlass);
        }

        return '';
    }

    /**
     * @param string $additionalСlass
     * @param string $variant
     * @return string
     */
    protected function getHtmlTextContentForRecentSales($variant, $additionalСlass = ''): string
    {
        if ($variant === self::HTML_TEXT_RECENT_SALES_VARIANT_DEFAULT) {
            return $this->getDefaultHtmlTextContentForRecentSales($additionalСlass);
        }

        if ($variant === self::HTML_TEXT_RECENT_SALES_VARIANT_CURRENT_PRODUCT) {
            return $this->getCurrentProductHtmlTextContentForRecentSales($additionalСlass);
        }

        return '';
    }

    /**
     * @param string $additionalСlass
     * @return string
     */
    protected function getZaffrePopupContentForViews($additionalСlass): string
    {
        return <<<EOD
<div class="mw-sp__container mw-sp__container--layout-info $additionalСlass" role="document">
    <button class="mw-sp__close" type="button" aria-label="Close">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
            <path d="M11 1L1 11M1 1L11 11" stroke="currentColor" stroke-width="2"></path>
        </svg>
    </button>
    <div class="mw-sp__media">
        <div class="mw-sp__media__aspect-ratio">
            <svg width="41" height="25" viewBox="0 0 41 25" fill="none" class="mw-sp__media__image" aria-hidden="true">
                <path d="M0 12.5C1.92698 8.73796 4.86152 5.57943 8.47931 3.37346C12.0971 1.1675 16.2572 0 20.5 0C24.7428 0 28.9029 1.1675 32.5207 3.37346C36.1385 5.57943 39.073 8.73796 41 12.5C39.073 16.262 36.1385 19.4206 32.5207 21.6265C28.9029 23.8325 24.7428 25 20.5 25C16.2572 25 12.0971 23.8325 8.47931 21.6265C4.86152 19.4206 1.92698 16.262 0 12.5ZM20.5 20.8278C22.7192 20.8278 24.8474 19.9504 26.4166 18.3886C27.9858 16.8269 28.8673 14.7087 28.8673 12.5C28.8673 10.2913 27.9858 8.17313 26.4166 6.61137C24.8474 5.04961 22.7192 4.17222 20.5 4.17222C18.2808 4.17222 16.1526 5.04961 14.5834 6.61137C13.0142 8.17313 12.1327 10.2913 12.1327 12.5C12.1327 14.7087 13.0142 16.8269 14.5834 18.3886C16.1526 19.9504 18.2808 20.8278 20.5 20.8278ZM20.5 16.6639C19.3904 16.6639 18.3263 16.2252 17.5417 15.4443C16.7571 14.6634 16.3163 13.6043 16.3163 12.5C16.3163 11.3957 16.7571 10.3366 17.5417 9.55569C18.3263 8.77481 19.3904 8.33611 20.5 8.33611C21.6096 8.33611 22.6737 8.77481 23.4583 9.55569C24.2429 10.3366 24.6837 11.3957 24.6837 12.5C24.6837 13.6043 24.2429 14.6634 23.4583 15.4443C22.6737 16.2252 21.6096 16.6639 20.5 16.6639Z"
                      fill="currentColor"></path>
            </svg>
        </div>
    </div>
    <div class="mw-sp__content">
        <span class="mw-sp__message"><strong>[count_customers] </strong>viewed this product <strong>in the last [period]</strong></span>
    </div>
</div>
EOD;
    }

    /**
     * @param string $additionalСlass
     * @return string
     */
    protected function getElectricCrimsonPopupContentForViews($additionalСlass): string
    {
        return <<<EOD
<div class="mw-sp__container mw-sp__container--layout-info $additionalСlass" role="document">
    <button class="mw-sp__close" type="button" aria-label="Close">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
            <path d="M11 1L1 11M1 1L11 11" stroke="currentColor" stroke-width="2"></path>
        </svg>
    </button>
    <div class="mw-sp__media">
        <div class="mw-sp__media__aspect-ratio">
            <svg width="35" height="44" viewBox="0 0 35 44" class="mw-sp__media__image" aria-hidden="true">
                <path d="M13.9887,0 C15.4391,2.7644 18.1728,8.25 9.80472,16.5 C6.48365,19.7742 4.81235,23.6299 4.30963,27.4195 C2.48061,27.2382 0.855694,26.6504 0.0419359,24.75 C-0.622277,36.0454 6.77117,41.746 10.3533,43.6 C10.5866,43.7481 10.8231,43.8817 11.0624,44 C11.0586,43.9786 11.0549,43.9573 11.0513,43.9359 C11.102,43.9584 11.1514,43.9797 11.1994,44 C11.1479,43.972 11.097,43.9418 11.0467,43.9095 C9.72371,36.1285 15.6236,31.0941 18.7758,29.7173 C18.2712,33.4793 19.6723,35.3814 21.0341,37.2301 C22.3408,39.004 23.6112,40.7287 23.127,44 C30.0492,42.3874 38.4587,31.2435 33.5143,16.5 C33.5143,19.25 29.3302,22 27.9355,22 C27.9355,16.5 25.6972,4.23874 13.9887,0 Z"
                      fill="currentColor" fill-rule="evenodd"></path>
            </svg>
        </div>
    </div>
    <div class="mw-sp__content">
        <span class="mw-sp__message"><strong>[count_customers] </strong>viewed this product <strong>in the last [period]</strong></span>
    </div>
</div>
EOD;
    }

    /**
     * @param string $additionalСlass
     * @return string
     */
    protected function getCeruleanBlueHtmlTextContentForViews($additionalСlass = ''): string
    {
        return <<<EOD
<div class="mw-ss__container mw-ss__container--style-cerulean-blue">
    <div class="mw-ss__media">
        <svg width="20" height="20" viewbox="0 0 41 25" fill="none" class="mw-ss__media-image" aria-hidden="true">
            <path d="M0 12.5C1.92698 8.73796 4.86152 5.57943 8.47931 3.37346C12.0971 1.1675 16.2572 0 20.5 0C24.7428 0 28.9029 1.1675 32.5207 3.37346C36.1385 5.57943 39.073 8.73796 41 12.5C39.073 16.262 36.1385 19.4206 32.5207 21.6265C28.9029 23.8325 24.7428 25 20.5 25C16.2572 25 12.0971 23.8325 8.47931 21.6265C4.86152 19.4206 1.92698 16.262 0 12.5ZM20.5 20.8278C22.7192 20.8278 24.8474 19.9504 26.4166 18.3886C27.9858 16.8269 28.8673 14.7087 28.8673 12.5C28.8673 10.2913 27.9858 8.17313 26.4166 6.61137C24.8474 5.04961 22.7192 4.17222 20.5 4.17222C18.2808 4.17222 16.1526 5.04961 14.5834 6.61137C13.0142 8.17313 12.1327 10.2913 12.1327 12.5C12.1327 14.7087 13.0142 16.8269 14.5834 18.3886C16.1526 19.9504 18.2808 20.8278 20.5 20.8278ZM20.5 16.6639C19.3904 16.6639 18.3263 16.2252 17.5417 15.4443C16.7571 14.6634 16.3163 13.6043 16.3163 12.5C16.3163 11.3957 16.7571 10.3366 17.5417 9.55569C18.3263 8.77481 19.3904 8.33611 20.5 8.33611C21.6096 8.33611 22.6737 8.77481 23.4583 9.55569C24.2429 10.3366 24.6837 11.3957 24.6837 12.5C24.6837 13.6043 24.2429 14.6634 23.4583 15.4443C22.6737 16.2252 21.6096 16.6639 20.5 16.6639Z"
            fill="currentColor"></path>
        </svg>
    </div>
    <div class="mw-ss__content"><span class="mw-ss__highlighted-text $additionalСlass">[count_customers]</span>&nbsp;viewed this product in the last [period]</div>
</div>
EOD;
    }

    /**
     * @param string $additionalСlass
     * @return string
     */
    protected function getCandyAppleHtmlTextContentForViews($additionalСlass = ''): string
    {
        return <<<EOD
<div class="mw-ss__container mw-ss__container--style-candy-apple">
    <div class="mw-ss__media">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" viewBox="0 0 12 16">
            <polygon fill="currentColor" fill-rule="evenodd" points="2.777 8.533 0 8.533 3.273 0 9.818 0 8.182 4.267 12 4.267 0 16"></polygon>
        </svg>
    </div>
    <div class="mw-ss__content">This product was&nbsp;<span class="mw-ss__highlighted-text $additionalСlass">viewed [count_times]</span>&nbsp;for last [period]</div>
</div>
EOD;
    }

    /**
     * @param string $additionalСlass
     * @return string
     */
    protected function getDefaultHtmlTextContentForRecentSales($additionalСlass = ''): string
    {
        return <<<EOD
<div class="mw-ss__container mw-ss__container--style-black">
    <div class="mw-ss__media">
        <svg width="17" height="16" viewbox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8.5 14.5C8.5 15.3284 7.82843 16 7 16C6.17157 16 5.5 15.3284 5.5 14.5C5.5 13.6716 6.17157 13 7 13C7.82843 13 8.5 13.6716 8.5 14.5Z" fill="currentColor"></path>
            <path d="M13.5 14.5C13.5 15.3284 12.8284 16 12 16C11.1716 16 10.5 15.3284 10.5 14.5C10.5 13.6716 11.1716 13 12 13C12.8284 13 13.5 13.6716 13.5 14.5Z" fill="currentColor"></path>
            <path d="M0 1H2.8125L4.0875 5M4.0875 5L6 11H13L15 5H4.0875Z" stroke="currentColor" stroke-width="2"></path>
        </svg>
    </div>
    <div class="mw-ss__content">[customer.name] from [customer.city] purchased&nbsp;<a class="mw-sp__product__link" href="[product.url]">[product.name]</a>&nbsp;<span class="mw-ss__highlighted-text $additionalСlass">[last_purchase]</span>
    </div>
</div>
EOD;
    }

    /**
     * @param string $additionalСlass
     * @return string
     */
    protected function getCurrentProductHtmlTextContentForRecentSales($additionalСlass = ''): string
    {
        return <<<EOD
<div class="mw-ss__container mw-ss__container--style-black">
    <div class="mw-ss__media">
        <svg width="17" height="16" viewbox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8.5 14.5C8.5 15.3284 7.82843 16 7 16C6.17157 16 5.5 15.3284 5.5 14.5C5.5 13.6716 6.17157 13 7 13C7.82843 13 8.5 13.6716 8.5 14.5Z" fill="currentColor"></path>
            <path d="M13.5 14.5C13.5 15.3284 12.8284 16 12 16C11.1716 16 10.5 15.3284 10.5 14.5C10.5 13.6716 11.1716 13 12 13C12.8284 13 13.5 13.6716 13.5 14.5Z" fill="currentColor"></path>
            <path d="M0 1H2.8125L4.0875 5M4.0875 5L6 11H13L15 5H4.0875Z" stroke="currentColor" stroke-width="2"></path>
        </svg>
    </div>
    <div class="mw-ss__content">This product was purchased&nbsp;<span class="mw-ss__highlighted-text $additionalСlass">[last_purchase]</span>
    </div>
</div>
EOD;
    }
}
