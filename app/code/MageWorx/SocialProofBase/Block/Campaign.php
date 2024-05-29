<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Block;

use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Framework\View\Element\Template\Context;
use MageWorx\SocialProofBase\Api\CurrentEntityIdResolverInterface;
use MageWorx\SocialProofBase\Model\CampaignConfigReaderInterface;
use MageWorx\SocialProofBase\Model\CurrentEntityIdResolverFactory;
use MageWorx\SocialProofBase\Model\Source\Campaign\DisplayMode as DisplayModeOptions;
use MageWorx\SocialProofBase\Model\Source\Campaign\DisplayOn as DisplayOnOptions;

class Campaign extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $displayMode = DisplayModeOptions::HTML_TEXT;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var DisplayOnOptions
     */
    protected $displayOnOptions;

    /**
     * @var CurrentEntityIdResolverInterface
     */
    protected $currentEntityIdResolver;

    /**
     * Campaign config reader
     *
     * @var CampaignConfigReaderInterface
     */
    private $configReader;

    /**
     * Campaign constructor.
     *
     * @param Context $context
     * @param CampaignConfigReaderInterface $configReader
     * @param Serializer $serializer
     * @param DisplayOnOptions $displayOnOptions
     * @param CurrentEntityIdResolverFactory $currentEntityIdResolverFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        CampaignConfigReaderInterface $configReader,
        Serializer $serializer,
        DisplayOnOptions $displayOnOptions,
        CurrentEntityIdResolverFactory $currentEntityIdResolverFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->configReader            = $configReader;
        $this->serializer              = $serializer;
        $this->displayOnOptions        = $displayOnOptions;
        $this->currentEntityIdResolver = $currentEntityIdResolverFactory->create(
            $this->getRequest()->getFullActionName()
        );
    }

    /*
    * @param int|null $storeId
    * @return bool
    */
    public function isCanBeDisplayed($storeId = null): bool
    {
        return $this->configReader->isEnabled($storeId)
            && $this->getPageType()
            && $this->getCurrentAssociatedEntityId();
    }

    /**
     * @return string
     */
    public function getJsonConfig(): string
    {
        return $this->serializer->serialize($this->getConfig());
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'displayMode'        => $this->getDisplayMode(),
            'ajaxUrl'            => $this->getAjaxUrl(),
            'pageType'           => $this->getPageType(),
            'associatedEntityId' => $this->getCurrentAssociatedEntityId()
        ];
    }

    /**
     * @return string
     */
    protected function getAjaxUrl(): string
    {
        return $this->getUrl('mageworx_socialproofbase/ajax/getCampaignData');
    }

    /**
     * @return string
     */
    protected function getPageType(): string
    {
        $pageTypes = $this->displayOnOptions->getPageTypes();

        if (empty($pageTypes[$this->getRequest()->getFullActionName()])) {
            return '';
        }

        return $pageTypes[$this->getRequest()->getFullActionName()];
    }

    /**
     * @return int|null
     */
    protected function getCurrentAssociatedEntityId(): ?int
    {
        return $this->currentEntityIdResolver->getEntityId();
    }

    /**
     * @return string
     */
    protected function getDisplayMode(): string
    {
        return $this->displayMode;
    }
}
