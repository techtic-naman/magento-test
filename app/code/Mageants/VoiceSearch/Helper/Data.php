<?php

/**
 * @category Mageants Voice Search
 * @package Mageants_VoiceSearch
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\VoiceSearch\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\Resolver as LocaleResolver;

class Data extends AbstractHelper
{
    /**
     * Voice search constructor
     *
     * @param Context $context
     * @param LocaleResolver $localeResolver
     */
    public function __construct(
        Context $context,
        LocaleResolver $localeResolver
    ) {
        $this->localeResolver = $localeResolver;
        parent::__construct($context);
    }

    /**
     * For get converted code
     */
    public function getConvertedLocaleCode(): string
    {
        return str_replace('_', '-', $this->getStoreLocale());
    }

    /**
     * For get store locate
     */
    public function getStoreLocale(): string
    {
        return $this->localeResolver->getLocale();
    }
}
